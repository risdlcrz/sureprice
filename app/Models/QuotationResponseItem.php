<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationResponseItem extends Model
{
    protected $fillable = [
        'quotation_response_id',
        'material_id',
        'quantity',
        'unit_price',
        'total_price',
        'specifications',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    public function response(): BelongsTo
    {
        return $this->belongsTo(QuotationResponse::class, 'quotation_response_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Helper methods
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Auto-calculate total price if unit price and quantity are set
            if ($item->unit_price && $item->quantity) {
                $item->total_price = $item->unit_price * $item->quantity;
            }
        });
    }
} 