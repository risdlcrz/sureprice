<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'contract_id',
        'contractor_id',
        'client_id',
        'property_id',
        'scope_of_work',
        'scope_description',
        'start_date',
        'end_date',
        'total_amount',
        'jurisdiction',
        'contract_terms',
        'client_signature',
        'contractor_signature',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            $contract->contract_id = 'CNT-' . date('Y') . '-' . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
        });
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'contractor_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'client_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }
} 