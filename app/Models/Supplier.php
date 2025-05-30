<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'street',
        'city',
        'state',
        'postal',
        'tax_id',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot('is_preferred', 'price')
            ->withTimestamps();
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }
} 