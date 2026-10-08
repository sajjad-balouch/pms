<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Session se locale check karein, default 'ur' ya 'en'
        $locale = Session::get('locale', config('app.locale', 'ur'));

        if (in_array($locale, ['en', 'ur'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}