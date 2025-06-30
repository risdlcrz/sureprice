<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'payment_id',
        'transaction_id',
        'amount',
        'payment_method',
        'reference_number',
        'receipt_date',
        'issued_by',
        'issued_to',
        'notes',
        'pdf_path'
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateReceiptNumber()
    {
        $prefix = 'RCP';
        $year = date('Y');
        $month = date('m');
        
        $lastReceipt = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastReceipt ? intval(substr($lastReceipt->receipt_number, -4)) + 1 : 1;
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }
} 