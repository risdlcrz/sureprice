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
        'materials',
        'price',
        'contact_person',
        'designation',
        'email',
        'mobile_number',
        'telephone_number',
        'address',
        'business_reg_no',
        'supplier_type',
        'business_size',
        'years_operation',
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
        'price' => 'float',
        'years_operation' => 'integer',
        'vat_registered' => 'boolean',
        'use_sureprice' => 'boolean'
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

    public function evaluations(): HasMany
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(SupplierMetric::class);
    }

    public function getLatestEvaluation()
    {
        return $this->evaluations()
            ->latest('evaluation_date')
            ->first();
    }

    public function getFinalScore()
    {
        $evaluation = $this->getLatestEvaluation();
        if (!$evaluation) return 0;

        $weights = [
            'engagement' => 0.15,
            'delivery' => 0.20,
            'performance' => 0.20,
            'quality' => 0.20,
            'cost' => 0.15,
            'sustainability' => 0.10
        ];

        return (
            $weights['engagement'] * $evaluation->engagement_score +
            $weights['delivery'] * $evaluation->delivery_speed_score +
            $weights['performance'] * $evaluation->performance_score +
            $weights['quality'] * $evaluation->quality_score +
            $weights['cost'] * $evaluation->cost_variance_score +
            $weights['sustainability'] * $evaluation->sustainability_score
        ) / 5;
    }

    public function getStatusColorAttribute()
    {
        return [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning'
        ][$this->status] ?? 'secondary';
    }
} 