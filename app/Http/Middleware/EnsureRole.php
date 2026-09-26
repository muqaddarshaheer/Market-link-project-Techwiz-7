<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    // Pending farmers can open tools, but not sell until admin approves the stall
    private const PENDING_FARMER_ALLOW = [
        'farmer.dashboard',
        'farmer.profile',
        'farmer.profile.update',
        'farmer.account',
        'farmer.crop-calculator',
        'farmer.crop-calculator.calculate',
        'farmer.smart-crop-guide',
        'farmer.smart-crop-guide.show',
        'farmer.crop-health',
        'farmer.crop-health.message',
        'farmer.crop-health.answer',
        'farmer.crop-health.photo',
        'farmer.crop-health.reset',
        'farmer.crop-health.speak',
        'farmer.weather.speak',
        'farmer.notifications',
        'farmer.notifications.poll',
        'farmer.notifications.read-all',
        'produce-guide',
        'logout',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }

        if (in_array($user->status, ['suspended', 'inactive'], true)) {
            auth()->logout();

            return redirect()->route('login')->withErrors(['email' => 'This account is not active.']);
        }

        if ($user->isFarmer() && in_array('farmer', $roles, true)) {
            $profile = $user->farmerProfile;
            if ($profile && ! $profile->isApproved()) {
                $name = $request->route()?->getName() ?? '';
                if ($name !== '' && ! in_array($name, self::PENDING_FARMER_ALLOW, true)) {
                    return redirect()
                        ->route('farmer.dashboard')
                        ->with('error', 'Your stall is waiting for admin approval. Products and orders unlock after approval.');
                }
            }
        }

        return $next($request);
    }
}
