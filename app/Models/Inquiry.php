<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'subject',
        'description',
        'priority',
        'required_date',
        'department',
        'status'
    ];

    protected $casts = [
        'required_date' => 'date'
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class)
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'in_progress' => 'info',
            'completed' => 'primary'
        ][$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'orange',
            'urgent' => 'danger'
        ][$this->priority] ?? 'secondary';
    }
} 