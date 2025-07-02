<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'is_read',
        'read_at',
        'image',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'file_size' => 'integer'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    // File attachment methods
    public function hasAttachment(): bool
    {
        return !empty($this->file_path) || !empty($this->image);
    }

    public function getAttachmentPath(): ?string
    {
        return $this->file_path ?? $this->image;
    }

    public function getAttachmentName(): ?string
    {
        return $this->file_name ?? basename($this->image ?? '');
    }

    public function getAttachmentType(): ?string
    {
        return $this->file_type ?? $this->getMimeTypeFromPath();
    }

    public function getAttachmentSize(): ?int
    {
        return $this->file_size ?? $this->getFileSizeFromStorage();
    }

    public function isImage(): bool
    {
        $type = $this->getAttachmentType();
        return $type && str_starts_with($type, 'image/');
    }

    public function isVideo(): bool
    {
        $type = $this->getAttachmentType();
        return $type && str_starts_with($type, 'video/');
    }

    public function isAudio(): bool
    {
        $type = $this->getAttachmentType();
        return $type && str_starts_with($type, 'audio/');
    }

    public function isDocument(): bool
    {
        $type = $this->getAttachmentType();
        return $type && (
            str_starts_with($type, 'application/') ||
            str_starts_with($type, 'text/') ||
            in_array($type, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ])
        );
    }

    public function getFormattedSizeAttribute(): string
    {
        $size = $this->getAttachmentSize();
        if (!$size) return 'Unknown size';

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute(): string
    {
        $path = $this->getAttachmentPath();
        return $path ? Storage::url($path) : '';
    }

    public function getFileIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'bi-file-earmark-image text-warning';
        } elseif ($this->isVideo()) {
            return 'bi-file-earmark-play text-danger';
        } elseif ($this->isAudio()) {
            return 'bi-file-earmark-music text-info';
        } elseif ($this->isDocument()) {
            $type = $this->getAttachmentType();
            if (str_contains($type, 'pdf')) {
                return 'bi-file-earmark-pdf text-danger';
            } elseif (str_contains($type, 'word') || str_contains($type, 'document')) {
                return 'bi-file-earmark-word text-primary';
            } elseif (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) {
                return 'bi-file-earmark-excel text-success';
            } elseif (str_contains($type, 'powerpoint') || str_contains($type, 'presentation')) {
                return 'bi-file-earmark-ppt text-warning';
            } else {
                return 'bi-file-earmark-text text-secondary';
            }
        } else {
            return 'bi-file-earmark text-secondary';
        }
    }

    private function getMimeTypeFromPath(): ?string
    {
        $path = $this->getAttachmentPath();
        return $path ? Storage::mimeType($path) : null;
    }

    private function getFileSizeFromStorage(): ?int
    {
        $path = $this->getAttachmentPath();
        return $path ? Storage::size($path) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($message) {
            // Delete the file from storage when message is deleted
            $path = $message->getAttachmentPath();
            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
        });
    }
} 