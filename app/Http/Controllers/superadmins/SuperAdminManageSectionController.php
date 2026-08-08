<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolYear;

class SuperAdminManageSectionController extends Controller
{
    public function SuperAdminManageSectionPage()
    {
        $sections = ClassSection::with(['schoolYear', 'gradeLevel', 'course', 'adviser'])->latest()->paginate(10);
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.sections.index', compact(
            'sections',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }
}
