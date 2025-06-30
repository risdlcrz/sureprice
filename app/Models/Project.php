<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_number',
        'contract_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress',
        'project_manager_id',
        'client_representative_id',
        'budget',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
        'budget' => 'decimal:2'
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function clientRepresentative()
    {
        return $this->belongsTo(User::class, 'client_representative_id');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function documents()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    // Status helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOnHold()
    {
        return $this->status === 'on_hold';
    }

    // Generate unique project number
    public static function generateProjectNumber()
    {
        $prefix = 'PRJ';
        $year = date('Y');
        $month = date('m');
        
        $lastProject = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastProject ? intval(substr($lastProject->project_number, -4)) + 1 : 1;
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }
} 