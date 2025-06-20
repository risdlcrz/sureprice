<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getActionColorAttribute()
    {
        return [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'approved' => 'success',
            'rejected' => 'danger',
            'sent' => 'primary'
        ][$this->action] ?? 'secondary';
    }
} 