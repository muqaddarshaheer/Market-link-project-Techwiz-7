<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order::with(['farmer', 'market'])
            ->where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'active_orders' => Order::where('customer_id', $user->id)
                ->whereIn('status', ['placed', 'accepted', 'ready_for_pickup'])->count(),
            'favorites' => $user->favorites()->count(),
            'completed' => Order::where('customer_id', $user->id)->where('status', 'completed')->count(),
        ];

        $announcements = Announcement::published()->latest('published_at')->take(3)->get();

        return view('customer.dashboard', compact('orders', 'stats', 'announcements'));
    }

    public function orders(Request $request)
    {
        $query = Order::with(['farmer', 'market', 'items'])
            ->where('customer_id', auth()->id());

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function profile()
    {
        return view('customer.profile.edit', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
