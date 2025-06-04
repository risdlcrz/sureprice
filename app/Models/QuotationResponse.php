<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationResponse extends Model
{
    protected $fillable = [
        'quotation_id',
        'supplier_id',
        'total_amount',
        'payment_terms',
        'delivery_terms',
        'validity_period',
        'notes',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationResponseItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuotationResponseAttachment::class);
    }

    // Helper methods
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SUBMITTED]);
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SUBMITTED => 'badge-info',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger'
        ][$this->status] ?? 'badge-secondary';
    }
} 