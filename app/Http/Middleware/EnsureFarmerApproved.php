<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmerApproved
{
    /**
     * Farmers must be admin-approved before managing listings / receiving orders publicly.
     * Profile editing remains allowed while pending.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $profile = $user?->farmerProfile;

        if (! $profile || $profile->approval_status !== 'approved') {
            return redirect()
                ->route('farmer.dashboard')
                ->with('warning', 'Your farmer account is pending admin approval. Product and order tools unlock after approval.');
        }

        return $next($request);
    }
}
