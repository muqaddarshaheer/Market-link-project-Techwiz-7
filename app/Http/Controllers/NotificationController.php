<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function poll()
    {
        $unread = AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest('created_at')
            ->take(10)
            ->get();

        return response()->json([
            'count' => AppNotification::where('user_id', auth()->id())->where('is_read', false)->count(),
            'items' => $unread->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'created_at' => $n->created_at?->diffForHumans(),
            ]),
        ]);
    }

    public function markRead(AppNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
