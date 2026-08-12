<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login.page')->with('error', 'Please login to access the Admin portal.');
        }

        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Unauthorized access. Only Admin users are allowed.');
        }

        return $next($request);
    }
}
