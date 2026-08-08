<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('superadmin.login.page')->with('error', 'Please login to access the Super Admin portal.');
        }

        if (Auth::user()->role !== 'superadmin') {
            Auth::logout();
            return redirect()->route('superadmin.login.page')->with('error', 'Unauthorized access. Only Super Admin is allowed.');
        }

        return $next($request);
    }
}
