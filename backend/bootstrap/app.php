<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // RateLimiter::for('otp-verify', function (Request $request) {
        //     $session = (string) $request->input('session_id', '');
        //     return Limit::perMinute(10)->by($request->ip() . '|' . $session);
        // });


        // RateLimiter::for('otp', function (Request $request) {
        //     $session = (string) $request->input('session_id', '');
        //     return Limit::perMinute(3)->by($request->ip() . '|' . $session);
        // });
        $middleware->alias([
            'rbac' => \App\Http\Middleware\RbacMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
