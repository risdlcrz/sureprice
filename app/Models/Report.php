<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'generated_by_id',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function generated_by()
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }
} 