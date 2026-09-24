<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those credentials do not match our records.'])->onlyInput('email');
        }

        $user = auth()->user();
        if (in_array($user->status, ['suspended', 'inactive'], true)) {
            auth()->logout();
            return back()->withErrors(['email' => 'This account is suspended or inactive.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($user->dashboardRoute());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function registerCustomer(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            ...$data,
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Welcome to MarketLink.');
    }

    public function registerFarmer(Request $request)
    {
        $data = $request->validate([
            'stall_name' => ['required', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'operating_days' => ['required', 'array', 'min:1'],
            'operating_days.*' => ['in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $data['contact_person'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => $data['password'],
            'role' => 'farmer',
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        FarmerProfile::query()->create([
            'user_id' => $user->id,
            'stall_name' => $data['stall_name'],
            'contact_person' => $data['contact_person'],
            'address' => $data['address'],
            'operating_days' => $data['operating_days'],
            'approval_status' => 'pending',
            'pickup_slots' => [
                ['label' => '08:00-10:00'],
                ['label' => '10:00-12:00'],
            ],
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('farmer.dashboard')->with('success', 'Account created. An admin must approve your stall before products go live.');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status).' If mail is set to log, open storage/logs/laravel.log for the link.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showReset(string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => request('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password updated. You can sign in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
