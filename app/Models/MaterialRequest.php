<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'contract_id',
        'requested_by',
        'status',
        'notes',
        'total_amount',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'completed_at',
        'completed_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function getProgressAttribute()
    {
        $statuses = [
            'pending' => 25,
            'processing' => 50,
            'approved' => 75,
            'completed' => 100,
            'rejected' => 100,
            'cancelled' => 100
        ];

        return $statuses[$this->status] ?? 0;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'approved' => 'success',
            'completed' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    public function approve()
    {
        if (!Auth::user()->can('approve-material-requests')) {
            throw new \Exception('Unauthorized to approve material requests.');
        }

        $this->status = 'approved';
        $this->approved_at = now();
        $this->approved_by = Auth::id();
        $this->save();
    }

    public function reject()
    {
        if (!Auth::user()->can('reject-material-requests')) {
            throw new \Exception('Unauthorized to reject material requests.');
        }

        $this->status = 'rejected';
        $this->rejected_at = now();
        $this->rejected_by = Auth::id();
        $this->save();
    }

    public function complete()
    {
        if (!Auth::user()->can('complete-material-requests')) {
            throw new \Exception('Unauthorized to complete material requests.');
        }

        $this->status = 'completed';
        $this->completed_at = now();
        $this->completed_by = Auth::id();
        $this->save();
    }
} 