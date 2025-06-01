<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'pr_number',
        'requester_id',
        'department',
        'required_date',
        'purpose',
        'status',
        'notes'
    ];

    protected $casts = [
        'required_date' => 'date'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'processing' => 'info',
            'completed' => 'primary'
        ][$this->status] ?? 'secondary';
    }
} 