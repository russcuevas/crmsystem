<?php

namespace App\Http\Controllers\admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;

class AdminStudentController extends Controller
{
    public function AdminStudentPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();
        $allGradeLevels = \App\Models\GradeLevel::all();
        $allCourses = \App\Models\Course::all();

        $studentsQuery = Student::with(['user', 'educationLevel', 'gradeLevel', 'course']);

        if ($selectedLevel) {
            $studentsQuery->where(function ($q) use ($selectedLevel) {
                $q->whereHas('educationLevel', fn($el) => $el->where('code', $selectedLevel))
                  ->orWhereHas('enrollments.gradeLevel.educationLevel', fn($el) => $el->where('code', $selectedLevel));
            });
        }

        $students = $studentsQuery->latest()->get();
        $nextStudentId = 'STU-' . date('Y') . '-' . str_pad(Student::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $nextStudentNumber = $nextStudentId;

        $totalAccounts = User::whereNotIn('role', ['superadmin', 'admin'])->count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('admins.students.index', compact(
            'students',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'selectedLevel',
            'educationLevelsList',
            'allGradeLevels',
            'allCourses',
            'nextStudentId',
            'nextStudentNumber'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'grade_level_id' => 'nullable|exists:grade_levels,id',
            'course_id' => 'nullable|exists:courses,id',
            'lrn' => 'nullable|string|max:50',
            'student_number' => 'nullable|string|max:50|unique:students,student_number',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Male,Female',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'status' => 'active',
            ]);

            $studentNo = $validated['student_number'] ?: ('STU-' . date('Y') . '-' . str_pad(Student::max('id') + 1, 4, '0', STR_PAD_LEFT));

            Student::create([
                'user_id' => $user->id,
                'education_level_id' => $validated['education_level_id'] ?? null,
                'grade_level_id' => $validated['grade_level_id'] ?? null,
                'course_id' => $validated['course_id'] ?? null,
                'lrn' => $validated['lrn'] ?? null,
                'student_number' => $studentNo,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
                'status' => 'Active',
            ]);
        });

        return redirect()->back()->with('success', 'Student registered successfully!');
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'grade_level_id' => 'nullable|exists:grade_levels,id',
            'course_id' => 'nullable|exists:courses,id',
            'lrn' => 'nullable|string|max:50',
            'student_number' => 'required|string|max:50|unique:students,student_number,' . $id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Male,Female',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($student, $validated) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            if ($student->user) {
                $userUpdate = [
                    'name' => $fullName,
                    'email' => $validated['email'],
                ];
                if (!empty($validated['password'])) {
                    $userUpdate['password'] = Hash::make($validated['password']);
                }
                $student->user->update($userUpdate);
            }

            $student->update([
                'education_level_id' => $validated['education_level_id'] ?? null,
                'grade_level_id' => $validated['grade_level_id'] ?? null,
                'course_id' => $validated['course_id'] ?? null,
                'lrn' => $validated['lrn'] ?? null,
                'student_number' => $validated['student_number'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Student details updated successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        DB::transaction(function () use ($student, $user) {
            $student->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }

    public function AdminStudentShowPage($id)
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $student = Student::with([
            'user',
            'educationLevel',
            'gradeLevel.educationLevel',
            'course',
            'enrollments.schoolYear',
            'enrollments.gradeLevel',
            'enrollments.classSection.adviser',
            'enrollments.attendances',
            'enrollments.grades'
        ])->findOrFail($id);

        $currentEnrollment = $activeSchoolYear 
            ? $student->enrollments->where('school_year_id', $activeSchoolYear->id)->first()
            : null;

        return view('admins.students.show', compact('student', 'currentEnrollment', 'activeSchoolYear'));
    }
}
