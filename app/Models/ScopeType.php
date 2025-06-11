<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScopeType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'estimated_days',
        'labor_rate',
        'materials',
        'items',
        'labor_type',
        'minimum_labor_cost',
        'complexity_factor',
        'labor_hours_per_unit'
    ];

    protected $casts = [
        'items' => 'array',
        'materials' => 'array',
        'labor_rate' => 'decimal:2',
        'estimated_days' => 'integer',
        'labor_type' => 'string',
        'minimum_labor_cost' => 'decimal:2',
        'complexity_factor' => 'decimal:2',
        'labor_hours_per_unit' => 'decimal:2'
    ];

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'scope_type_material');
    }
}
