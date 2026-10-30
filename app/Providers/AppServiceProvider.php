<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mobile API limits. Named limiters keep separate counters (plain throttle:x,y
        // middlewares share one counter per user, so the strict ones tripped early).
        $by = fn (Request $r) => $r->user()?->id ?: $r->ip();
        RateLimiter::for('api', fn (Request $r) => Limit::perMinute(120)->by($by($r)));
        RateLimiter::for('api-auth', fn (Request $r) => Limit::perMinute(10)->by($r->ip()));
        RateLimiter::for('api-topup', fn (Request $r) => Limit::perMinute(10)->by($by($r)));
        RateLimiter::for('api-unlock', fn (Request $r) => Limit::perMinute(20)->by($by($r)));

        // Livewire script path
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/vendor/livewire/livewire.js', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) {
            return Route::post(
                parse_url(config('app.url'), PHP_URL_PATH) . '/livewire/update',
                $handle
            )->middleware('web');
        });

        // Team permission check also runs on Livewire actions (clicks), not only on page load
        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\TownPermission::class,
        ]);
    }
}