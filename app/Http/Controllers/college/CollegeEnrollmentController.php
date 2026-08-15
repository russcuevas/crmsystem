<?php

namespace App\Http\Controllers\college;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\SchoolYear;
use App\Models\EducationLevel;

class CollegeEnrollmentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        // Get class sections handled by this instructor
        $handledSectionIds = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
                $q->whereHas('gradeLevel.educationLevel', fn($eq) => $eq->whereIn('code', ['COLLEGE', 'COL']));
            })->pluck('class_section_id')->unique();

        $sections = ClassSection::whereIn('id', $handledSectionIds)->with(['gradeLevel', 'course'])->get();

        $enrollments = Enrollment::whereIn('class_section_id', $handledSectionIds)
            ->with(['student.user', 'student.course', 'classSection.course', 'schoolYear'])
            ->latest()
            ->get();

        $collegeLevel = EducationLevel::whereIn('code', ['COLLEGE', 'COL'])->first();
        $students = Student::where('education_level_id', $collegeLevel->id ?? 0)->get();

        return view('college.enrollment.index', compact(
            'enrollments',
            'sections',
            'students',
            'activeSchoolYear'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_id' => 'required|exists:class_sections,id',
        ]);

        $section = ClassSection::findOrFail($validated['class_section_id']);
        $student = Student::findOrFail($validated['student_id']);

        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $validated['class_section_id'])
            ->where('school_year_id', $section->school_year_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Student is already enrolled in this class section.');
        }

        Enrollment::create([
            'student_id' => $validated['student_id'],
            'class_section_id' => $validated['class_section_id'],
            'school_year_id' => $section->school_year_id,
            'grade_level_id' => $student->grade_level_id ?? $section->grade_level_id,
            'status' => 'Enrolled',
        ]);

        return redirect()->route('college.enrollment.page')
            ->with('success', 'Student ' . $student->first_name . ' ' . $student->last_name . ' successfully enrolled in ' . $section->section_name . '!');
    }
}
