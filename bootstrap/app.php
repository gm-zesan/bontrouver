<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureNotSuspended;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            EnsureNotSuspended::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'webhook/stripe',
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Redirect back or return JSON for payload too large violations
        $exceptions->renderable(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'The total upload size is too large. Please select fewer or smaller images.',
                ], 413);
            }
            return back()->with('error', 'The uploaded files are too large. Please select fewer or smaller images.');
        });

        // Redirect back with user-friendly message for domain rule violations
        $exceptions->renderable(function (\App\Exceptions\CompanionshipException $e, Request $request) {
            if (!$request->expectsJson()) {
                return back()->with('error', $e->getMessage());
            }
        });
    })->create();
