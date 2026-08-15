<?php

namespace App\Http\Controllers\college;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\ClassSectionSubject;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CollegeStudentController extends Controller
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

        $handledSubjects = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
                $q->whereHas('gradeLevel.educationLevel', fn($eq) => $eq->whereIn('code', ['COLLEGE', 'COL']));
            })->pluck('class_section_id')->unique();

        $studentsQuery = Student::with(['user', 'educationLevel', 'gradeLevel', 'course', 'enrollments.classSection'])
            ->whereHas('educationLevel', fn($q) => $q->whereIn('code', ['COLLEGE', 'COL']));

        $search = request('search');
        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('student_number', 'LIKE', '%' . $search . '%')
                  ->orWhere('first_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $search . '%');
            });
        }

        $students = $studentsQuery->latest()->get();

        $collegeLevel = EducationLevel::whereIn('code', ['COLLEGE', 'COL'])->first();
        $collegeCourses = Course::where('level', 'COLLEGE')->get();
        $collegeGradeLevels = $collegeLevel ? GradeLevel::where('education_level_id', $collegeLevel->id)->get() : collect();

        // Auto-generate next Student Number
        $latestStudent = Student::latest('id')->first();
        $nextIdNumber = $latestStudent ? ($latestStudent->id + 1) : 1;
        $nextStudentNumber = date('Y') . '-COL-' . str_pad($nextIdNumber, 4, '0', STR_PAD_LEFT);

        return view('college.students.index', compact(
            'students',
            'activeSchoolYear',
            'collegeLevel',
            'collegeCourses',
            'collegeGradeLevels',
            'nextStudentNumber'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required|string|unique:students,student_number',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'course_id' => 'required|exists:courses,id',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        $collegeLevel = EducationLevel::whereIn('code', ['COLLEGE', 'COL'])->first();

        // 1. Create User
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // 2. Create Student Profile
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => $validated['student_number'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'extension_name' => $validated['extension_name'] ?? null,
            'gender' => $validated['gender'],
            'birthday' => $validated['birthday'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'education_level_id' => $collegeLevel->id ?? null,
            'grade_level_id' => $validated['grade_level_id'],
            'course_id' => $validated['course_id'],
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
        ]);

        return redirect()->route('college.students.page')
            ->with('success', 'Student ' . $student->first_name . ' ' . $student->last_name . ' registered successfully!');
    }
}
