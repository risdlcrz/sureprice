<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuotationAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'path',
        'original_name',
        'file_type',
        'file_size'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
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

        static::creating(function ($attachment) {
            if (!$attachment->file_size && Storage::exists($attachment->path)) {
                $attachment->file_size = Storage::size($attachment->path);
            }
            if (!$attachment->file_type && Storage::exists($attachment->path)) {
                $attachment->file_type = Storage::mimeType($attachment->path);
            }
        });

        static::deleting(function ($attachment) {
            if (Storage::exists($attachment->path)) {
                Storage::delete($attachment->path);
            }
        });
    }
} 