<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'registration_number',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot(['price', 'lead_time'])
            ->withTimestamps();
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class)
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusColorAttribute()
    {
        return [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning'
        ][$this->status] ?? 'secondary';
    }

    public function evaluations()
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    public function metrics()
    {
        return $this->hasOne(SupplierMetrics::class);
    }
} 