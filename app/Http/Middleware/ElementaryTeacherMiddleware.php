<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ElementaryTeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('elementary.login.page')
                ->with('error', 'Please login to access the Basic Education (BED / Elementary) Teacher Portal.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is a teacher and belongs to Basic Education (BED / Elementary) level
        $isBedTeacher = $user->role === 'teacher' 
            && $user->teacher 
            && $user->teacher->educationLevel 
            && in_array(strtoupper($user->teacher->educationLevel->code), ['BED', 'ELEM', 'ELEMENTARY']);

        if (!$isBedTeacher) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('elementary.login.page')
                ->with('error', 'Unauthorized access. Only designated Basic Education (BED / Elementary) teachers are permitted to access this portal.');
        }

        return $next($request);
    }
}
