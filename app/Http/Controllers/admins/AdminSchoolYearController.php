<?php

namespace App\Http\Controllers\admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;

class AdminSchoolYearController extends Controller
{
    public function AdminSchoolYearPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $schoolYears = SchoolYear::withCount(['classSections', 'enrollments'])->latest()->get();

        $totalAccounts = User::whereNotIn('role', ['superadmin', 'admin'])->count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('admins.school_years.index', compact(
            'schoolYears',
            'activeSchoolYear',
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
            'school_year' => 'required|string|max:50|unique:school_years,school_year',
            'is_active' => 'nullable|boolean',
        ]);

        $makeActive = $request->boolean('is_active');
        
        if (!$makeActive && SchoolYear::where('is_active', true)->count() === 0) {
            $makeActive = true;
        }

        if ($makeActive) {
            SchoolYear::query()->update(['is_active' => false]);
        }

        $sy = SchoolYear::create([
            'school_year' => $validated['school_year'],
            'is_active' => $makeActive,
        ]);

        if ($makeActive) {
            session(['active_school_year_id' => $sy->id]);
        }

        return redirect()->back()->with('success', 'School Year created successfully!');
    }

    public function update(Request $request, $id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        $validated = $request->validate([
            'school_year' => 'required|string|max:50|unique:school_years,school_year,' . $id,
            'is_active' => 'nullable|boolean',
        ]);

        $makeActive = $request->boolean('is_active');

        if ($makeActive) {
            SchoolYear::where('id', '!=', $id)->update(['is_active' => false]);
            $schoolYear->is_active = true;
            session(['active_school_year_id' => $id]);
        } else {
            $schoolYear->is_active = false;
        }

        $schoolYear->school_year = $validated['school_year'];
        $schoolYear->save();

        return redirect()->back()->with('success', 'School Year updated successfully!');
    }

    public function setActive($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        SchoolYear::query()->update(['is_active' => false]);
        $schoolYear->update(['is_active' => true]);
        session(['active_school_year_id' => $schoolYear->id]);

        return redirect()->back()->with('success', "S.Y. {$schoolYear->school_year} set as active School Year!");
    }

    public function destroy($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        if ($schoolYear->is_active) {
            return redirect()->back()->withErrors(['is_active' => 'Cannot delete the active School Year. Please set another School Year as active first.']);
        }

        if ($schoolYear->classSections()->count() > 0 || $schoolYear->enrollments()->count() > 0) {
            return redirect()->back()->withErrors(['delete' => 'Cannot delete School Year with associated class sections or student enrollments.']);
        }

        $schoolYear->delete();

        return redirect()->back()->with('success', 'School Year deleted successfully!');
    }

    public function switch(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $schoolYear = SchoolYear::findOrFail($validated['school_year_id']);

        SchoolYear::query()->update(['is_active' => false]);
        $schoolYear->update(['is_active' => true]);
        session(['active_school_year_id' => $schoolYear->id]);

        return redirect()->back()->with('success', "Switched to Active S.Y. {$schoolYear->school_year}!");
    }
}
