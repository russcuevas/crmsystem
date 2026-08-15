<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CollegeTeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('college.login.page')
                ->with('error', 'Please login to access the College Faculty Portal.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is a teacher and belongs to College level
        $isCollegeTeacher = $user->role === 'teacher' 
            && $user->teacher 
            && $user->teacher->educationLevel 
            && in_array(strtoupper($user->teacher->educationLevel->code), ['COLLEGE', 'COL']);

        if (!$isCollegeTeacher) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('college.login.page')
                ->with('error', 'Unauthorized access. Only designated College faculty members are permitted to access this portal.');
        }

        return $next($request);
    }
}
