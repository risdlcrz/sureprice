<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * A requested change to a verified company detail, approved by an admin against an uploaded proof document.
 * Approving applies new_value to the company (see CompanyChangeRequestController / Admin\CompanyChangeRequestController).
 */
class CompanyChangeRequest extends Model
{
    /** Fields that may be changed this way => label. */
    public const FIELDS = ['company_name' => 'Company name'];

    public const PROOF_DISK = 'local';

    protected $fillable = [
        'company_id', 'requested_by', 'field', 'old_value', 'new_value', 'reason',
        'proof_path', 'proof_name', 'proof_mime', 'proof_size',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function fieldLabel(): string
    {
        return self::FIELDS[$this->field] ?? ucfirst(str_replace('_', ' ', $this->field));
    }

    public function deleteProof(): void
    {
        Storage::disk(self::PROOF_DISK)->delete($this->proof_path);
    }
}
