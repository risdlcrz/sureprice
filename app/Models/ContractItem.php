<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractItem extends Model
{
    protected $fillable = [
        'contract_id',
        'material_id',
        'material_name',
        'unit',
        'supplier_id',
        'supplier_name',
        'quantity',
        'amount',
        'total'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'amount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Get the contract that owns the item
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the material for this item
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the supplier for this item
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
} 