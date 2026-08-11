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

        $subjectsQuery = Subject::with(['educationLevel', 'course', 'parentSubject', 'subSubjects']);

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
        } else {
            $subjectsQuery->whereNull('parent_subject_id');
        }

        $subjects = $subjectsQuery->latest()->get();
        $parentSubjects = Subject::where('is_parent', true)->get();
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
            'parentSubjects',
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
            'has_lab' => 'nullable|boolean',
            'lecture_weight' => 'nullable|numeric|min:0|max:100',
            'lab_weight' => 'nullable|numeric|min:0|max:100',
            'parent_subject_id' => 'nullable|exists:subjects,id',
            'is_parent' => 'nullable|boolean',
        ]);

        $educationLevel = EducationLevel::find($validated['education_level_id']);
        if ($educationLevel && in_array(strtoupper($educationLevel->code), ['BED', 'JHS'])) {
            $quarterOptions = ['All Quarters', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            if (!in_array($validated['semester'], $quarterOptions)) {
                $validated['semester'] = 'All Quarters';
            }
            $validated['units'] = null;
            $validated['has_lab'] = false;
            $validated['lecture_weight'] = 100.00;
            $validated['lab_weight'] = 0.00;
            $validated['is_parent'] = $request->boolean('is_parent');
            $validated['parent_subject_id'] = $validated['is_parent'] ? null : $request->input('parent_subject_id');
        } else {
            $validated['is_parent'] = false;
            $validated['parent_subject_id'] = null;
            $validated['has_lab'] = $request->boolean('has_lab');
            if ($validated['has_lab']) {
                $validated['lecture_weight'] = $validated['lecture_weight'] ?? 70.00;
                $validated['lab_weight'] = $validated['lab_weight'] ?? 30.00;
            } else {
                $validated['lecture_weight'] = 100.00;
                $validated['lab_weight'] = 0.00;
            }
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
            'has_lab' => 'nullable|boolean',
            'lecture_weight' => 'nullable|numeric|min:0|max:100',
            'lab_weight' => 'nullable|numeric|min:0|max:100',
            'parent_subject_id' => 'nullable|exists:subjects,id',
            'is_parent' => 'nullable|boolean',
        ]);

        $educationLevel = EducationLevel::find($validated['education_level_id']);
        if ($educationLevel && in_array(strtoupper($educationLevel->code), ['BED', 'JHS'])) {
            $quarterOptions = ['All Quarters', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            if (!in_array($validated['semester'], $quarterOptions)) {
                $validated['semester'] = 'All Quarters';
            }
            $validated['units'] = null;
            $validated['has_lab'] = false;
            $validated['lecture_weight'] = 100.00;
            $validated['lab_weight'] = 0.00;
            $validated['is_parent'] = $request->boolean('is_parent');
            $validated['parent_subject_id'] = $validated['is_parent'] ? null : $request->input('parent_subject_id');
        } else {
            $validated['is_parent'] = false;
            $validated['parent_subject_id'] = null;
            $validated['has_lab'] = $request->boolean('has_lab');
            if ($validated['has_lab']) {
                $validated['lecture_weight'] = $validated['lecture_weight'] ?? 70.00;
                $validated['lab_weight'] = $validated['lab_weight'] ?? 30.00;
            } else {
                $validated['lecture_weight'] = 100.00;
                $validated['lab_weight'] = 0.00;
            }
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

