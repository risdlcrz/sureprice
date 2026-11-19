<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'requested_by',
        'notes',
        'quotation_request_id',
        'status',
    ];

    public function quotationRequest()
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    /** Same as requestedBy(); material_requests has no user_id column. */
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\MaterialRequestItem::class);
    }

    public function contract()
    {
        return $this->belongsTo(\App\Models\Contract::class);
    }
} 