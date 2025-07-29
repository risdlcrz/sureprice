<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Contract extends Model
{
    protected $fillable = [
        'contract_number',
        'contractor_id',
        'client_id',
        'property_id',
        'quotation_request_id',
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
        'payment_plan',
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
        'client_date_signed',
        'contractor_date_signed',
        'status',
        'purchase_order_id',
        'property_address',
        'same_as_client_address',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'final_amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_date' => 'date',
        'client_date_signed' => 'datetime',
        'contractor_date_signed' => 'datetime',
        'total_amount' => 'decimal:2',
        'base_labor_rate' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'materials_cost' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            DB::beginTransaction();
            try {
                // Get the current year
                $year = date('Y');
                
                // Get the last contract number for this year
                $lastContract = static::where('contract_number', 'like', "CT{$year}%")
                    ->orderBy('contract_number', 'desc')
                    ->lockForUpdate()  // Add lock to prevent race conditions
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
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
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
            'active' => 'primary',
            'partially_paid' => 'info',
            'fully_paid' => 'success',
            'overdue' => 'danger',
            'suspended' => 'warning',
            'terminated' => 'dark',
            'expired' => 'secondary',
            'renewed' => 'success',
            'completed' => 'success',
        ][$this->status] ?? 'secondary';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeEdited()
    {
        return $this->status !== 'completed';
    }

    public function canBeDeleted()
    {
        return $this->status !== 'completed';
    }

    public function generateMaterialRequest()
    {
        $materialRequest = new \App\Models\MaterialRequest([
            'contract_id' => $this->id,
            'requested_by' => auth()->id(),
            'status' => 'pending',
            'notes' => 'Auto-generated from contract ' . $this->contract_number
        ]);
        $materialRequest->save();

        foreach ($this->items as $item) {
            $requiredQty = $item->quantity;
            $warehouses = \App\Models\Warehouse::all();
            $fulfilled = 0;
            foreach ($warehouses as $warehouse) {
                $materialStock = $warehouse->materials()->where('materials.id', $item->material_id)->first();
                $available = $materialStock ? $materialStock->pivot->current_stock : 0;
                if ($available > 0 && $fulfilled < $requiredQty) {
                    $toDeduct = min($available, $requiredQty - $fulfilled);
                    // Deduct stock
                    $warehouse->materials()->updateExistingPivot($item->material_id, [
                        'current_stock' => $available - $toDeduct
                    ]);
                    // Add fulfilled item
                    $materialRequest->items()->create([
                        'material_id' => $item->material_id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => $toDeduct,
                        'unit' => $item->unit,
                        'fulfilled_quantity' => $toDeduct
                    ]);
                    $fulfilled += $toDeduct;
                }
            }
            // If not fully fulfilled, create a purchase request for the lacking quantity
            if ($fulfilled < $requiredQty) {
                $materialRequest->items()->create([
                    'material_id' => $item->material_id,
                    'warehouse_id' => null,
                    'quantity' => $requiredQty - $fulfilled,
                    'unit' => $item->unit,
                    'fulfilled_quantity' => 0
                ]);
                // Create a purchase request for the lacking quantity
                $purchaseRequest = new \App\Models\PurchaseRequest([
                    'request_number' => 'PR-' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
                    'contract_id' => $this->id,
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                    'is_project_related' => true,
                    'notes' => 'Auto-generated from material request for contract ' . $this->contract_number
                ]);
                $purchaseRequest->save();
                $purchaseRequest->items()->create([
                    'material_id' => $item->material_id,
                    'description' => $item->material_name,
                    'quantity' => $requiredQty - $fulfilled,
                    'unit' => $item->unit,
                    'estimated_unit_price' => $item->amount,
                    'total_amount' => ($requiredQty - $fulfilled) * $item->amount,
                    'notes' => 'From material request',
                    'supplier_id' => null,
                    'preferred_supplier_id' => null
                ]);
                $purchaseRequest->total_amount = ($requiredQty - $fulfilled) * $item->amount;
                $purchaseRequest->save();
            }
        }
        return $materialRequest;
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
                    'payment_type' => $schedule['stage'],
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

    public function generatePaymentSchedule()
    {
        $paymentSchedule = [];
        
        if ($this->payment_plan) {
            $plan = $this->payment_plan;
            $total = $this->total_amount;
            
            if ($plan === '30% down, 40% halfway, 30% on completion') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.30,
                    'due_date' => $this->start_date->format('Y-m-d')
                ];
                
                // Calculate halfway date (middle of project duration)
                $projectDuration = $this->start_date->diffInDays($this->end_date);
                $halfwayDate = $this->start_date->copy()->addDays($projectDuration / 2);
                
                $paymentSchedule[] = [
                    'stage' => 'Halfway Payment',
                    'amount' => $total * 0.40,
                    'due_date' => $halfwayDate->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.30,
                    'due_date' => $this->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === '50/50') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.50,
                    'due_date' => $this->start_date->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.50,
                    'due_date' => $this->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'Full upon completion') {
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total,
                    'due_date' => $this->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'milestone') {
                $paymentSchedule[] = [
                    'stage' => 'Downpayment',
                    'amount' => $total * 0.20,
                    'due_date' => $this->start_date->format('Y-m-d')
                ];
                
                // After Foundation (25% of project duration)
                $foundationDate = $this->start_date->copy()->addDays($this->start_date->diffInDays($this->end_date) * 0.25);
                $paymentSchedule[] = [
                    'stage' => 'After Foundation',
                    'amount' => $total * 0.20,
                    'due_date' => $foundationDate->format('Y-m-d')
                ];
                
                // After Structure (60% of project duration)
                $structureDate = $this->start_date->copy()->addDays($this->start_date->diffInDays($this->end_date) * 0.60);
                $paymentSchedule[] = [
                    'stage' => 'After Structure',
                    'amount' => $total * 0.30,
                    'due_date' => $structureDate->format('Y-m-d')
                ];
                
                $paymentSchedule[] = [
                    'stage' => 'Completion Payment',
                    'amount' => $total * 0.30,
                    'due_date' => $this->end_date->format('Y-m-d')
                ];
            }
            elseif ($plan === 'monthly3') {
                $monthlyAmount = $total / 3;
                for ($i = 1; $i <= 3; $i++) {
                    $dueDate = $this->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
            elseif ($plan === 'monthly6') {
                $monthlyAmount = $total / 6;
                for ($i = 1; $i <= 6; $i++) {
                    $dueDate = $this->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
            elseif ($plan === 'monthly12') {
                $monthlyAmount = $total / 12;
                for ($i = 1; $i <= 12; $i++) {
                    $dueDate = $this->start_date->copy()->addMonths($i);
                    $paymentSchedule[] = [
                        'stage' => "Month {$i} Payment",
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate->format('Y-m-d')
                    ];
                }
            }
        }
        
        return $paymentSchedule;
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

    public function getOverduePaymentsAttribute()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();
    }

    public function getPaymentStatusSummaryAttribute()
    {
        $payments = $this->payments;
        $total = $payments->sum('amount');
        $paid = $payments->where('status', 'paid')->sum('amount');
        $pending = $payments->where('status', 'pending')->sum('amount');
        $verification = $payments->where('status', 'for_verification')->sum('amount');
        
        return [
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'verification' => $verification,
            'paid_percentage' => $total > 0 ? round(($paid / $total) * 100, 2) : 0,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'verification_percentage' => $total > 0 ? round(($verification / $total) * 100, 2) : 0,
        ];
    }

    public function hasOverduePayments()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->exists();
    }

    public function isPaymentComplete()
    {
        return $this->payments()
            ->where('status', '!=', 'paid')
            ->count() === 0;
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

    public function getEffectivePropertyAddressAttribute()
    {
        if ($this->same_as_client_address && $this->client && isset($this->client->address)) {
            return $this->client->address;
        }
        return $this->property_address;
    }

    public function getWorkflowStatus()
    {
        // Example logic, adjust as needed for your workflow
        if (!$this->material_request_status) {
            return 'Pending Material Request';
        } elseif (!$this->stock_checked_at) {
            return 'Pending Stock Check';
        } elseif ($this->admin_approval_status === 'pending') {
            return 'Pending Admin Approval';
        } elseif ($this->supplier_approval_status === 'pending') {
            return 'Pending Supplier Approval';
        } elseif (!$this->isPaymentValidated()) {
            return 'Pending Payment Validation';
        } elseif (!$this->delivery_status) {
            return 'Pending Delivery';
        } elseif ($this->delivery_status === 'pending_confirmation') {
            return 'Pending Delivery Confirmation';
        } elseif ($this->isCompleted()) {
            return 'Completed';
        } else {
            return 'Unknown';
        }
    }

    public function isFullyApproved()
    {
        return ($this->admin_approval_status === 'approved') && ($this->supplier_approval_status === 'approved');
    }

    public function isPaymentValidated()
    {
        // Adjust logic as needed for your app's payment validation
        return ($this->admin_payment_validated_at && $this->supplier_payment_validated_at);
    }

    public function isDeliveryConfirmed()
    {
        return $this->delivery_status === 'confirmed';
    }

    /**
     * Check if contractor signature is present
     */
    public function hasContractorSignature()
    {
        if (empty($this->contractor_signature)) {
            return false;
        }
        
        // Check if it's a file path
        if (strpos($this->contractor_signature, 'signatures/') === 0) {
            return file_exists(storage_path('app/public/' . $this->contractor_signature));
        }
        
        // Check if it's base64 data
        if (strpos($this->contractor_signature, 'data:image') === 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if client signature is present
     */
    public function hasClientSignature()
    {
        if (empty($this->client_signature)) {
            return false;
        }
        
        // Check if it's a file path
        if (strpos($this->client_signature, 'signatures/') === 0) {
            return file_exists(storage_path('app/public/' . $this->client_signature));
        }
        
        // Check if it's base64 data
        if (strpos($this->client_signature, 'data:image') === 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if both signatures are present
     */
    public function hasBothSignatures()
    {
        return $this->hasContractorSignature() && $this->hasClientSignature();
    }

    /**
     * Check if contract can be approved (has both signatures)
     */
    public function canBeApproved()
    {
        return $this->hasBothSignatures();
    }

    /**
     * Get signature status for debugging
     */
    public function getSignatureStatus()
    {
        return [
            'contractor_signature' => [
                'present' => $this->hasContractorSignature(),
                'path' => $this->contractor_signature,
                'date_signed' => $this->contractor_date_signed,
            ],
            'client_signature' => [
                'present' => $this->hasClientSignature(),
                'path' => $this->client_signature,
                'date_signed' => $this->client_date_signed,
            ],
            'can_be_approved' => $this->canBeApproved(),
        ];
    }
} 