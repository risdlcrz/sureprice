<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'engagement_score',
        'delivery_speed_score',
        'delivery_ontime_ratio',
        'performance_score',
        'quality_score',
        'defect_ratio',
        'cost_variance_score',
        'cost_variance_ratio',
        'sustainability_score',
        'final_score',
        'evaluation_date'
    ];

    protected $casts = [
        'engagement_score' => 'decimal:2',
        'delivery_speed_score' => 'decimal:2',
        'delivery_ontime_ratio' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'defect_ratio' => 'decimal:2',
        'cost_variance_score' => 'decimal:2',
        'cost_variance_ratio' => 'decimal:2',
        'sustainability_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'evaluation_date' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
} 