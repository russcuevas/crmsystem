<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function SuperAdminLoginPage()
    {
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard.page');
        }

        return view('auth.superadmins.login');
    }

    public function SuperAdminLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->role === 'superadmin') {
                $user->last_login_at = now();
                $user->save();

                $request->session()->regenerate();

                return redirect()->intended(route('superadmin.dashboard.page'))
                    ->with('success', 'Welcome back, Super Admin!');
            }

            Auth::logout();
            return redirect()->route('superadmin.login.page')
                ->with('error', 'Unauthorized access. Only Super Admin accounts are permitted here.');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email address or password.');
    }

    public function SuperAdminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login.page')
            ->with('success', 'You have been logged out successfully.');
    }

    public function AdminLoginPage()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect()->route('admin.dashboard.page');
        }

        return view('auth.admins.login');
    }

    public function AdminLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (in_array($user->role, ['admin', 'superadmin'])) {
                $user->last_login_at = now();
                $user->save();

                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard.page'))
                    ->with('success', 'Welcome back, Admin!');
            }

            Auth::logout();
            return redirect()->route('admin.login.page')
                ->with('error', 'Unauthorized access. Only Admin accounts are permitted here.');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email address or password.');
    }

    public function AdminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.page')
            ->with('success', 'You have been logged out successfully.');
    }

    public function ElementaryLoginPage()
    {
        return view('auth.elementary.login');
    }

    public function JuniorHighSchoolLoginPage()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->role === 'teacher' && $user->teacher && $user->teacher->educationLevel && strtoupper($user->teacher->educationLevel->code) === 'JHS') {
                return redirect()->route('junior_high_school.dashboard.page');
            }
        }

        return view('auth.junior_high_school.login');
    }

    public function JuniorHighSchoolLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $isJhsTeacher = $user->role === 'teacher' 
                && $user->teacher 
                && $user->teacher->educationLevel 
                && strtoupper($user->teacher->educationLevel->code) === 'JHS';

            if ($isJhsTeacher) {
                $user->last_login_at = now();
                $user->save();

                $request->session()->regenerate();

                $teacherName = $user->teacher ? ($user->teacher->first_name . ' ' . $user->teacher->last_name) : $user->name;

                return redirect()->intended(route('junior_high_school.dashboard.page'))
                    ->with('success', 'Welcome back, Teacher ' . $teacherName . '!');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('junior_high_school.login.page')
                ->with('error', 'Unauthorized access. Only designated Junior High School (JHS) teachers are permitted to log in here.');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email address or password.');
    }

    public function JuniorHighSchoolLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('junior_high_school.login.page')
            ->with('success', 'You have been logged out successfully.');
    }

    public function SeniorHighSchoolLoginPage()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->role === 'teacher' && $user->teacher && $user->teacher->educationLevel && strtoupper($user->teacher->educationLevel->code) === 'SHS') {
                return redirect()->route('senior_high_school.dashboard.page');
            }
        }

        return view('auth.senior_high_school.login');
    }

    public function SeniorHighSchoolLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $isShsTeacher = $user->role === 'teacher' 
                && $user->teacher 
                && $user->teacher->educationLevel 
                && strtoupper($user->teacher->educationLevel->code) === 'SHS';

            if ($isShsTeacher) {
                $user->last_login_at = now();
                $user->save();

                $request->session()->regenerate();

                $teacherName = $user->teacher ? ($user->teacher->first_name . ' ' . $user->teacher->last_name) : $user->name;

                return redirect()->intended(route('senior_high_school.dashboard.page'))
                    ->with('success', 'Welcome back, Teacher ' . $teacherName . '!');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('senior_high_school.login.page')
                ->with('error', 'Unauthorized access. Only designated Senior High School (SHS) teachers are permitted to log in here.');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email address or password.');
    }

    public function SeniorHighSchoolLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('senior_high_school.login.page')
            ->with('success', 'You have been logged out successfully.');
    }

    public function CollegeLoginPage()
    {
        return view('auth.college.login');
    }
}
