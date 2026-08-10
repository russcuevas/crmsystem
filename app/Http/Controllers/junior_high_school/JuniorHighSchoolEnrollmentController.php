<?php

namespace App\Http\Controllers\junior_high_school;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\GradeLevel;

class JuniorHighSchoolEnrollmentController extends Controller
{
    public function JuniorHighSchoolEnrollmentPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('junior_high_school.login.page')->with('error', 'Teacher profile not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $jhsLevel = EducationLevel::where('code', 'JHS')->first();

        // Enrollments for current active school year and JHS level
        $enrollmentsQuery = Enrollment::with([
            'student.user',
            'student.gradeLevel',
            'classSection.gradeLevel',
            'schoolYear',
            'gradeLevel'
        ]);

        if ($activeSchoolYear) {
            $enrollmentsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($jhsLevel) {
            $enrollmentsQuery->where(function ($q) use ($jhsLevel) {
                $q->whereHas('gradeLevel', fn($g) => $g->where('education_level_id', $jhsLevel->id))
                  ->orWhereHas('classSection.gradeLevel', fn($g) => $g->where('education_level_id', $jhsLevel->id))
                  ->orWhereHas('student', fn($s) => $s->where('education_level_id', $jhsLevel->id));
            });
        }

        $enrollments = $enrollmentsQuery->latest()->get();

        // ALL Students for JHS Level available for enrollment (per user rule: "except sa enrollment kita lahat ng student")
        $allJhsStudents = Student::with(['user', 'gradeLevel'])
            ->where(function ($q) use ($jhsLevel) {
                if ($jhsLevel) {
                    $q->where('education_level_id', $jhsLevel->id)
                      ->orWhereHas('gradeLevel', fn($gl) => $gl->where('education_level_id', $jhsLevel->id));
                }
            })
            ->latest()
            ->get();

        // If no specific JHS filtered students found, show all students so teacher can enroll any student
        if ($allJhsStudents->isEmpty()) {
            $allJhsStudents = Student::with(['user', 'gradeLevel'])->latest()->get();
        }

        // Active JHS Class Sections for current School Year
        $sectionsQuery = ClassSection::with(['gradeLevel', 'course']);
        if ($activeSchoolYear) {
            $sectionsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($jhsLevel) {
            $sectionsQuery->whereHas('gradeLevel', fn($g) => $g->where('education_level_id', $jhsLevel->id));
        }

        $availableSections = $sectionsQuery->get();
        $allGradeLevels = $jhsLevel ? GradeLevel::where('education_level_id', $jhsLevel->id)->get() : GradeLevel::all();

        return view('junior_high_school.enrollment.index', compact(
            'teacher',
            'enrollments',
            'allJhsStudents',
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

        return redirect()->back()->with('success', 'Student enrolled successfully into section!');
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
