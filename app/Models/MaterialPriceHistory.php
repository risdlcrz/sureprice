<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPriceHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'material_id',
        'price',
        'date',
    ];
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
} 