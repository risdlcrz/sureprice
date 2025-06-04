<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'rfq_number',
        'due_date',
        'notes',
        'status',
        'total_amount',
        'payment_terms',
        'delivery_terms',
        'validity_period',
        'awarded_supplier_id',
        'awarded_amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'quotation_supplier')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material_quotation')
            ->withPivot('quantity', 'specifications')
            ->withTimestamps();
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuotationResponse::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuotationAttachment::class);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'sent' => 'info',
            'responded' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    // Helper methods
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]);
    }

    public function canBeSent(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }
} 