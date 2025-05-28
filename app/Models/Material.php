<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_price',
        'unit',
        'has_preferred_suppliers'
    ];

    protected $casts = [
        'has_preferred_suppliers' => 'boolean',
        'default_price' => 'decimal:2'
    ];

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot('price', 'is_preferred')
            ->withTimestamps();
    }

    public function preferredSuppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot('price')
            ->wherePivot('is_preferred', true)
            ->withTimestamps();
    }
} 