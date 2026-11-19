<?php

namespace App\Support;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/** Unread notification/message counts for the signed-in user, computed at most once per request per user. */
class UnreadCounts
{
    private static array $cache = [];

    public static function notifications(): int
    {
        return self::remember('notifications', fn (User $u) => Notification::forUser($u)->unread()->count());
    }

    public static function messages(): int
    {
        return self::remember('messages', fn (User $user) => Message::whereHas('conversation', function ($q) use ($user) {
            match (true) {
                $user->role === 'manager' || $user->user_type === 'admin' => $q->where('admin_id', $user->id),
                $user->isClientCompany() => $q->where('client_id', $user->id),
                $user->isSupplierCompany() => $q->where('supplier_id', $user->company->id),
                default => $q->whereRaw('1 = 0'),
            };
        })->where('is_read', false)->where('sender_id', '!=', $user->id)->count());
    }

    /** Drop cached counts (e.g. after marking notifications read). */
    public static function flush(): void
    {
        self::$cache = [];
    }

    private static function remember(string $key, callable $count): int
    {
        $user = Auth::user();
        return $user ? self::$cache[$key . ':' . $user->id] ??= $count($user) : 0;
    }
}
