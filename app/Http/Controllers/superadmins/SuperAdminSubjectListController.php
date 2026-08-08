<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\SchoolYear;

class SuperAdminSubjectListController extends Controller
{
    public function SuperAdminSubjectListPage()
    {
        $subjects = Subject::with(['educationLevel', 'course'])->latest()->paginate(10);
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.subject_list.index', compact(
            'subjects',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }
}
