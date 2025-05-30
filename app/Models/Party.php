<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends Model
{
    protected $fillable = [
        'type',
        'entity_type',
        'name',
        'company_name',
        'street',
        'city',
        'state',
        'postal',
        'email',
        'phone'
    ];

    protected $casts = [
        'type' => 'string',
        'entity_type' => 'string'
    ];

    public function contractsAsContractor(): HasMany
    {
        return $this->hasMany(Contract::class, 'contractor_id');
    }

    public function contractsAsClient(): HasMany
    {
        return $this->hasMany(Contract::class, 'client_id');
    }
} 