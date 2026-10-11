<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);

        // Not logged in / session expired -> login page.
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Already logged in and opens /login -> dashboard.
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        // No browser cache for app pages (Back button after logout).
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 419 "Page Expired" (CSRF token died with the session) -> login page.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Please log in again.'], 419);
            }

            return redirect()->route('login')
                ->with('toast_message', ['type' => 'warning', 'message' => 'Your session expired. Please log in again.']);
        });
    })->create();
