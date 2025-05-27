<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyDocument extends Model
{
    // 👇 This line tells Laravel to use the correct table
    protected $table = 'company_docs';

    protected $fillable = [
        'company_id',
        'type', // DTI_SEC, BUSINESS_PERMIT, etc.
        'path',
        'original_name',
        'mime_type',
        'size',
        'disk'
    ];

    protected $attributes = [
        'disk' => 'public'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }
}
