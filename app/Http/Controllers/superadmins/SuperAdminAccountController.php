<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;

class SuperAdminAccountController extends Controller
{
    public function SuperAdminAccountPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();

        $usersQuery = User::query();
        if ($activeSchoolYear) {
            $usersQuery->where(function ($q) use ($activeSchoolYear) {
                $q->whereHas('student.enrollments', fn($eq) => $eq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhereHas('teacher.advisedClassSections', fn($sq) => $sq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhereHas('teacher.classSectionSubjects.classSection', fn($sq) => $sq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhere('role', 'superadmin');
            });
        }

        if ($selectedLevel) {
            $usersQuery->where(function ($q) use ($selectedLevel) {
                $q->whereHas('student.enrollments.gradeLevel.educationLevel', fn($el) => $el->where('code', $selectedLevel))
                  ->orWhereHas('teacher.educationLevel', fn($el) => $el->where('code', $selectedLevel))
                  ->orWhere('role', 'superadmin');
            });
        }

        $users = $usersQuery->latest()->paginate(10);
        $totalAccounts = User::count();

        if ($activeSchoolYear) {
            $totalFaculty = Teacher::whereHas('advisedClassSections', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->orWhereHas('classSectionSubjects.classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();

            $totalStudents = Student::whereHas('enrollments', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();

            $totalSections = ClassSection::where('school_year_id', $activeSchoolYear->id)->count();

            $totalSubjects = Subject::whereHas('classSectionSubjects.classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();
        } else {
            $totalFaculty = Teacher::count();
            $totalStudents = Student::count();
            $totalSubjects = Subject::count();
            $totalSections = ClassSection::count();
        }

        return view('superadmins.accounts.index', compact(
            'users',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'selectedLevel',
            'educationLevelsList'
        ));
    }
}
