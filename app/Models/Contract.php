<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function scopeTypes(): BelongsToMany
    {
        return $this->belongsToMany(ScopeType::class, 'contract_scope_type');
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'in_progress' => 'info',
            'completed' => 'success'
        ][$this->status] ?? 'secondary';
    }

    public function generatePurchaseRequest()
    {
        $purchaseRequest = new PurchaseRequest([
            'request_number' => 'PR-' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'contract_id' => $this->id,
            'requested_by' => auth()->id(),
            'status' => 'pending',
            'is_project_related' => true,
            'notes' => 'Auto-generated from contract ' . $this->contract_number
        ]);

        $totalAmount = 0;

        foreach ($this->items as $item) {
            $purchaseRequest->items()->create([
                'material_id' => $item->material_id,
                'description' => $item->material_name,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'estimated_unit_price' => $item->amount,
                'total_amount' => $item->total,
                'notes' => 'From contract item',
                'supplier_id' => null,
                'preferred_supplier_id' => null
            ]);

            $totalAmount += $item->total;
        }

        $purchaseRequest->total_amount = $totalAmount;
        $purchaseRequest->save();

        return $purchaseRequest;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function generatePayments()
    {
        \Log::info('Generating payments for contract: ' . $this->id);
        \Log::info('Payment schedule: ' . $this->payment_schedule);
        
        $paymentSchedule = json_decode($this->payment_schedule, true);
        if (!$paymentSchedule) {
            \Log::error('Invalid payment schedule format for contract: ' . $this->id);
            return;
        }

        foreach ($paymentSchedule as $schedule) {
            try {
                Payment::create([
                    'payment_number' => Payment::generatePaymentNumber(),
                    'payable_type' => Contract::class,
                    'payable_id' => $this->id,
                    'contract_id' => $this->id,
                    'amount' => $schedule['amount'],
                    'payment_method' => $this->payment_method,
                    'payment_type' => $this->getPaymentType($schedule['stage']),
                    'status' => 'pending',
                    'due_date' => $schedule['due_date'],
                    'created_by' => auth()->id() ?? 1
                ]);
                \Log::info('Created payment for contract: ' . $this->id, [
                    'amount' => $schedule['amount'],
                    'due_date' => $schedule['due_date']
                ]);
            } catch (\Exception $e) {
                \Log::error('Error creating payment for contract: ' . $this->id, [
                    'error' => $e->getMessage(),
                    'schedule' => $schedule
                ]);
            }
        }
    }

    private function getPaymentType($stage)
    {
        if (stripos($stage, 'advance') !== false) {
            return 'advance';
        } else if (stripos($stage, 'retention') !== false) {
            return 'retention';
        } else if (stripos($stage, 'progress') !== false) {
            return 'progress';
        } else if (stripos($stage, 'installment') !== false) {
            return 'installment';
        } else {
            return 'regular';
        }
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getTotalPendingAttribute()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function getNextPaymentDueAttribute()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();
    }

    public function getOverduePaymentsAttribute()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function generateTasks()
    {
        // Generate tasks based on rooms and scope types
        foreach ($this->rooms as $room) {
            foreach ($room->scopeTypes as $scopeType) {
                // Calculate task duration based on scope type complexity
                $duration = $scopeType->estimated_days ?? 7; // Default to 7 days if not specified
                $startDate = $this->start_date;
                $endDate = $startDate->copy()->addDays($duration);

                ProjectTask::create([
                    'contract_id' => $this->id,
                    'room_id' => $room->id,
                    'scope_type_id' => $scopeType->id,
                    'title' => "{$scopeType->name} in {$room->name}",
                    'description' => "Complete {$scopeType->name} work in {$room->name}",
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'pending',
                    'progress' => 0,
                    'priority' => 'medium',
                    'created_by' => auth()->id()
                ]);
            }
        }
    }
} 