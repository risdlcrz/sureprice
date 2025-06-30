<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_request_id',
        'material_id',
        'warehouse_id',
        'quantity',
        'unit',
        'fulfilled_quantity',
        'unit_price',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'fulfilled_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getFulfilledPercentageAttribute()
    {
        if ($this->quantity <= 0) {
            return 0;
        }
        return min(100, round(($this->fulfilled_quantity / $this->quantity) * 100));
    }
} 