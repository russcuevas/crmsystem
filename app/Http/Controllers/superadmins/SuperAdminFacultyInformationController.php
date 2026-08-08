<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;

class SuperAdminFacultyInformationController extends Controller
{
    public function SuperAdminFacultyInformationPage()
    {
        $teachers = Teacher::with(['user', 'educationLevel'])->latest()->paginate(10);
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.faculty_information.index', compact(
            'teachers',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }
}
