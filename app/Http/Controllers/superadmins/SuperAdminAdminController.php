<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;

class SuperAdminAdminController extends Controller
{
    public function SuperAdminAdminPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();

        // Retrieve admin users (roles 'admin' or 'superadmin')
        $admins = User::whereIn('role', ['admin', 'superadmin'])->latest()->get();

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

        return view('superadmins.admins.index', compact(
            'admins',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'selectedLevel',
            'educationLevelsList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,superadmin',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'New admin account created successfully!');
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,superadmin',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        return redirect()->back()->with('success', 'Admin account updated successfully!');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        if (Auth::id() == $admin->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account while logged in!');
        }

        $admin->delete();

        return redirect()->back()->with('success', 'Admin account deleted successfully!');
    }
}
