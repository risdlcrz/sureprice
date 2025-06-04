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
        'date',
        'description',
        'amount',
        'type',
        'status',
        'payment_method',
        'reference_number',
        'notes'
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
} 