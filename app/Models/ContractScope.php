<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractScope extends Model
{
    protected $fillable = [
        'room_id',
        'scope_type_id',
        'scope_name',
        'scope_category',
        'selected_materials',
    ];

    protected $casts = [
        'selected_materials' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeType(): BelongsTo
    {
        return $this->belongsTo(ScopeType::class);
    }
} 