<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationRequestRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_request_id',
        'name',
        'length',
        'width',
        'height',
        'area',
        'volume'
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'area' => 'decimal:2',
        'volume' => 'decimal:2'
    ];

    // Relationships
    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(QuotationRequestScope::class);
    }

    // Helper methods
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($room) {
            // Auto-calculate area and volume
            if ($room->length && $room->width) {
                $room->area = $room->length * $room->width;
            }
            if ($room->area && $room->height) {
                $room->volume = $room->area * $room->height;
            }
        });
    }
}
