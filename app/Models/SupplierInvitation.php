<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'business_type',
        'contact_person',
        'position',
        'email',
        'phone',
        'address',
        'tax_number',
        'registration_number',
        'status',
        'notes'
    ];

    // Relationships
    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    public function getBusinessTypeTextAttribute()
    {
        return [
            'corporation' => 'Corporation',
            'partnership' => 'Partnership',
            'sole_proprietorship' => 'Sole Proprietorship',
            'other' => 'Other'
        ][$this->business_type] ?? 'Unknown';
    }
} 