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

class ElementaryStudentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        // Advisory sections
        $advisorySectionIds = ClassSection::where('class_adviser_id', $teacher->id)
            ->whereHas('gradeLevel.educationLevel', fn($q) => $q->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']))
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->pluck('id');

        // Subject sections
        $subjectSectionIds = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                $q->whereHas('gradeLevel.educationLevel', fn($sq) => $sq->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']));
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->pluck('class_section_id');

        $allSectionIds = $advisorySectionIds->merge($subjectSectionIds)->unique();

        $students = Student::whereHas('enrollments', function ($q) use ($allSectionIds, $activeSchoolYear) {
            $q->whereIn('class_section_id', $allSectionIds)
              ->whereIn('status', ['enrolled', 'active']);
            if ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            }
        })
        ->with(['user', 'gradeLevel', 'enrollments.classSection'])
        ->get();

        $advisoryStudentIds = Student::whereHas('enrollments', function ($q) use ($advisorySectionIds, $activeSchoolYear) {
            $q->whereIn('class_section_id', $advisorySectionIds)
              ->whereIn('status', ['enrolled', 'active']);
            if ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            }
        })->pluck('id')->toArray();

        return view('elementary.students.index', compact('students', 'advisoryStudentIds', 'activeSchoolYear'));
    }

    public function show($id)
    {
        $student = Student::with(['user', 'educationLevel', 'gradeLevel', 'enrollments.classSection.schoolYear'])
            ->findOrFail($id);

        return view('elementary.students.show', compact('student'));
    }
}
