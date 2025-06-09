<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'quantity',
        'unit',
        'location',
        'batch_number',
        'expiry_date',
        'status',
        'last_restock_date',
        'last_restock_quantity',
        'minimum_threshold',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'expiry_date' => 'date',
        'last_restock_date' => 'datetime',
        'last_restock_quantity' => 'decimal:2',
        'minimum_threshold' => 'decimal:2'
    ];

    // Relationships
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_threshold');
    }

    public function scopeExpiring($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addMonths(3));
    }

    // Methods
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_threshold;
    }

    public function needsRestock(): bool
    {
        return $this->quantity <= $this->minimum_threshold;
    }

    public function updateStock(float $quantity, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->quantity += $quantity;
        } else {
            $this->quantity -= $quantity;
        }
        
        $this->save();
    }
} 