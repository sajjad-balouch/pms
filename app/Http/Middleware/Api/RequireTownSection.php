<?php

namespace App\Http\Middleware\Api;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Town owner team permissions for the API. Usage: ->middleware('api.section:recovery')
class RequireTownSection
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        // Only town owners (and their team) have sections; agents etc. are limited by api.role instead
        if ($request->user()?->role === 'town_owner' && ! Tenant::can($section)) {
            return response()->json(['success' => false, 'message' => 'You do not have permission for this section. Please contact your owner.'], 403);
        }

        return $next($request);
    }
}
