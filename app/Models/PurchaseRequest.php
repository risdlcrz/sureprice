<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'material_request_id',
        'contract_id',
        'supplier_id',
        'status',
        'notes',
        'requested_by',
        'is_project_related',
        'admin_approved',
        'admin_approved_at',
        'admin_approved_by',
        'supplier_approved',
        'supplier_approved_at',
        'supplier_approved_by',
        'total_amount'
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'supplier_approved' => 'boolean',
        'admin_approved_at' => 'datetime',
        'supplier_approved_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PENDING_ADMIN_APPROVAL = 'pending_admin_approval';
    const STATUS_PENDING_SUPPLIER_APPROVAL = 'pending_supplier_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'purchase_request_items', 'purchase_request_id', 'material_id')
            ->withPivot(['quantity', 'unit', 'description', 'estimated_unit_price', 'total_amount', 'notes'])
            ->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PurchaseRequestAttachment::class);
    }

    public function purchaseOrder(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    public function supplierApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_approved_by');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'pending_admin_approval' => 'info',
            'pending_supplier_approval' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    public function approveByAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            throw new \Exception('Only administrators can approve purchase requests.');
        }
        if ($this->status !== self::STATUS_PENDING_ADMIN_APPROVAL) {
            throw new \Exception('Purchase request is not pending admin approval.');
        }
        $this->admin_approved = true;
        $this->admin_approved_at = now();
        $this->admin_approved_by = $user->id;
        $this->status = self::STATUS_PENDING_SUPPLIER_APPROVAL;
        $this->save();
    }

    public function approveBySupplier()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('supplier')) {
            throw new \Exception('Only suppliers can approve purchase requests.');
        }
        if ($this->status !== self::STATUS_PENDING_SUPPLIER_APPROVAL) {
            throw new \Exception('Purchase request is not pending supplier approval.');
        }
        $this->supplier_approved = true;
        $this->supplier_approved_at = now();
        $this->supplier_approved_by = $user->id;
        $this->status = self::STATUS_APPROVED;
        $this->save();
        $this->checkAndCreatePurchaseOrder();
    }

    protected function checkAndCreatePurchaseOrder()
    {
        if ($this->admin_approved && $this->supplier_approved) {
            // Generate a unique PO number (format: POYYYYNNNN)
            $year = date('Y');
            $lastPO = \App\Models\PurchaseOrder::where('po_number', 'like', "PO{$year}%")
                ->orderBy('po_number', 'desc')
                ->first();
            $sequence = '0001';
            if ($lastPO) {
                $lastSequence = intval(substr($lastPO->po_number, 6));
                $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
            }
            $poNumber = "PO{$year}{$sequence}";

            // Calculate total amount
            $totalAmount = $this->items->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });

            // Set delivery_date to 7 days from now as a default
            $deliveryDate = now()->addDays(7)->toDateString();

            // Determine supplier_id: use the purchase request's supplier_id, or fallback to the first item's preferred_supplier_id
            $supplierId = $this->supplier_id;
            if (!$supplierId) {
                $supplierId = $this->items()->first()?->preferred_supplier_id;
            }

            $purchaseOrder = new \App\Models\PurchaseOrder([
                'po_number' => $poNumber,
                'purchase_request_id' => $this->id,
                'supplier_id' => $supplierId,
                'status' => 'pending_payment',
                'notes' => 'Auto-generated from approved purchase request #' . $this->id,
                'total_amount' => $totalAmount,
                'delivery_date' => $deliveryDate,
                'payment_terms' => 'Net 30',
                'shipping_terms' => 'FOB Destination'
            ]);
            $purchaseOrder->save();

            // Copy items from purchase request to purchase order
            foreach ($this->items()->get() as $item) {
                $purchaseOrder->items()->create([
                    'material_id' => $item->material_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->estimated_unit_price, // Use estimated_unit_price from PR item
                    'total_amount' => $item->quantity * $item->estimated_unit_price, // Calculate total
                    'unit' => $item->unit,
                    'description' => $item->description,
                    'notes' => $item->notes ?? null
                ]);
            }
            // Recalculate and update PO total amount
            $purchaseOrder->total_amount = $purchaseOrder->items()->sum('total_amount');
            $purchaseOrder->save();

            $this->status = self::STATUS_APPROVED;
            $this->save();
        }
    }

    public function isFullyApproved(): bool
    {
        return $this->admin_approved && $this->supplier_approved;
    }
} 