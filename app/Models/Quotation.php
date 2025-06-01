<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'rfq_number',
        'due_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class)
            ->withPivot(['quantity', 'specifications'])
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
            'draft' => 'secondary',
            'sent' => 'info',
            'responded' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ][$this->status] ?? 'secondary';
    }
} 