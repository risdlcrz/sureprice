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
        'status',
        'total_amount',
        'payment_terms',
        'delivery_date',
        'shipping_terms',
        'notes',
        'supplier_payment_validated_at',
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
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'supplier_payment_validated_at' => 'datetime',
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



    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }


    public function payments()
    {
        return $this->hasMany(\App\Models\PurchaseOrderPayment::class);
    }


    public function getStatusColorAttribute()
    {
        return \App\Support\Status::color($this->status);
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

    /** Statuses in which the order may still be edited or deleted (nothing paid or shipped yet). */
    public const EDITABLE_STATUSES = ['draft', 'pending', 'pending_payment'];

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES, true);
    }

    /** Supplier may ship once the payment has been verified (status "paid"). */
    public function canShipOut(): bool
    {
        return $this->status === 'paid' && is_null($this->shipped_out_at);
    }

    /** Warehouse/admin may receive once shipped. */
    public function canReceive(): bool
    {
        return $this->status === self::STATUS_SHIPPING && is_null($this->received_at);
    }

    /** Only approved/received orders can be completed (quality check + stock-in). */
    public function canComplete(): bool
    {
        return in_array($this->status, ['approved', self::STATUS_DELIVERED], true);
    }

    public function markAsShippedOut(string $trackingNumber, ?string $note = null): void
    {
        if (!$this->canShipOut()) {
            throw new \Exception('This order cannot be shipped: payment must be verified and it must not already be shipped.');
        }
        $this->forceFill([
            'shipped_out_at' => now(), 'shipped_out_by' => auth()->id(), 'shipped_at' => now(),
            'tracking_number' => $trackingNumber, 'shipping_note' => $note,
            'shipping_status' => 'Shipped Out', 'status' => self::STATUS_SHIPPING,
        ])->save();
    }

    public function markAsReceived(): void
    {
        if (!$this->canReceive()) {
            throw new \Exception('Only shipped orders that have not been received can be marked as received.');
        }
        $this->forceFill([
            'received_at' => now(), 'received_by' => auth()->id(), 'delivered_at' => now(),
            'is_delivered' => true, 'shipping_status' => 'Delivered', 'status' => self::STATUS_DELIVERED,
        ])->save();
    }
} 