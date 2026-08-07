<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function SuperAdminLoginPage()
    {
        return view('auth.superadmins.login');
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
