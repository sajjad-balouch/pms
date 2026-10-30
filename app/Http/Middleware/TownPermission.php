<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Team members (staff of a town owner) may only open the sections the owner gave them.
 * Registered as Livewire persistent middleware too, so component actions are checked as well.
 */
class TownPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'town_owner' || ! $user->owner_id) {
            return $next($request); // owners (and non town-owner routes) are not limited here
        }

        $section = Tenant::ROUTE_SECTIONS[$request->route()?->getName()] ?? null;

        if ($section === null || Tenant::can($section)) {
            return $next($request);
        }

        // Not allowed: send dashboard visits to the first allowed page, block the rest
        if ($section === 'dashboard' && ($home = Tenant::homeRoute())) {
            return redirect()->route($home);
        }

        abort(403, 'Aap ko is section ki ijazat nahi. Apne owner se rabta karein.');
    }
}
