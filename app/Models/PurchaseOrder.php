<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'purchase_request_id',
        'contract_id',
        'supplier_id',
        'total_amount',
        'status',
        'delivery_date',
        'payment_terms',
        'shipping_terms',
        'notes',
        'is_delivered',
        'is_on_time',
        'total_units',
        'defective_units',
        'estimated_cost',
        'actual_cost',
        'quality_notes',
        'is_completed'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
        'is_delivered' => 'boolean',
        'is_on_time' => 'boolean',
        'total_units' => 'integer',
        'defective_units' => 'integer',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'is_completed' => 'boolean'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getDeliveryStatusAttribute()
    {
        if (!$this->is_delivered) {
            return 'Pending';
        }
        return $this->is_on_time ? 'On Time' : 'Delayed';
    }

    public function getDefectRateAttribute()
    {
        if ($this->total_units == 0) {
            return 0;
        }
        return ($this->defective_units / $this->total_units) * 100;
    }

    public function getCostVarianceAttribute()
    {
        if ($this->estimated_cost == 0) {
            return 0;
        }
        return (($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
    }

    public function calculateEstimatedCost()
    {
        return $this->items->sum(function ($item) {
            $material = $item->material;
            return $item->quantity * $material->srp_price;
        });
    }

    public function calculateActualCost()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }
} 