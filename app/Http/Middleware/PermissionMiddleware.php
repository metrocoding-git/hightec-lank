<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Backup check ('auth' middleware normally catches this first).
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('login'));
        }

        $user = Auth::user();
        $routeName = $request->route()->getName(); // Get the current route name

        if (!$user->role || !$user->role->permissions->contains('permission_name', $routeName)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
