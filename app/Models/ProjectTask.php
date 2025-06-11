<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'room_id',
        'scope_type_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress',
        'priority',
        'assigned_to',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer'
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeType()
    {
        return $this->belongsTo(ScopeType::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDelayed($query)
    {
        return $query->where('status', 'delayed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeDueToday($query)
    {
        return $query->where('end_date', now()->toDateString())
                    ->where('status', '!=', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now()->toDateString())
                    ->where('status', '!=', 'completed');
    }

    // Methods
    public function updateProgress($progress)
    {
        $this->update([
            'progress' => $progress,
            'status' => $progress >= 100 ? 'completed' : 'in_progress'
        ]);
    }

    public function markAsDelayed()
    {
        $this->update(['status' => 'delayed']);
    }

    public function isOverdue()
    {
        return $this->end_date < now() && $this->status !== 'completed';
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->end_date);
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getRemainingDaysAttribute()
    {
        if ($this->status === 'completed') {
            return 0;
        }
        return max(0, now()->diffInDays($this->end_date, false));
    }
} 