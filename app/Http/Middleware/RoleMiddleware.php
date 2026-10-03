<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Accept a single role or multiple roles (e.g. role:developer,designer,qa)
        $allowed = $roles;
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $allowed = explode(',', $roles[0]);
        }

        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
