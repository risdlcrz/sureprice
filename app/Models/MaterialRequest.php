<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_request_id',
        'user_id',
        'status',
    ];

    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 