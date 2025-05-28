<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'street',
        'city',
        'state',
        'postal'
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
} 