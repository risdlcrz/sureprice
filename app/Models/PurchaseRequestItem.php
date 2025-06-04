<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'material_id',
        'description',
        'quantity',
        'unit',
        'estimated_unit_price',
        'total_amount',
        'notes',
        'specifications'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    // Relationships
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_amount = $item->quantity * $item->estimated_unit_price;
        });
    }
} 