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

class SuperAdminDashboardController extends Controller
{
    public function SuperAdminDashboardPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();

        $totalAccounts = User::count();

        if ($activeSchoolYear) {
            $facultyQuery = Teacher::where(function ($query) use ($activeSchoolYear) {
                $query->whereHas('advisedClassSections', fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
                      ->orWhereHas('classSectionSubjects.classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id));
            });

            $studentsQuery = Student::whereHas('enrollments', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            });

            $sectionsQuery = ClassSection::where('school_year_id', $activeSchoolYear->id);

            $subjectsQuery = Subject::query();

            $usersQuery = User::where(function ($q) use ($activeSchoolYear) {
                $q->whereHas('student.enrollments', fn($eq) => $eq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhereHas('teacher.advisedClassSections', fn($sq) => $sq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhereHas('teacher.classSectionSubjects.classSection', fn($sq) => $sq->where('school_year_id', $activeSchoolYear->id))
                  ->orWhere('role', 'superadmin');
            });

            if ($selectedLevel) {
                $facultyQuery->whereHas('educationLevel', fn($q) => $q->where('code', $selectedLevel));
                $studentsQuery->whereHas('enrollments.gradeLevel.educationLevel', fn($q) => $q->where('code', $selectedLevel));
                $sectionsQuery->whereHas('gradeLevel.educationLevel', fn($q) => $q->where('code', $selectedLevel));
                $subjectsQuery->whereHas('educationLevel', fn($q) => $q->where('code', $selectedLevel));
                $usersQuery->where(function ($q) use ($selectedLevel) {
                    $q->whereHas('student.enrollments.gradeLevel.educationLevel', fn($el) => $el->where('code', $selectedLevel))
                      ->orWhereHas('teacher.educationLevel', fn($el) => $el->where('code', $selectedLevel))
                      ->orWhere('role', 'superadmin');
                });
            }

            $totalFaculty = $facultyQuery->distinct()->count();
            $totalStudents = $studentsQuery->distinct()->count();
            $totalSections = $sectionsQuery->count();
            $totalSubjects = $subjectsQuery->count();

            $recentStudents = $studentsQuery->latest()->take(6)->get();
            $recentTeachers = $facultyQuery->with('educationLevel')->latest()->take(6)->get();
            $recentUsers = $usersQuery->latest()->take(6)->get();

            $educationLevels = EducationLevel::withCount(['gradeLevels', 'subjects'])->get();
        } else {
            $totalFaculty = Teacher::count();
            $totalStudents = Student::count();
            $totalSubjects = Subject::count();
            $totalSections = ClassSection::count();
            $recentStudents = Student::latest()->take(6)->get();
            $recentTeachers = Teacher::with('educationLevel')->latest()->take(6)->get();
            $recentUsers = User::latest()->take(6)->get();
            $educationLevels = EducationLevel::withCount(['gradeLevels', 'subjects'])->get();
        }

        return view('superadmins.dashboard.index', compact(
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'recentUsers',
            'recentStudents',
            'recentTeachers',
            'educationLevels',
            'selectedLevel',
            'educationLevelsList'
        ));
    }
}
