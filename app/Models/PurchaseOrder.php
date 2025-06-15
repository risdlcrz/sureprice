<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Contract;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'purchase_request_id',
        'contract_id',
        'supplier_id',
        'ordered_by',
        'approved_by',
        'status',
        'order_date',
        'expected_delivery_date',
        'total_amount',
        'payment_terms',
        'delivery_terms',
        'delivery_date',
        'shipping_terms',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2'
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
} 