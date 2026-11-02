<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs out a user who is deactivated while still logged in (users.status
 * = 0), on their very next request. LoginController blocks new logins;
 * this covers sessions that were already open.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !(bool) $user->status) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'This account has been deactivated.'], 401);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'This account has been deactivated. Please contact the administrator.']);
        }

        return $next($request);
    }
}
