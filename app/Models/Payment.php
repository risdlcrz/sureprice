<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'payable_type',
        'payable_id',
        'contract_id',
        'purchase_order_id',
        'amount',
        'payment_method',
        'payment_type',
        'status',
        'due_date',
        'paid_date',
        'reference_number',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'marked_paid_by',
        'client_payment_proof',
        'client_payment_method',
        'client_reference_number',
        'client_paid_amount',
        'client_paid_date',
        'client_notes',
        'admin_payment_proof',
        'admin_payment_method',
        'admin_reference_number',
        'admin_received_amount',
        'admin_received_date',
        'admin_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
        'client_paid_amount' => 'decimal:2',
        'client_paid_date' => 'date',
        'admin_received_amount' => 'decimal:2',
        'admin_received_date' => 'date',
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachment()
    {
        return $this->morphOne(\App\Models\Attachment::class, 'attachable');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'pending')
                    ->where('due_date', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('due_date', '<', now());
    }

    // Methods
    public function approve(User $approver)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now()
        ]);
    }

    public function markAsPaid($referenceNumber = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'reference_number' => $referenceNumber
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected'
        ]);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function isForVerification()
    {
        return $this->status === 'for_verification';
    }

    public function markForVerification()
    {
        $this->update(['status' => 'for_verification']);
    }

    public function canBeMarkedPaid()
    {
        return $this->status === 'for_verification' &&
            $this->client_paid_amount == $this->admin_received_amount &&
            $this->client_reference_number == $this->admin_reference_number &&
            $this->client_payment_method == $this->admin_payment_method;
    }

    // Static methods
    public static function generatePaymentNumber()
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $lastPayment = self::where('payment_number', 'like', "{$prefix}{$date}%")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $sequence = (int) substr($lastPayment->payment_number, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
} 