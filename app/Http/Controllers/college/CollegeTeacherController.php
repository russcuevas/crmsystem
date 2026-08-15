<?php

namespace App\Http\Controllers\college;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassSectionSubject;
use App\Models\SchoolYear;
use App\Models\Enrollment;
use App\Models\Student;

class CollegeTeacherController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('college.login.page')->with('error', 'Teacher record not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $handledSubjectsQuery = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->with(['classSection.gradeLevel.educationLevel', 'classSection.course', 'subject'])
            ->whereHas('classSection.gradeLevel.educationLevel', function ($q) {
                $q->whereIn('code', ['COLLEGE', 'COL']);
            });

        if ($activeSchoolYear) {
            $handledSubjectsQuery->whereHas('classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id));
        }

        $handledSubjects = $handledSubjectsQuery->get();
        $handledSectionIds = $handledSubjects->pluck('class_section_id')->unique();

        $enrolledStudentsCount = Enrollment::whereIn('class_section_id', $handledSectionIds)
            ->distinct('student_id')
            ->count('student_id');

        return view('college.dashboard.index', compact(
            'teacher',
            'activeSchoolYear',
            'handledSubjects',
            'enrolledStudentsCount'
        ));
    }
}
