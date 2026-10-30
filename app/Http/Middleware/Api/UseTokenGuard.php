<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes the Sanctum token guard the default for API requests, so the existing code that uses
 * Auth::id() / Auth::user() / Tenant::ownerId() works the same for the mobile app.
 */
class UseTokenGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('sanctum');

        // Blocked / not-yet-approved accounts lose API access immediately
        $user = $request->user();
        if ($user && ! $user->is_approved && in_array($user->role, ['town_owner', 'agent'], true)) {
            $user->currentAccessToken()?->delete();

            return response()->json(['success' => false, 'message' => 'Your account is pending admin approval or has been blocked.'], 403);
        }

        return $next($request);
    }
}
