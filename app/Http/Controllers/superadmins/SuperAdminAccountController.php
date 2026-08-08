<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;

class SuperAdminAccountController extends Controller
{
    public function SuperAdminAccountPage()
    {
        $users = User::latest()->paginate(10);
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.accounts.index', compact(
            'users',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }
}
