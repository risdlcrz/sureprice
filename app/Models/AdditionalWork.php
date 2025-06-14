<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'work_type',
        'description',
        'estimated_hours',
        'required_skills',
        'labor_notes',
        'preferred_start_date',
        'preferred_end_date',
        'timeline_notes',
        'additional_notes',
        'status'
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
        'preferred_end_date' => 'date',
        'estimated_hours' => 'decimal:2'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function materials()
    {
        return $this->hasMany(AdditionalWorkMaterial::class);
    }
} 