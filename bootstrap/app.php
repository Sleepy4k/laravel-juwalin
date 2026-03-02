<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ContentSecurityPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(static function(Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'csp'   => ContentSecurityPolicy::class,
        ]);

        $middleware->append(ContentSecurityPolicy::class);
    })
    ->withExceptions(static function(Exceptions $exceptions): void {})
    ->booted(static function(): void {
        RateLimiter::for('login', static fn(Request $request): Limit => Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip()));

        RateLimiter::for('api', static fn(Request $request): Limit => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('webhook', static fn(Request $request): Limit => Limit::perMinute(30)->by($request->ip()));
    })
    ->create();
