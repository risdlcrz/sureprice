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
} 