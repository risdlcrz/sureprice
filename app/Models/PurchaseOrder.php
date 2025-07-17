<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'po_number',
        'purchase_request_id',
        'contract_id',
        'supplier_id',
        'ordered_by',
        'approved_by',
        'status',
        'order_date',
        'total_amount',
        'payment_terms',
        'delivery_terms',
        'delivery_date',
        'shipping_terms',
        'notes',
        'client_payment_validated',
        'client_payment_validated_at',
        'client_payment_validated_by',
        'supplier_payment_validated',
        'supplier_payment_validated_at',
        'supplier_payment_validated_by',
        'delivery_confirmed',
        'delivery_confirmed_at',
        'delivery_confirmed_by',
        'warehouse_id',
        'due_date',
        'penalty_rate',
        'penalty_type',
        'penalty_accrued',
        'last_penalty_calculation',
        'shipped_at',
        'delivered_at',
        'shipping_note',
        'shipping_status',
        'tracking_number',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'client_payment_validated' => 'boolean',
        'supplier_payment_validated' => 'boolean',
        'delivery_confirmed' => 'boolean',
        'client_payment_validated_at' => 'datetime',
        'supplier_payment_validated_at' => 'datetime',
        'delivery_confirmed_at' => 'datetime',
        'due_date' => 'date',
        'penalty_rate' => 'decimal:2',
        'penalty_accrued' => 'decimal:2',
        'last_penalty_calculation' => 'date',
        'shipping_status' => 'string',
        'tracking_number' => 'string',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\PurchaseOrderPayment::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'in_progress' => 'info',
            'completed' => 'success',
            'partially_delivered' => 'warning'
        ][$this->status] ?? 'secondary';
    }

    public function calculateEstimatedCost()
    {
        // Sum of all item quantities * unit price
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function calculateActualCost()
    {
        // Sum of all item quantities * unit price (same as estimated for now)
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function validateClientPayment()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('finance')) {
            throw new \Exception('Only administrators or finance can validate client payments.');
        }

        $this->client_payment_validated = true;
        $this->client_payment_validated_at = now();
        $this->client_payment_validated_by = auth()->id();
        $this->save();

        $this->checkPaymentValidation();
    }

    public function validateSupplierPayment()
    {
        if (!auth()->user()->hasRole('supplier')) {
            throw new \Exception('Only suppliers can validate payments.');
        }

        $this->supplier_payment_validated = true;
        $this->supplier_payment_validated_at = now();
        $this->supplier_payment_validated_by = auth()->id();
        $this->save();

        $this->checkPaymentValidation();
    }

    protected function checkPaymentValidation()
    {
        if ($this->client_payment_validated && $this->supplier_payment_validated) {
            $this->status = 'payment_validated';
            $this->save();
        }
    }

    public function confirmDelivery()
    {
        if (!$this->client_payment_validated || !$this->supplier_payment_validated) {
            throw new \Exception('Cannot confirm delivery until payment is validated by both parties.');
        }

        DB::beginTransaction();
        try {
            $this->delivery_confirmed = true;
            $this->delivery_confirmed_at = now();
            $this->delivery_confirmed_by = auth()->id();
            $this->status = 'completed';
            $this->save();

            // Update warehouse stock
            foreach ($this->items as $item) {
                $warehouseStock = WarehouseStock::firstOrCreate(
                    [
                        'warehouse_id' => $this->warehouse_id,
                        'material_id' => $item->material_id
                    ],
                    ['quantity' => 0]
                );

                $warehouseStock->increment('quantity', $item->quantity);

                // Log stock movement
                StockMovement::create([
                    'warehouse_id' => $this->warehouse_id,
                    'material_id' => $item->material_id,
                    'quantity' => $item->quantity,
                    'type' => 'in',
                    'reference_type' => 'purchase_order',
                    'reference_id' => $this->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function isPaymentValidated(): bool
    {
        return $this->client_payment_validated && $this->supplier_payment_validated;
    }

    /**
     * Calculate and update penalty if overdue.
     *
     * @return float The updated penalty accrued.
     */
    public function calculateAndUpdatePenalty()
    {
        if (!$this->due_date || $this->status === 'completed') {
            return $this->penalty_accrued;
        }
        $now = now();
        $lastCalc = $this->last_penalty_calculation ?? $this->due_date;
        $overdueDays = $now->diffInDays($this->due_date, false);
        if ($overdueDays >= 0) {
            // Not overdue
            return $this->penalty_accrued;
        }
        $daysLate = $now->diffInDays($lastCalc);
        if ($daysLate <= 0) {
            return $this->penalty_accrued;
        }
        $baseAmount = $this->total_amount;
        $penalty = 0;
        switch ($this->penalty_type) {
            case 'fixed':
                // Fixed penalty per day late
                $penalty = $this->penalty_rate * $daysLate;
                break;
            case 'compound':
                // Compound penalty (like interest)
                $penalty = $this->penalty_accrued;
                for ($i = 0; $i < $daysLate; $i++) {
                    $penalty += ($baseAmount + $penalty) * ($this->penalty_rate / 100 / 30); // monthly rate, daily compounding
                }
                $penalty -= $this->penalty_accrued;
                break;
            case 'percentage':
            default:
                // Percentage penalty per month, applied daily (simple interest)
                $monthlyRate = $this->penalty_rate / 100;
                $dailyRate = $monthlyRate / 30;
                $penalty = $baseAmount * $dailyRate * $daysLate;
                break;
        }
        $this->penalty_accrued += $penalty;
        $this->last_penalty_calculation = $now;
        $this->save();
        return $this->penalty_accrued;
    }

    public function shippedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_out_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function canShipOut(): bool
    {
        // Only supplier can ship out, and only if payment is validated (PAID)
        return $this->status === 'pending_payment' && $this->isPaymentValidated() && is_null($this->shipped_out_at);
    }

    public function canReceive(): bool
    {
        // Only warehouse can receive, and only if shipped out
        return !is_null($this->shipped_out_at) && is_null($this->received_at);
    }

    public function markAsShippedOut()
    {
        if (!$this->canShipOut()) {
            throw new \Exception('Cannot ship out unless payment is validated and not already shipped.');
        }
        $this->shipped_out_at = now();
        $this->shipped_out_by = auth()->id();
        $this->status = 'shipping';
        $this->save();
    }

    public function markAsReceived()
    {
        if (!$this->canReceive()) {
            throw new \Exception('Cannot receive unless shipped out and not already received.');
        }
        $this->received_at = now();
        $this->received_by = auth()->id();
        $this->status = 'delivered';
        $this->save();
    }
} 