<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Str; // Assuming Str is still needed for other parts of the controller

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        // This method is typically used by the AuthenticatesUsers trait.
        // Since we are implementing custom login, we will handle redirection in the login method.
        // However, if other parts of the application rely on this, it can be kept.
        // For now, we'll keep a basic version.
        return '/home';
    }

    public function index()
    {
        return view('auth.login');
    }

    public function recoveryPassword()
    {
        return view('auth.recoverypassword');
    }

    public function register()
    {
        return view('template.auth.register');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        // Attempt to authenticate the user with email, password, and role_id
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $user->role_id,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirect all authenticated users to the main dashboard (generic path)
            return redirect()->intended('/dashboard');
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
