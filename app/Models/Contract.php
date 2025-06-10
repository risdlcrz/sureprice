<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'contract_number',
        'contractor_id',
        'client_id',
        'property_id',
        'title',
        'scope_of_work',
        'scope_description',
        'start_date',
        'end_date',
        'total_amount',
        'base_labor_rate',
        'labor_cost',
        'materials_cost',
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
        'status',
        'purchase_order_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_date' => 'date',
        'total_amount' => 'decimal:2',
        'base_labor_rate' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'materials_cost' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            // Get the current year
            $year = date('Y');
            
            // Get the last contract number for this year
            $lastContract = static::where('contract_number', 'like', "CT{$year}%")
                ->orderBy('contract_number', 'desc')
                ->first();

            if ($lastContract) {
                // Extract the number from the last contract number and increment it
                $lastNumber = intval(substr($lastContract->contract_number, -4));
                $newNumber = $lastNumber + 1;
            } else {
                // If no contracts exist for this year, start with 0001
                $newNumber = 1;
            }

            // Generate the new contract number
            $contract->contract_number = sprintf("CT%s%04d", $year, $newNumber);
        });

        static::booted(function () {
            static::creating(function ($contract) {
                if (empty($contract->title)) {
                    $contract->title = 'Contract for ' . ($contract->client->name ?? 'Unknown Client');
                }
            });
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
} 