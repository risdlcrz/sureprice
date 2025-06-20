<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'quality_rating',
        'has_complaints',
        // Add other evaluation fields as needed
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
} 