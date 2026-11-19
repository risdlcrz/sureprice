<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Support\UnreadCounts;
use Illuminate\Http\Request;

/** One inbox for every role; replaces the per-role notification centers. */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser($request->user())->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /** Mark as read, then follow the notification's link (or return to the inbox). */
    public function open(Request $request, int $id)
    {
        $notification = $this->find($request, $id);
        $notification->read_at ??= now();
        $notification->save();

        return redirect($notification->link ?? route('notifications.index'));
    }

    public function markRead(Request $request, int $id)
    {
        $this->find($request, $id)->update(['read_at' => now()]);

        return $this->done($request, 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        Notification::forUser($request->user())->unread()->update(['read_at' => now()]);

        return $this->done($request, 'All notifications marked as read.');
    }

    public function clearRead(Request $request)
    {
        Notification::forUser($request->user())->whereNotNull('read_at')->delete();

        return $this->done($request, 'Read notifications cleared.');
    }

    public function unreadCounts()
    {
        return response()->json(['notifications' => UnreadCounts::notifications(), 'messages' => UnreadCounts::messages()]);
    }

    private function find(Request $request, int $id): Notification
    {
        return Notification::forUser($request->user())->findOrFail($id);
    }

    private function done(Request $request, string $message)
    {
        if (!$request->wantsJson()) {
            return back()->with('success', $message);
        }
        UnreadCounts::flush();

        return response()->json(['success' => true, 'unread' => UnreadCounts::notifications()]);
    }
}
