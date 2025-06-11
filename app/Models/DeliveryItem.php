<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'material_id',
        'purchase_order_item_id',
        'quantity',
        'defective_quantity',
        'wastage_quantity',
        'unit_price',
        'total_amount',
        'quality_check_notes',
        'batch_number',
        'expiry_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'defective_quantity' => 'decimal:2',
        'wastage_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expiry_date' => 'date'
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function getDefectRateAttribute()
    {
        if ($this->quantity == 0) {
            return 0;
        }
        return ($this->defective_quantity / $this->quantity) * 100;
    }

    public function getWastageRateAttribute()
    {
        if ($this->quantity == 0) {
            return 0;
        }
        return ($this->wastage_quantity / $this->quantity) * 100;
    }
} 