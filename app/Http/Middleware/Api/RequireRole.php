<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Usage: ->middleware('api.role:agent,town_owner')
class RequireRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated. Please log in again.'], 401);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json(['success' => false, 'message' => 'This section is not available for your account type.'], 403);
        }

        return $next($request);
    }
}
