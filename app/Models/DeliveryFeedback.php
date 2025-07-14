<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryFeedback extends Model
{
    protected $table = 'delivery_feedback';
    protected $fillable = [
        'delivery_id',
        'warehouse_id',
        'supplier_id',
        'rating',
        'comments',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(User::class, 'warehouse_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
} 