<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalWorkMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'additional_work_id',
        'material_id',
        'quantity',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2'
    ];

    public function additionalWork()
    {
        return $this->belongsTo(AdditionalWork::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
} 