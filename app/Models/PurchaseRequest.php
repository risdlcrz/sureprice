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
        'material_request_id',
        'contract_id',
        'supplier_id',
        'status',
        'notes',
        'admin_approved',
        'admin_approved_at',
        'admin_approved_by',
        'supplier_approved',
        'supplier_approved_at',
        'supplier_approved_by'
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'supplier_approved' => 'boolean',
        'admin_approved_at' => 'datetime',
        'supplier_approved_at' => 'datetime',
    ];

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
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    public function approveByAdmin()
    {
        if (!Auth::user()->hasRole('admin')) {
            throw new \Exception('Only administrators can approve purchase requests.');
        }

        $this->admin_approved = true;
        $this->admin_approved_at = now();
        $this->admin_approved_by = Auth::id();
        $this->save();

        $this->checkAndCreatePurchaseOrder();
    }

    public function approveBySupplier()
    {
        if (!Auth::user()->hasRole('supplier')) {
            throw new \Exception('Only suppliers can approve purchase requests.');
        }

        $this->supplier_approved = true;
        $this->supplier_approved_at = now();
        $this->supplier_approved_by = Auth::id();
        $this->save();

        $this->checkAndCreatePurchaseOrder();
    }

    protected function checkAndCreatePurchaseOrder()
    {
        if ($this->admin_approved && $this->supplier_approved) {
            // Create purchase order
            $purchaseOrder = new PurchaseOrder([
                'purchase_request_id' => $this->id,
                'supplier_id' => $this->supplier_id,
                'status' => 'pending_payment',
                'notes' => 'Auto-generated from approved purchase request #' . $this->id
            ]);
            $purchaseOrder->save();

            // Copy items from purchase request to purchase order
            foreach ($this->items as $item) {
                $purchaseOrder->items()->create([
                    'material_id' => $item->material_id,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                ]);
            }

            $this->status = 'approved';
            $this->save();
        }
    }

    public function isFullyApproved(): bool
    {
        return $this->admin_approved && $this->supplier_approved;
    }
} 