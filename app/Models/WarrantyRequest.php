<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'product_name',
        'serial_number',
        'purchase_date',
        'receipt_number',
        'model_number',
        'issue_description',
        'proof_of_purchase_path',
        'issue_photos_paths',
        'status',
        'admin_notes',
        'reviewed_at'
    ];

    protected $casts = [
        'issue_photos_paths' => 'array',
        'reviewed_at' => 'datetime',
        'purchase_date' => 'date'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
