<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractItem extends Model
{
    protected $fillable = [
        'contract_id',
        'material_id',
        'supplier_id',
        'quantity',
        'amount',
        'total'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'amount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
} 