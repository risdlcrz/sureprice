<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'contract_id',
        'invitation_code',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'message',
        'due_date',
        'status',
        'response_notes',
        'responded_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'responded_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'supplier_invitation_materials')->withTimestamps();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getStatusColorAttribute()
    {
        return \App\Support\Status::color($this->status);
    }
}
