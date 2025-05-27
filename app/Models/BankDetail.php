<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'company_id',
        'bank_name',
        'account_name',
        'account_number'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}