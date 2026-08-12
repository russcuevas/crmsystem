<?php

namespace App\Http\Controllers\admins;

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

class AdminEnrollmentController extends Controller
{
    public function AdminEnrollmentPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();
        $allSchoolYears = SchoolYear::all();
        $schoolYears = $allSchoolYears;
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
            $enrollmentsQuery->where(function ($query) use ($selectedLevel) {
                $query->whereHas('gradeLevel.educationLevel', fn($q) => $q->where('code', $selectedLevel))
                      ->orWhereHas('student.educationLevel', fn($q) => $q->where('code', $selectedLevel))
                      ->orWhereHas('classSection.gradeLevel.educationLevel', fn($q) => $q->where('code', $selectedLevel));
            });
        }

        $enrollments = $enrollmentsQuery->latest()->get();

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
        $sectionsList = $classSections;

        $studentsQuery = Student::with(['user', 'educationLevel', 'gradeLevel', 'course']);
        if ($selectedLevel) {
            $studentsQuery->where(function ($q) use ($selectedLevel) {
                $q->whereHas('educationLevel', fn($el) => $el->where('code', $selectedLevel))
                  ->orWhereDoesntHave('educationLevel');
            });
        }
        $students = $studentsQuery->get();
        $studentsList = $students;

        $totalAccounts = User::whereNotIn('role', ['superadmin', 'admin'])->count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('admins.enrollment.index', compact(
            'enrollments',
            'classSections',
            'sectionsList',
            'students',
            'studentsList',
            'allSchoolYears',
            'schoolYears',
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

        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $validated['class_section_id'])
            ->where('school_year_id', $validated['school_year_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['student_id' => 'This student is already enrolled in the selected class section for this School Year!']);
        }

        Enrollment::create([
            'student_id' => $validated['student_id'],
            'class_section_id' => $validated['class_section_id'],
            'school_year_id' => $validated['school_year_id'],
            'grade_level_id' => $classSection->grade_level_id,
            'semester' => $semester,
            'status' => $validated['status'],
            'enrolled_at' => $validated['enrolled_at'] ?? now()->toDateString(),
        ]);

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
