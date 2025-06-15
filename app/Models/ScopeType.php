<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScopeType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'estimated_days',
        'labor_rate',
        'items',
        'labor_type',
        'minimum_labor_cost',
        'complexity_factor',
        'labor_hours_per_unit'
    ];

    protected $casts = [
        'items' => 'array',
        'labor_rate' => 'decimal:2',
        'estimated_days' => 'integer',
        'labor_type' => 'string',
        'minimum_labor_cost' => 'decimal:2',
        'complexity_factor' => 'decimal:2',
        'labor_hours_per_unit' => 'decimal:2'
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'scope_type_material')
                    ->withPivot(['id'])
                    ->select('materials.id', 'materials.name', 'materials.unit', 'materials.base_price', 'materials.srp_price', 'materials.is_per_area', 'materials.is_wall_material', 'materials.coverage_rate', 'materials.minimum_quantity', 'materials.waste_factor', 'materials.bulk_pricing', 'materials.warranty_period')
                    ->withTimestamps();
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_scope_type');
    }
}
