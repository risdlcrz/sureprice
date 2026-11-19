<?php

namespace App\Models;

use App\Support\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * In-app notification. Rows are addressed to a user through any of user_id, for_user_id, or a
 * User morph (notifiable_*). `forUser()` is the single way to query a user's inbox.
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at', 'for_role', 'for_user_id'];

    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];

    private const PR_TYPES = ['Purchase Request', 'Purchase Request Approval Needed', 'Purchase Request Approved',
        'Purchase Request Supplier Approval Needed', 'Purchase Request Supplier Approved', 'Purchase Request Ready for PO'];

    protected static function booted(): void
    {
        // Keep both addressing styles in sync so every reader finds the row (notifiable_* is NOT NULL).
        static::creating(function (self $n) {
            if (!$n->user_id && $n->notifiable_type === User::class) {
                $n->user_id = $n->notifiable_id;
            }
            if (!$n->notifiable_type && $n->user_id) {
                $n->notifiable_type = User::class;
                $n->notifiable_id = $n->user_id;
            }
        });
    }

    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        $id = $user instanceof User ? $user->id : $user;

        return $query->where(fn ($q) => $q->where('user_id', $id)->orWhere('for_user_id', $id)
            ->orWhere(fn ($q) => $q->where('notifiable_type', User::class)->where('notifiable_id', $id)));
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function getPayloadAttribute(): array
    {
        $data = $this->data;
        return is_array($data) ? $data : (json_decode((string) $data, true) ?: ['message' => (string) $data]);
    }

    public function getTitleAttribute(): string
    {
        $d = $this->payload;
        if (!empty($d['title'])) return $d['title'];
        if (isset($d['payment_number'])) return 'Payment ' . Status::label($d['status'] ?? 'update') . ' — #' . $d['payment_number'];
        if ($this->type === 'ClientProceededQuotation') return 'Material Request Needed';

        return Status::label(class_basename((string) $this->type)) ?: 'Notification';
    }

    public function getMessageAttribute(): string
    {
        return (string) ($this->payload['message'] ?? '');
    }

    /** Where clicking the notification should take the user, or null. */
    public function getLinkAttribute(): ?string
    {
        $d = $this->payload;
        $link = $d['link'] ?? $d['action_url'] ?? null;
        if ($link) return $link;

        return match (true) {
            $this->type === 'ClientProceededQuotation' && isset($d['quotation_id'])
                => route('material-requests.create', ['quotation_id' => $d['quotation_id']]),
            isset($d['purchase_request_id']) && is_numeric($d['purchase_request_id'])
                => route('purchase-requests.show', $d['purchase_request_id']),
            in_array($this->type, self::PR_TYPES, true) => route('purchase-requests.index'),
            isset($d['payment_id']) => route('client.payments.show', $d['payment_id']),
            default => null,
        };
    }

    /** Bootstrap color for the item's accent, from the payload status (falls back to primary). */
    public function getToneAttribute(): string
    {
        $status = $this->payload['status'] ?? null;
        return $status ? Status::color($status) : 'primary';
    }
}
