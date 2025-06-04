<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class QuotationResponseAttachment extends Model
{
    protected $fillable = [
        'quotation_response_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];

    // Relationships
    public function response(): BelongsTo
    {
        return $this->belongsTo(QuotationResponse::class, 'quotation_response_id');
    }

    // Helper methods
    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            // Delete the actual file when the model is deleted
            Storage::delete($attachment->file_path);
        });
    }
} 