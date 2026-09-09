<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->web(append: [EnsureAccountIsActive::class]);
        // Plain, non-sensitive UI preference set directly by client JS
        // (document.cookie), not through a Laravel response — it isn't in
        // Laravel's encrypted format, so it must be exempted or every
        // request() ->cookie('tema') read silently comes back null.
        $middleware->encryptCookies(except: ['tema', 'sidebar']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
