<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'total_deliveries',
        'ontime_deliveries',
        'average_defect_rate',
        'average_cost_variance'
    ];

    protected $casts = [
        'total_deliveries' => 'integer',
        'ontime_deliveries' => 'integer',
        'average_defect_rate' => 'decimal:2',
        'average_cost_variance' => 'decimal:2'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getOnTimeDeliveryRateAttribute(): float
    {
        if ($this->total_deliveries === 0) {
            return 0;
        }
        return ($this->ontime_deliveries / $this->total_deliveries) * 100;
    }
} 