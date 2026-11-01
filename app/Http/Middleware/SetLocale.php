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
        // ?lang=ur wala link (Google aur share kiye hue links) session se pehle chalta hai
        if (in_array($request->query('lang'), ['en', 'ur'], true)) {
            Session::put('locale', $request->query('lang'));
        }

        // Default English (config app.locale); user ka chuna hua locale session mein
        $locale = Session::get('locale', config('app.locale', 'en'));

        if (in_array($locale, ['en', 'ur'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}