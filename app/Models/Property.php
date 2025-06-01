<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'unit_number',
        'barangay',
        'city',
        'state',
        'postal',
        'property_type',
        'property_size'
    ];

    /**
     * Get the contracts associated with this property
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
} 