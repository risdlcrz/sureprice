<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'score',
        // Add other ranking fields as needed
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
} 