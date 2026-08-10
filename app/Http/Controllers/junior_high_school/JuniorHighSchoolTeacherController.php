<?php

namespace App\Http\Controllers\junior_high_school;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SchoolYear;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\Enrollment;

class JuniorHighSchoolTeacherController extends Controller
{
    public function JuniorHighSchoolDashboardPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            Auth::logout();
            return redirect()->route('junior_high_school.login.page')
                ->with('error', 'Teacher profile not found.');
        }

        $teacher->load(['educationLevel', 'user']);

        // Active School Year selection
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        // Advisory Sections for this teacher
        $advisorySectionsQuery = $teacher->advisedClassSections()->with(['gradeLevel', 'course', 'schoolYear']);
        if ($activeSchoolYear) {
            $advisorySectionsQuery->where('school_year_id', $activeSchoolYear->id);
        }
        $advisorySections = $advisorySectionsQuery->get();

        // Teaching Subjects assigned to this teacher
        $assignedSubjectsQuery = ClassSectionSubject::with(['classSection.gradeLevel', 'subject', 'classSection.schoolYear'])
            ->where('teacher_id', $teacher->id);
        
        if ($activeSchoolYear) {
            $assignedSubjectsQuery->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            });
        }
        $assignedSubjects = $assignedSubjectsQuery->get();

        // Calculate unique sections
        $sectionIds = collect();
        foreach ($advisorySections as $sec) {
            $sectionIds->push($sec->id);
        }
        foreach ($assignedSubjects as $subj) {
            if ($subj->classSection) {
                $sectionIds->push($subj->classSection->id);
            }
        }
        $uniqueSectionIds = $sectionIds->unique();

        // Count total students enrolled across teacher's sections
        $totalStudents = 0;
        if ($uniqueSectionIds->isNotEmpty()) {
            $totalStudents = Enrollment::whereIn('class_section_id', $uniqueSectionIds)
                ->where('status', 'enrolled')
                ->distinct('student_id')
                ->count('student_id');
        }

        return view('junior_high_school.dashboard.index', compact(
            'user',
            'teacher',
            'activeSchoolYear',
            'advisorySections',
            'assignedSubjects',
            'totalStudents'
        ));
    }
}
