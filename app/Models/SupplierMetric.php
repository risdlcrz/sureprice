<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierMetric extends Model
{
    protected $fillable = [
        'supplier_id',
        'total_deliveries',
        'ontime_deliveries',
        'total_units',
        'defective_units',
        'estimated_cost',
        'actual_cost'
    ];

    protected $casts = [
        'total_deliveries' => 'integer',
        'ontime_deliveries' => 'integer',
        'total_units' => 'integer',
        'defective_units' => 'integer',
        'estimated_cost' => 'float',
        'actual_cost' => 'float'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
} 