<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'path'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
} 