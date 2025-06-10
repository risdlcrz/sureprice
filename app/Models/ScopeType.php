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
        'items'
    ];

    protected $casts = [
        'materials' => 'array',
        'items' => 'array',
        'labor_rate' => 'decimal:2',
        'estimated_days' => 'integer'
    ];

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'scope_type_material');
    }
}
