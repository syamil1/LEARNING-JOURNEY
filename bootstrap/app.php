<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // ✅ MIDDLEWARE
    ->withMiddleware(function (Middleware $middleware) {

        // 🔥 AUTO REDIRECT kalau guest / session hilang
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->group('web', [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'checkID' => \App\Http\Middleware\CheckSSRole::class,
        ]);
    })

    // ✅ HANDLE 419 CSRF EXPIRED
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (TokenMismatchException $e, Request $request) {

            // kalau API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired'
                ], 419);
            }

            // kalau web
            return redirect()
                ->guest(route('login'))
                ->with('error', 'Session expired, silakan login kembali.');
        });

    })

    ->create();