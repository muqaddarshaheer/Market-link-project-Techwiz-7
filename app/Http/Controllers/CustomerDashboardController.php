<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Order;
use App\Support\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        return back();
    }

    public function readAll()
    {
        auth()->user()->appNotifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function profile()
    {
        return view('customer.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->fill(collect($data)->except('avatar')->all());
        if ($request->hasFile('avatar')) {
            $user->avatar = ImageStore::put($request->file('avatar'), 'avatars', $user->avatar);
        }
        $user->save();

        return back()->with('success', 'Profile saved.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password changed.');
    }
}
