<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\RoleMiddleware;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // dd(auth()->user());
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Check if user has required role
        if (! in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        // Check approval for Town Owners and Agents
        if (in_array($request->user()->role, ['town_owner', 'agent']) && ! $request->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')->with('status', 'Your account is pending admin approval.');
        }

        return $next($request);
    }
}