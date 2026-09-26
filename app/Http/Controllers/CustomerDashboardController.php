<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('customer.dashboard', [
            'orders' => Order::query()->where('customer_id', $user->id)->with('farmer')->latest()->take(5)->get(),
            'favorites' => $user->favorites()->with(['product', 'farmer', 'market'])->latest()->take(4)->get(),
            'notifications' => $user->appNotifications()->latest('created_at')->take(5)->get(),
            'stats' => [
                'orders' => $user->orders()->count(),
                'open' => $user->orders()->whereIn('status', ['placed', 'accepted', 'ready_for_pickup'])->count(),
                'favorites' => $user->favorites()->count(),
            ],
        ]);
    }

    public function favorites()
    {
        $favorites = auth()->user()->favorites()->with(['product.farmer', 'farmer', 'market'])->latest()->paginate(12);

        return view('customer.favorites', compact('favorites'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->appNotifications()->latest('created_at')->paginate(15);

        return view('customer.notifications', compact('notifications'));
    }

    public function poll()
    {
        $items = auth()->user()->appNotifications()->latest('created_at')->take(8)->get();

        return response()->json([
            'unread' => $items->where('is_read', false)->count() > 0
                ? auth()->user()->appNotifications()->where('is_read', false)->count()
                : 0,
            'items' => $items->map(fn (AppNotification $n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'is_read' => $n->is_read,
                'time' => $n->created_at?->diffForHumans(),
            ]),
        ]);
    }

    public function read(AppNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['is_read' => true]);
        Cache::forget('user.'.auth()->id().'.unread');

        return back();
    }

    public function readAll()
    {
        auth()->user()->appNotifications()->where('is_read', false)->update(['is_read' => true]);
        Cache::forget('user.'.auth()->id().'.unread');

        return back()->with('success', 'All notifications marked as read.');
    }

    public function reviews()
    {
        $reviews = Review::query()->where('customer_id', auth()->id())->with(['product', 'farmer'])->latest()->paginate(12);

        return view('customer.reviews', compact('reviews'));
    }

    public function profile()
    {
        return view('customer.profile');
    }
}
