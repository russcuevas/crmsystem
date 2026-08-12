<?php

namespace App\Http\Controllers\elementary;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementaryTeacherController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        // Advisory sections
        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->whereHas('gradeLevel.educationLevel', fn($q) => $q->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']))
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['gradeLevel', 'schoolYear'])
            ->get();

        $advisorySectionIds = $advisorySections->pluck('id');

        // Subject sections assigned
        $assignedSectionSubjects = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                $q->whereHas('gradeLevel.educationLevel', fn($sq) => $sq->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']));
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })
            ->with(['classSection.gradeLevel', 'subject'])
            ->get();

        $subjectSectionIds = $assignedSectionSubjects->pluck('class_section_id');
        $allSectionIds = $advisorySectionIds->merge($subjectSectionIds)->unique();

        // Students in teacher's sections
        $studentsCount = Student::whereHas('enrollments', function ($q) use ($allSectionIds, $activeSchoolYear) {
            $q->whereIn('class_section_id', $allSectionIds)
              ->whereIn('status', ['enrolled', 'active']);
            if ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            }
        })->count();

        // Recent enrollments
        $recentEnrollments = Enrollment::whereIn('class_section_id', $allSectionIds)
            ->whereIn('status', ['enrolled', 'active'])
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['student.user', 'classSection.gradeLevel'])
            ->latest()
            ->take(5)
            ->get();

        return view('elementary.dashboard.index', compact(
            'teacher',
            'advisorySections',
            'assignedSectionSubjects',
            'studentsCount',
            'recentEnrollments',
            'activeSchoolYear'
        ));
    }
}
