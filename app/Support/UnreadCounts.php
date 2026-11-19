<?php

namespace App\Support;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/** Unread notification/message counts for the signed-in user, computed at most once per request per user. */
class UnreadCounts
{
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
        foreach (array_keys(request()->attributes->all()) as $k) {
            if (str_starts_with($k, 'unread-counts.')) request()->attributes->remove($k);
        }
    }

    // Cached on the current request, so nothing leaks between requests in long-running processes or tests.
    private static function remember(string $key, callable $count): int
    {
        $user = Auth::user();
        if (!$user) return 0;
        $bag = request()->attributes;
        $k = "unread-counts.$key.{$user->id}";
        if (!$bag->has($k)) $bag->set($k, $count($user));

        return $bag->get($k);
    }
}
