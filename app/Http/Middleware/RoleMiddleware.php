<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Restrict access to users with one of the given roles.
     * Usage: middleware('role:admin') or middleware('role:farmer,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        if ($user->status !== 'active' && ! ($user->isFarmer() && $user->status === 'pending')) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Your account is not active.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
