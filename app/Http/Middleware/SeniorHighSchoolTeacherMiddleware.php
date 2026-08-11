<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SeniorHighSchoolTeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('senior_high_school.login.page')
                ->with('error', 'Please login to access the Senior High School Teacher Portal.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is a teacher and belongs to Senior High School (SHS) level
        $isShsTeacher = $user->role === 'teacher' 
            && $user->teacher 
            && $user->teacher->educationLevel 
            && strtoupper($user->teacher->educationLevel->code) === 'SHS';

        if (!$isShsTeacher) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('senior_high_school.login.page')
                ->with('error', 'Unauthorized access. Only designated Senior High School (SHS) teachers are permitted to access this portal.');
        }

        return $next($request);
    }
}
