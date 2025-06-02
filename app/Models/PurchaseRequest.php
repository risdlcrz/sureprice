<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'pr_number',
        'requester_id',
        'department',
        'required_date',
        'purpose',
        'status',
        'notes'
    ];

    protected $casts = [
        'required_date' => 'date'
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PurchaseRequestAttachment::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'primary',
        };
    }
} 