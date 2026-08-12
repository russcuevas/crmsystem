<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSection;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\GradeLevel;
use App\Models\Course;

class SuperAdminManageSectionController extends Controller
{
    public function SuperAdminManageSectionPage()
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
        $allCourses = Course::all();
        $teachers = Teacher::with(['user', 'educationLevel'])->get();
        $teachersList = $teachers;

        $sectionsQuery = ClassSection::with(['schoolYear', 'gradeLevel.educationLevel', 'course', 'adviser.user']);
        if ($activeSchoolYear) {
            $sectionsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        if ($selectedLevel) {
            $sectionsQuery->whereHas('gradeLevel.educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }

        $sections = $sectionsQuery->latest()->get();

        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.sections.index', compact(
            'sections',
            'activeSchoolYear',
            'allSchoolYears',
            'schoolYears',
            'allGradeLevels',
            'allCourses',
            'teachers',
            'teachersList',
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
            'school_year_id' => 'required|exists:school_years,id',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'section_name' => 'required|string|max:100',
            'course_id' => 'nullable|exists:courses,id',
            'class_adviser_id' => 'nullable|exists:teachers,id',
        ]);

        $gradeLevel = GradeLevel::with('educationLevel')->find($validated['grade_level_id']);
        $edCode = strtoupper($gradeLevel->educationLevel->code ?? '');

        // BED and JHS have no course
        if (in_array($edCode, ['BED', 'JHS'])) {
            $validated['course_id'] = null;
        }

        // College has no class adviser
        if ($edCode === 'COLLEGE') {
            $validated['class_adviser_id'] = null;
        }

        ClassSection::create($validated);

        return redirect()->back()->with('success', 'Class Section created successfully!');
    }

    public function update(Request $request, $id)
    {
        $section = ClassSection::findOrFail($id);

        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'section_name' => 'required|string|max:100',
            'course_id' => 'nullable|exists:courses,id',
            'class_adviser_id' => 'nullable|exists:teachers,id',
        ]);

        $gradeLevel = GradeLevel::with('educationLevel')->find($validated['grade_level_id']);
        $edCode = strtoupper($gradeLevel->educationLevel->code ?? '');

        // BED and JHS have no course
        if (in_array($edCode, ['BED', 'JHS'])) {
            $validated['course_id'] = null;
        }

        // College has no class adviser
        if ($edCode === 'COLLEGE') {
            $validated['class_adviser_id'] = null;
        }

        $section->update($validated);

        return redirect()->back()->with('success', 'Class Section updated successfully!');
    }

    public function destroy($id)
    {
        $section = ClassSection::findOrFail($id);
        $section->delete();

        return redirect()->back()->with('success', 'Class Section deleted successfully!');
    }
}
