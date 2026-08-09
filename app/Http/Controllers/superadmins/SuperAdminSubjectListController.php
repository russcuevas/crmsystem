<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\Course;

class SuperAdminSubjectListController extends Controller
{
    public function SuperAdminSubjectListPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $selectedSem = request('semester');
        $selectedPeriod = request('academic_period');
        $search = request('search');

        $educationLevelsList = EducationLevel::all();
        $coursesList = Course::all();

        $subjectsQuery = Subject::with(['educationLevel', 'course']);

        if ($selectedLevel) {
            $subjectsQuery->whereHas('educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });

            if (in_array(strtoupper($selectedLevel), ['BED', 'JHS'])) {
                if ($selectedPeriod) {
                    $subjectsQuery->where(function ($q) use ($selectedPeriod) {
                        $q->where('semester', 'All Quarters')
                          ->orWhere('semester', $selectedPeriod)
                          ->orWhereNull('semester');
                    });
                }
            } else {
                if ($selectedSem) {
                    $subjectsQuery->where(function ($q) use ($selectedSem) {
                        $q->where('semester', $selectedSem)
                          ->orWhereNull('semester');
                    });
                }
            }
        }

        if ($search) {
            $subjectsQuery->where(function ($q) use ($search) {
                $q->where('subject_code', 'like', '%' . $search . '%')
                  ->orWhere('subject_name', 'like', '%' . $search . '%');
            });
        }

        $subjects = $subjectsQuery->latest()->get();
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

            $totalSubjects = Subject::count();
        } else {
            $totalFaculty = Teacher::count();
            $totalStudents = Student::count();
            $totalSubjects = Subject::count();
            $totalSections = ClassSection::count();
        }

        $currentEducationLevel = $selectedLevel ? EducationLevel::where('code', strtoupper($selectedLevel))->first() : null;

        return view('superadmins.subject_list.index', compact(
            'subjects',
            'activeSchoolYear',
            'totalAccounts',
            'totalFaculty',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'selectedLevel',
            'currentEducationLevel',
            'educationLevelsList',
            'coursesList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|string|max:50',
            'subject_name' => 'required|string|max:255',
            'education_level_id' => 'required|exists:education_levels,id',
            'course_id' => 'nullable|exists:courses,id',
            'units' => 'nullable|integer|min:0',
            'semester' => 'required|string|max:50',
        ]);

        $educationLevel = EducationLevel::find($validated['education_level_id']);
        if ($educationLevel && in_array(strtoupper($educationLevel->code), ['BED', 'JHS'])) {
            $quarterOptions = ['All Quarters', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            if (!in_array($validated['semester'], $quarterOptions)) {
                $validated['semester'] = 'All Quarters';
            }
            $validated['units'] = null;
        }

        Subject::create($validated);

        return redirect()->back()->with('success', 'Subject added successfully!');
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_code' => 'required|string|max:50',
            'subject_name' => 'required|string|max:255',
            'education_level_id' => 'required|exists:education_levels,id',
            'course_id' => 'nullable|exists:courses,id',
            'units' => 'nullable|integer|min:0',
            'semester' => 'required|string|max:50',
        ]);

        $educationLevel = EducationLevel::find($validated['education_level_id']);
        if ($educationLevel && in_array(strtoupper($educationLevel->code), ['BED', 'JHS'])) {
            $quarterOptions = ['All Quarters', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            if (!in_array($validated['semester'], $quarterOptions)) {
                $validated['semester'] = 'All Quarters';
            }
            $validated['units'] = null;
        }

        $subject->update($validated);

        return redirect()->back()->with('success', 'Subject updated successfully!');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->back()->with('success', 'Subject deleted successfully!');
    }
}

