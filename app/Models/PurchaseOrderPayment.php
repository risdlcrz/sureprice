<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPayment extends Model
{
    protected $fillable = [
        'purchase_order_id', 'amount', 'payment_method', 'admin_proof', 'admin_reference_number',
        'admin_paid_date', 'admin_notes', 'supplier_verified', 'supplier_verified_at',
        'supplier_notes', 'status'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->purchaseOrder->supplier();
    }
} 