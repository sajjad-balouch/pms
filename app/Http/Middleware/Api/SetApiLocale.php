<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

// Mobile app picks the language with "Accept-Language: ur" (default English)
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $lang = strtolower(substr((string) $request->header('Accept-Language', 'en'), 0, 2));
        App::setLocale($lang === 'ur' ? 'ur' : 'en');

        return $next($request);
    }
}
