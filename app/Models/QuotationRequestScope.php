<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRequestScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_request_room_id',
        'scope_type_id',
        'scope_name',
        'scope_category',
        'selected_materials'
    ];

    protected $casts = [
        'selected_materials' => 'array'
    ];

    // Relationships
    public function room(): BelongsTo
    {
        return $this->belongsTo(QuotationRequestRoom::class, 'quotation_request_room_id');
    }

    public function scopeType(): BelongsTo
    {
        return $this->belongsTo(ScopeType::class);
    }
}
