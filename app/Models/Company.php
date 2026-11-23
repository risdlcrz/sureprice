<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /** Options offered on the registration / profile forms (validated with Rule::in). */
    public const TYPES = [
        'Construction & Engineering', 'Architecture & Design', 'Real Estate & Property Development', 'Manufacturing',
        'Wholesale & Distribution', 'Retail & E-Commerce', 'Information Technology & Software', 'Telecommunications',
        'Healthcare & Medical', 'Logistics & Transportation', 'Energy & Utilities', 'Financial Services',
        'Legal & Compliance', 'Education & Training', 'Marketing & Advertising', 'Hospitality & Tourism',
        'Government & Public Sector', 'Nonprofit / NGO', 'Other',
    ];

    public const BANKS = ['BDO', 'BPI', 'MetroBank', 'PNB', 'Security Bank', 'Union Bank', 'RCBC', 'China Bank'];

    public const BUSINESS_SIZES = ['Solo', 'Small Enterprise', 'Medium', 'Large'];

    public const PAYMENT_TERMS = ['7 days', '15 days', '30 days'];

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'email',
        'username',
        'mobile_number',
        'telephone_number',
        'street',
        'barangay',
        'city',
        'state',
        'postal',
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

    public function materials()
    {
        return $this->belongsToMany(\App\Models\Material::class, 'material_supplier', 'supplier_id', 'material_id')
            ->withPivot(['price', 'is_preferred'])
            ->withTimestamps();
    }
}
