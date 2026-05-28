<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $count = auth()->user()->notifications()->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }

    public static function createForUser($userId, string $type, string $title, string $body, string $channel = 'in_app')
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'title' => $title,
            'body' => $body,
            'sent_at' => now(),
        ]);
    }
}
