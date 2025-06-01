<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'client_name',
        'start_date',
        'end_date',
        'status',
        'budget'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }
} 