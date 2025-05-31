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
        'budget_allocation',
        'payment_method',
        'payment_terms',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'check_number',
        'check_date',
        'check_image',
        'jurisdiction',
        'contract_terms',
        'client_signature',
        'contractor_signature',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_date' => 'date',
        'total_amount' => 'decimal:2',
        'budget_allocation' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            // Get the current year
            $year = date('Y');
            
            // Get the last contract number for this year
            $lastContract = static::where('contract_id', 'like', "CT{$year}%")
                ->orderBy('contract_id', 'desc')
                ->first();

            if ($lastContract) {
                // Extract the number from the last contract ID and increment it
                $lastNumber = intval(substr($lastContract->contract_id, -4));
                $newNumber = $lastNumber + 1;
            } else {
                // If no contracts exist for this year, start with 0001
                $newNumber = 1;
            }

            // Generate the new contract ID
            $contract->contract_id = sprintf("CT%s%04d", $year, $newNumber);
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