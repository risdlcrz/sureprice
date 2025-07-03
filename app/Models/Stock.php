<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'warehouse_id',
        'supplier_id',
        'material_id',
        'current_stock',
        'threshold',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
} 