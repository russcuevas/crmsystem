<?php

namespace App\Http\Controllers\junior_high_school;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\SchoolYear;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\EducationLevel;

class JuniorHighSchoolStudentController extends Controller
{
    public function JuniorHighSchoolStudentPage()
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

        // Get JHS EducationLevel ID
        $jhsLevel = EducationLevel::where('code', 'JHS')->first();
        $allGradeLevels = $jhsLevel ? GradeLevel::where('education_level_id', $jhsLevel->id)->get() : GradeLevel::all();

        // Handled Sections for this JHS Teacher
        $advisorySectionIds = $teacher->advisedClassSections()
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->pluck('id');

        $teachingSectionIds = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->pluck('class_section_id');

        $handledSectionIds = $advisorySectionIds->merge($teachingSectionIds)->unique();

        // Students ONLY in teacher's handled sections for active school year
        $handledStudentIds = Enrollment::whereIn('class_section_id', $handledSectionIds)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->pluck('student_id')
            ->unique();

        $students = Student::whereIn('id', $handledStudentIds)
            ->with(['user', 'educationLevel', 'gradeLevel', 'enrollments.classSection'])
            ->latest()
            ->get();

        $nextStudentId = 'STU-' . date('Y') . '-' . str_pad(Student::max('id') + 1, 4, '0', STR_PAD_LEFT);

        return view('junior_high_school.students.index', compact(
            'teacher',
            'students',
            'activeSchoolYear',
            'allGradeLevels',
            'jhsLevel',
            'nextStudentId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'student_number' => 'nullable|string|max:50|unique:students,student_number',
            'lrn' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female',
            'phone_number' => 'nullable|string|max:30',
        ]);

        $jhsLevel = EducationLevel::where('code', 'JHS')->first();

        DB::transaction(function () use ($validated, $jhsLevel) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'status' => 'active',
            ]);

            $studentNo = !empty($validated['student_number']) ? $validated['student_number'] : ('STU-' . date('Y') . '-' . str_pad(Student::max('id') + 1, 4, '0', STR_PAD_LEFT));

            Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNo,
                'lrn' => $validated['lrn'] ?? null,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'education_level_id' => $jhsLevel ? $jhsLevel->id : null,
                'grade_level_id' => $validated['grade_level_id'],
                'birthday' => $validated['birthday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'status' => 'Active',
            ]);
        });

        return redirect()->back()->with('success', 'Student added successfully!');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;

        // Check if student belongs to teacher's handled sections
        $advisorySectionIds = $teacher->advisedClassSections()->pluck('id');
        $teachingSectionIds = ClassSectionSubject::where('teacher_id', $teacher->id)->pluck('class_section_id');
        $handledSectionIds = $advisorySectionIds->merge($teachingSectionIds)->unique();

        $handledStudentIds = Enrollment::whereIn('class_section_id', $handledSectionIds)->pluck('student_id')->unique();

        if (!$handledStudentIds->contains($id)) {
            return redirect()->route('junior_high_school.students.page')
                ->with('error', 'Unauthorized: You can only view students in your handled sections.');
        }

        $student = Student::with(['user', 'educationLevel', 'gradeLevel', 'enrollments.classSection', 'enrollments.schoolYear'])->findOrFail($id);

        return view('junior_high_school.students.show', compact('student', 'teacher'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'student_number' => 'required|string|max:50|unique:students,student_number,' . $id,
            'lrn' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female',
            'phone_number' => 'nullable|string|max:30',
        ]);

        DB::transaction(function () use ($student, $validated) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            $userData = [
                'name' => $fullName,
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if ($student->user) {
                $student->user->update($userData);
            }

            $student->update([
                'student_number' => $validated['student_number'],
                'lrn' => $validated['lrn'] ?? null,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'grade_level_id' => $validated['grade_level_id'],
                'birthday' => $validated['birthday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Student information updated successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        DB::transaction(function () use ($student) {
            if ($student->user) {
                $student->user->delete();
            }
            $student->delete();
        });

        return redirect()->back()->with('success', 'Student record removed successfully!');
    }
}
