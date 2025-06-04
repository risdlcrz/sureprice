<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierEvaluation extends Model
{
    protected $fillable = [
        'supplier_id',
        'engagement_score',
        'delivery_speed_score',
        'performance_score',
        'quality_score',
        'cost_variance_score',
        'sustainability_score',
        'final_score',
        'evaluation_date',
        'delivery_ontime_ratio',
        'defect_ratio',
        'cost_variance_ratio'
    ];

    protected $casts = [
        'evaluation_date' => 'datetime',
        'engagement_score' => 'float',
        'delivery_speed_score' => 'float',
        'performance_score' => 'float',
        'quality_score' => 'float',
        'cost_variance_score' => 'float',
        'sustainability_score' => 'float',
        'final_score' => 'float',
        'delivery_ontime_ratio' => 'float',
        'defect_ratio' => 'float',
        'cost_variance_ratio' => 'float'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
} 