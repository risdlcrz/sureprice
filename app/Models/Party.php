<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'name',
        'company_name',
        'street',
        'barangay',
        'city',
        'state',
        'postal',
        'email',
        'phone'
    ];

    /**
     * Get the contracts where this party is the contractor
     */
    public function contractorContracts()
    {
        return $this->hasMany(Contract::class, 'contractor_id');
    }

    /**
     * Get the contracts where this party is the client
     */
    public function clientContracts()
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    /**
     * Scope a query to only include contractors
     */
    public function scopeContractors($query)
    {
        return $query->where('entity_type', 'contractor');
    }

    /**
     * Scope a query to only include clients
     */
    public function scopeClients($query)
    {
        return $query->where('entity_type', 'client');
    }
} 