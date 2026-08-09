<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;

class SuperAdminFacultyInformationController extends Controller
{
    public function SuperAdminFacultyInformationPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $search = request('search');
        $educationLevelsList = EducationLevel::all();

        $teachersQuery = Teacher::with(['user', 'educationLevel']);

        if ($selectedLevel) {
            $teachersQuery->whereHas('educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }

        if ($search) {
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('teacher_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', "%{$search}%"));
            });
        }

        $teachers = $teachersQuery->latest()->get();

        // Preview for next auto-generated teacher ID
        $nextTeacherId = 'TCH-' . date('Y') . '-' . str_pad(Teacher::max('id') + 1, 4, '0', STR_PAD_LEFT);

        $totalAccounts = User::count();

        if ($activeSchoolYear) {
            $totalFaculty = Teacher::whereHas('advisedClassSections', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->orWhereHas('classSectionSubjects.classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();

            $totalStudents = Student::whereHas('enrollments', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();

            $totalSections = ClassSection::where('school_year_id', $activeSchoolYear->id)->count();

            $totalSubjects = Subject::whereHas('classSectionSubjects.classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            })->distinct()->count();
        } else {
            $totalFaculty = Teacher::count();
            $totalStudents = Student::count();
            $totalSubjects = Subject::count();
            $totalSections = ClassSection::count();
        }

        return view('superadmins.faculty_information.index', compact(
            'teachers',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'selectedLevel',
            'educationLevelsList',
            'nextTeacherId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'teacher_id' => 'nullable|string|max:50|unique:teachers,teacher_id',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'education_level_id' => 'required|exists:education_levels,id',
            'position' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Male,Female',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            // Create Part 1: LMS Account User
            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
                'status' => 'active',
            ]);

            // Auto-generate Teacher ID if empty
            $teacherId = $validated['teacher_id'] ?: ('TCH-' . date('Y') . '-' . str_pad(Teacher::max('id') + 1, 4, '0', STR_PAD_LEFT));

            // Create Part 2: Teacher Profile
            Teacher::create([
                'user_id' => $user->id,
                'teacher_id' => $teacherId,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'education_level_id' => $validated['education_level_id'],
                'position' => $validated['position'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Faculty member added successfully!');
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:6',
            'teacher_id' => 'required|string|max:50|unique:teachers,teacher_id,' . $id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'education_level_id' => 'required|exists:education_levels,id',
            'position' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Male,Female',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($teacher, $validated) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name'] . ($validated['extension_name'] ? ' ' . $validated['extension_name'] : ''));

            // Update Part 1: User Account
            $userData = [
                'name' => $fullName,
                'email' => $validated['email'],
                'role' => 'teacher',
                'status' => 'active',
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if ($teacher->user) {
                $teacher->user->update($userData);
            }

            // Update Part 2: Teacher Profile
            $teacher->update([
                'teacher_id' => $validated['teacher_id'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'] ?? null,
                'education_level_id' => $validated['education_level_id'],
                'position' => $validated['position'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Faculty member updated successfully!');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);

        DB::transaction(function () use ($teacher) {
            if ($teacher->user) {
                $teacher->user->delete();
            }
            $teacher->delete();
        });

        return redirect()->back()->with('success', 'Faculty member deleted successfully!');
    }
}
