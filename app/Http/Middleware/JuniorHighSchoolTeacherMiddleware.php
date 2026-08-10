<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JuniorHighSchoolTeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('junior_high_school.login.page')
                ->with('error', 'Please login to access the Junior High School Teacher Portal.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is a teacher and belongs to Junior High School (JHS) level
        $isJhsTeacher = $user->role === 'teacher' 
            && $user->teacher 
            && $user->teacher->educationLevel 
            && strtoupper($user->teacher->educationLevel->code) === 'JHS';

        if (!$isJhsTeacher) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('junior_high_school.login.page')
                ->with('error', 'Unauthorized access. Only designated Junior High School (JHS) teachers are permitted to access this portal.');
        }

        return $next($request);
    }
}
