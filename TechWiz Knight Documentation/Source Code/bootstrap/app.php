<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->appendToGroup('api', [
            \App\Http\Middleware\SecurityHeaders::class,
            'throttle:60,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Never show the bare "419 Page Expired" screen — soft-recover instead
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Session expired. Please try again.',
                    'reload' => true,
                ], 419);
            }

            $back = url()->previous();
            if (! $back || $back === $request->fullUrl()) {
                $back = route('home');
            }

            return redirect()
                ->to($back)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('status', 'Session refreshed. Please submit again.');
        });
    })->create();
