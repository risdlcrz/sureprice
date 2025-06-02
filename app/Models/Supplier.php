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
        'company',
        'supplier_type',
        'business_reg_no',
        'contact_person',
        'designation',
        'email',
        'mobile_number',
        'telephone_number',
        'street',
        'city',
        'province',
        'zip_code',
        'payment_terms',
        'vat_registered',
        'use_sureprice',
        'bank_name',
        'account_name',
        'account_number',
        'dti_sec_registration_path',
        'accreditation_docs_path',
        'mayors_permit_path',
        'valid_id_path',
        'company_profile_path',
        'price_list_path'
    ];

    protected $casts = [
        'vat_registered' => 'boolean',
        'use_sureprice' => 'boolean'
    ];

    public function materials(): HasMany
    {
        return $this->hasMany(SupplierMaterial::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    public function latestEvaluation()
    {
        return $this->hasOne(SupplierEvaluation::class)->latest();
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

    public function metrics(): HasMany
    {
        return $this->hasMany(SupplierMetric::class);
    }
} 