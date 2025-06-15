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
        'quantity',
        'defective_quantity',
        'wastage_quantity'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'defective_quantity' => 'decimal:2',
        'wastage_quantity' => 'decimal:2'
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function getGoodQuantityAttribute(): float
    {
        return $this->quantity - $this->defective_quantity - $this->wastage_quantity;
    }
} 