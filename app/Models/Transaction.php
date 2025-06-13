<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'payment_id',
        'date',
        'description',
        'amount',
        'type',
        'status',
        'payment_method',
        'reference_number',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    protected $dates = [
        'date'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 