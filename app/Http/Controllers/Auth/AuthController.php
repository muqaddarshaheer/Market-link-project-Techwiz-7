<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->status === 'suspended' || $user->status === 'inactive') {
            Auth::logout();

            return back()->withErrors(['email' => 'Your account has been '.$user->status.'.']);
        }

        return redirect()->intended($this->redirectFor($user));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:customer,farmer',
            // Farmer-specific fields
            'stall_name' => 'required_if:role,farmer|nullable|string|max:100',
            'contact_person' => 'required_if:role,farmer|nullable|string|max:100',
            'business_description' => 'nullable|string|max:2000',
            'operating_days' => 'nullable|array',
            'farmer_address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['role'] === 'farmer' ? 'pending' : 'active',
        ]);

        if ($user->isCustomer()) {
            Cart::create(['customer_id' => $user->id]);
        }

        if ($user->isFarmer()) {
            FarmerProfile::create([
                'user_id' => $user->id,
                'stall_name' => $data['stall_name'],
                'contact_person' => $data['contact_person'],
                'business_description' => $data['business_description'] ?? null,
                'operating_days' => $data['operating_days'] ?? [],
                'address' => $data['farmer_address'] ?? $data['address'] ?? null,
                'approval_status' => 'pending',
            ]);

            // Notify admins about pending farmer
            User::where('role', 'admin')->get()->each(function (User $admin) use ($user) {
                AppNotification::notify(
                    $admin,
                    'farmer_pending',
                    'New farmer registration',
                    $user->name.' registered as a farmer and awaits approval.',
                    ['user_id' => $user->id]
                );
            });
        }

        Auth::login($user);
        $request->session()->regenerate();

        $message = $user->isFarmer()
            ? 'Welcome! Your farmer account is pending admin approval.'
            : 'Welcome to MarketLink!';

        return redirect($this->redirectFor($user))->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    protected function redirectFor(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'farmer' => route('farmer.dashboard'),
            default => route('customer.dashboard'),
        };
    }
}
