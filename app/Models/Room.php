<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'contract_id', 'name', 'length', 'width', 'area'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function scopeTypes()
    {
        return $this->belongsToMany(ScopeType::class, 'room_scope_type', 'room_id', 'scope_type_id');
    }
}
