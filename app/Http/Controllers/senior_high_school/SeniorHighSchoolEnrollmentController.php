<?php

namespace App\Http\Controllers\senior_high_school;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\GradeLevel;

class SeniorHighSchoolEnrollmentController extends Controller
{
    public function SeniorHighSchoolEnrollmentPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('senior_high_school.login.page')->with('error', 'Teacher profile not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $shsLevel = EducationLevel::where('code', 'SHS')->first();

        // Enrollments for current active school year and SHS level
        $enrollmentsQuery = Enrollment::with([
            'student.user',
            'student.gradeLevel',
            'student.course',
            'classSection.gradeLevel',
            'classSection.course',
            'schoolYear',
            'gradeLevel'
        ]);

        if ($activeSchoolYear) {
            $enrollmentsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($shsLevel) {
            $enrollmentsQuery->where(function ($q) use ($shsLevel) {
                $q->whereHas('gradeLevel', fn($g) => $g->where('education_level_id', $shsLevel->id))
                  ->orWhereHas('classSection.gradeLevel', fn($g) => $g->where('education_level_id', $shsLevel->id))
                  ->orWhereHas('student', fn($s) => $s->where('education_level_id', $shsLevel->id));
            });
        }

        $enrollments = $enrollmentsQuery->latest()->get();

        // All Students for SHS Level available for enrollment
        $allShsStudents = Student::with(['user', 'gradeLevel', 'course'])
            ->where(function ($q) use ($shsLevel) {
                if ($shsLevel) {
                    $q->where('education_level_id', $shsLevel->id)
                      ->orWhereHas('gradeLevel', fn($gl) => $gl->where('education_level_id', $shsLevel->id));
                }
            })
            ->latest()
            ->get();

        if ($allShsStudents->isEmpty()) {
            $allShsStudents = Student::with(['user', 'gradeLevel', 'course'])->latest()->get();
        }

        // Active SHS Class Sections for current School Year
        $sectionsQuery = ClassSection::with(['gradeLevel', 'course']);
        if ($activeSchoolYear) {
            $sectionsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($shsLevel) {
            $sectionsQuery->whereHas('gradeLevel', fn($g) => $g->where('education_level_id', $shsLevel->id));
        }

        $availableSections = $sectionsQuery->get();
        $allGradeLevels = $shsLevel ? GradeLevel::where('education_level_id', $shsLevel->id)->get() : GradeLevel::all();

        return view('senior_high_school.enrollment.index', compact(
            'teacher',
            'enrollments',
            'allShsStudents',
            'availableSections',
            'activeSchoolYear',
            'allGradeLevels'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'status' => 'required|in:enrolled,pending,dropped,transferred',
        ]);

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $classSection = ClassSection::findOrFail($validated['class_section_id']);
        $student = Student::findOrFail($validated['student_id']);

        // Check duplicate enrollment for same section in same school year
        $existing = Enrollment::where('student_id', $student->id)
            ->where('class_section_id', $classSection->id)
            ->where('school_year_id', $activeSchoolYear->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Student is already enrolled in this class section for the current school year.');
        }

        Enrollment::create([
            'student_id' => $student->id,
            'class_section_id' => $classSection->id,
            'school_year_id' => $activeSchoolYear->id,
            'grade_level_id' => $classSection->grade_level_id,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Senior High School student enrolled successfully into section!');
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'status' => 'required|in:enrolled,pending,dropped,transferred',
        ]);

        $classSection = ClassSection::findOrFail($validated['class_section_id']);

        $enrollment->update([
            'class_section_id' => $classSection->id,
            'grade_level_id' => $classSection->grade_level_id,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Enrollment record updated successfully!');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->back()->with('success', 'Enrollment record removed successfully!');
    }
}
