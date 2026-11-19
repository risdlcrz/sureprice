<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['pending', 'in_progress', 'on_hold', 'delayed', 'completed'];
    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    /** Validation shared by the project task pages and the project timeline. */
    public static function rules(bool $updating): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:' . implode(',', self::PRIORITIES),
            'assigned_to' => 'nullable|exists:users,id',
            'room_id' => 'nullable|exists:rooms,id',
            'scope_type_id' => 'nullable|exists:scope_types,id',
            'notes' => 'nullable|string',
        ] + ($updating ? [
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'progress' => 'required|integer|min:0|max:100',
        ] : []);
    }

    protected static function booted(): void
    {
        // the project's progress is the average of its tasks', whichever page changed them
        $sync = fn (self $task) => $task->project?->recalculateProgress();
        static::saved($sync);
        static::deleted($sync);
    }

    protected $fillable = [
        'contract_id',
        'project_id',
        'room_id',
        'scope_type_id',
        'name',
        'description',
        'start_date',
        'due_date',
        'status',
        'priority',
        'assigned_to',
        'progress',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'progress' => 'integer'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
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
        return $query->where('due_date', now()->toDateString())
                    ->where('status', '!=', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
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
        return $this->due_date < now() && $this->status !== 'completed';
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->due_date);
    }

    public function getRemainingDaysAttribute()
    {
        if ($this->status === 'completed') {
            return 0;
        }
        return max(0, now()->diffInDays($this->due_date, false));
    }
} 