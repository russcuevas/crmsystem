<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\GradeLevel;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;

class SuperAdminEnrollmentController extends Controller
{
    public function SuperAdminEnrollmentPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();
        $allSchoolYears = SchoolYear::all();
        $allGradeLevels = GradeLevel::with('educationLevel')->get();

        $enrollmentsQuery = Enrollment::with([
            'student.user',
            'student.educationLevel',
            'student.gradeLevel',
            'student.course',
            'classSection.gradeLevel.educationLevel',
            'classSection.course',
            'schoolYear',
            'gradeLevel.educationLevel'
        ]);

        if ($activeSchoolYear) {
            $enrollmentsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($selectedLevel) {
            $enrollmentsQuery->whereHas('gradeLevel.educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }

        $enrollments = $enrollmentsQuery->latest()->get();

        // Get Available Class Sections for current S.Y. and Level filter
        $sectionsQuery = ClassSection::with(['gradeLevel.educationLevel', 'course']);
        if ($activeSchoolYear) {
            $sectionsQuery->where('school_year_id', $activeSchoolYear->id);
        }
        if ($selectedLevel) {
            $sectionsQuery->whereHas('gradeLevel.educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }
        $classSections = $sectionsQuery->get();

        // Get All Students with User & Levels
        $students = Student::with(['user', 'educationLevel', 'gradeLevel', 'course'])->get();

        // System overview statistics
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.enrollment.index', compact(
            'enrollments',
            'classSections',
            'students',
            'allSchoolYears',
            'allGradeLevels',
            'activeSchoolYear',
            'selectedLevel',
            'educationLevelsList',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'school_year_id' => 'required|exists:school_years,id',
            'status' => 'required|string|in:active,inactive,completed,dropped,transferred',
            'enrolled_at' => 'nullable|date',
            'semester' => 'nullable|string|max:50',
        ]);

        $classSection = ClassSection::with('gradeLevel.educationLevel')->findOrFail($validated['class_section_id']);
        $student = Student::findOrFail($validated['student_id']);

        $edCode = strtoupper($classSection->gradeLevel->educationLevel->code ?? '');
        $semester = in_array($edCode, ['SHS', 'COLLEGE'])
            ? ($validated['semester'] ?? '1st Semester')
            : '1st - 4th Quarter';

        // Check for duplicate enrollment in same section and school year
        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $validated['class_section_id'])
            ->where('school_year_id', $validated['school_year_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['student_id' => 'This student is already enrolled in the selected class section for this School Year!']);
        }

        // Create Enrollment Record
        Enrollment::create([
            'student_id' => $validated['student_id'],
            'class_section_id' => $validated['class_section_id'],
            'school_year_id' => $validated['school_year_id'],
            'grade_level_id' => $classSection->grade_level_id,
            'semester' => $semester,
            'status' => $validated['status'],
            'enrolled_at' => $validated['enrolled_at'] ?? now()->toDateString(),
        ]);

        // Sync Student profile with enrolled Section Level & Course
        $student->update([
            'education_level_id' => $classSection->gradeLevel->education_level_id,
            'grade_level_id' => $classSection->grade_level_id,
            'course_id' => $classSection->course_id,
        ]);

        return redirect()->back()->with('success', 'Student enrolled successfully!');
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'school_year_id' => 'required|exists:school_years,id',
            'status' => 'required|string|in:active,inactive,completed,dropped,transferred',
            'enrolled_at' => 'nullable|date',
            'semester' => 'nullable|string|max:50',
        ]);

        $classSection = ClassSection::with('gradeLevel.educationLevel')->findOrFail($validated['class_section_id']);
        $edCode = strtoupper($classSection->gradeLevel->educationLevel->code ?? '');
        $semester = in_array($edCode, ['SHS', 'COLLEGE'])
            ? ($validated['semester'] ?? '1st Semester')
            : '1st - 4th Quarter';

        $enrollment->update([
            'class_section_id' => $validated['class_section_id'],
            'school_year_id' => $validated['school_year_id'],
            'grade_level_id' => $classSection->grade_level_id,
            'semester' => $semester,
            'status' => $validated['status'],
            'enrolled_at' => $validated['enrolled_at'] ?? $enrollment->enrolled_at,
        ]);

        // Sync Student profile
        if ($enrollment->student) {
            $enrollment->student->update([
                'education_level_id' => $classSection->gradeLevel->education_level_id,
                'grade_level_id' => $classSection->grade_level_id,
                'course_id' => $classSection->course_id,
            ]);
        }

        return redirect()->back()->with('success', 'Enrollment record updated successfully!');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->back()->with('success', 'Enrollment record deleted successfully!');
    }
}
