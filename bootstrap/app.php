<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminOrUserRedirect;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // mobile app API (/api/v1)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Web middleware group mein SetLocale append karein (yeh automatically session ke baad execute hoga)
        $middleware->web(append: [
            SetLocale::class,
            \App\Http\Middleware\TrackPageViews::class,
        ]);

        // Route Aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'admin' => AdminMiddleware::class,
            'town.can' => \App\Http\Middleware\TownPermission::class,
            // Mobile API
            'api.token' => \App\Http\Middleware\Api\UseTokenGuard::class,
            'api.locale' => \App\Http\Middleware\Api\SetApiLocale::class,
            'api.role' => \App\Http\Middleware\Api\RequireRole::class,
            'api.section' => \App\Http\Middleware\Api\RequireTownSection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Mobile API always gets JSON (never an HTML error page or a redirect to /login)
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(function (\Throwable $e, $request) {
            if (! $request->is('api/*')) {
                return null; // web keeps Laravel's normal handling
            }

            [$status, $message, $errors] = match (true) {
                $e instanceof \Illuminate\Validation\ValidationException => [422, $e->getMessage(), $e->errors()],
                $e instanceof \Illuminate\Auth\AuthenticationException => [401, 'Unauthenticated. Please log in again.', null],
                $e instanceof \Illuminate\Auth\Access\AuthorizationException => [403, $e->getMessage() ?: 'Forbidden.', null],
                $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => [404, 'Not found.', null],
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface => [$e->getStatusCode(), $e->getMessage() ?: 'Request failed.', null],
                default => [500, config('app.debug') ? $e->getMessage() : 'Server error. Please try again.', null],
            };

            if ($status === 500) {
                report($e);
            }

            return response()->json(array_filter([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], fn ($v) => $v !== null), $status);
        });
    })->create();