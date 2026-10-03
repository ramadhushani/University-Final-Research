<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectUser(Auth::user());
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Redirect user based on role.
     */
    protected function redirectUser($user)
    {
        return match($user->role) {
            'pm'        => redirect()->intended('/pm/dashboard'),
            'client'    => redirect()->intended('/client/dashboard'),
            'developer' => redirect()->intended('/developer/dashboard'),
            'designer'  => redirect()->intended('/designer/dashboard'),
            'qa'        => redirect()->intended('/qa/dashboard'),
            default     => tap(null, fn() => Auth::logout()) ?? abort(403, 'Unknown user role.'),
        };
    }
}
