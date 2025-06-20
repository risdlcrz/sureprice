<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_number',
        'purchase_order_id',
        'delivery_date',
        'received_by',
        'status',
        'total_units',
        'defective_units',
        'wastage_units',
        'quality_check_notes',
        'is_on_time',
        'actual_cost',
        'estimated_cost',
        'supplier_evaluation_id'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_units' => 'decimal:2',
        'defective_units' => 'decimal:2',
        'wastage_units' => 'decimal:2',
        'is_on_time' => 'boolean',
        'actual_cost' => 'decimal:2',
        'estimated_cost' => 'decimal:2'
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplierEvaluation(): BelongsTo
    {
        return $this->belongsTo(SupplierEvaluation::class);
    }

    public function getDefectRateAttribute(): float
    {
        if ($this->total_units == 0) {
            return 0;
        }
        return ($this->defective_units / $this->total_units) * 100;
    }

    public function getWastageRateAttribute(): float
    {
        if ($this->total_units == 0) {
            return 0;
        }
        return ($this->wastage_units / $this->total_units) * 100;
    }

    public function getCostVarianceAttribute(): float
    {
        if ($this->estimated_cost == 0) {
            return 0;
        }
        return (($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
    }

    public function getGoodUnitsAttribute(): float
    {
        return $this->total_units - $this->defective_units - $this->wastage_units;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ][$this->status] ?? 'secondary';
    }
} 