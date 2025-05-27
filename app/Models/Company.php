<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'email',
        'username',
        'mobile_number',
        'telephone_number',
        'street',
        'city',
        'province',
        'zip_code',
        'supplier_type',
        'other_supplier_type',
        'business_reg_no',
        'years_operation',
        'business_size',
        'service_areas',
        'vat_registered',
        'use_sureprice',
        'payment_terms',
        'designation',
        'status',
        'primary_products_services',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
    ];

    protected $casts = [
        'vat_registered' => 'boolean',
        'use_sureprice' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function bankDetails()
    {
        return $this->hasOne(BankDetail::class);
    }
}
