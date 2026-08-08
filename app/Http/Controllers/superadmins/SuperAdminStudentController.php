<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;

class SuperAdminStudentController extends Controller
{
    public function SuperAdminStudentPage()
    {
        $students = Student::with('user')->latest()->paginate(10);
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.students.index', compact(
            'students',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }
}
