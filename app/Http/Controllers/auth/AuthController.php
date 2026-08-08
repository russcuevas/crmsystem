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
        return view('auth.admins.login');
    }

    public function ElementaryLoginPage()
    {
        return view('auth.elementary.login');
    }

    public function JuniorHighSchoolLoginPage()
    {
        return view('auth.junior_high_school.login');
    }

    public function SeniorHighSchoolLoginPage()
    {
        return view('auth.senior_high_school.login');
    }

    public function CollegeLoginPage()
    {
        return view('auth.college.login');
    }
}
