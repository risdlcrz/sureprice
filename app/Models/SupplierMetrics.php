<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'total_deliveries',
        'ontime_deliveries',
        'total_units',
        'defective_units',
        'estimated_cost',
        'actual_cost',
        'measurement_date'
    ];

    protected $casts = [
        'total_deliveries' => 'integer',
        'ontime_deliveries' => 'integer',
        'total_units' => 'integer',
        'defective_units' => 'integer',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'measurement_date' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
} 