<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSectionSubject;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\User;

class SuperAdminAssignedSubjectController extends Controller
{
    public function SuperAdminAssignedSubjectPage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $educationLevelsList = EducationLevel::all();

        $assignedQuery = ClassSectionSubject::with([
            'classSection.gradeLevel.educationLevel',
            'classSection.course',
            'subject',
            'teacher.user'
        ]);

        if ($activeSchoolYear) {
            $assignedQuery->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            });
        }

        if ($selectedLevel) {
            $assignedQuery->whereHas('classSection.gradeLevel.educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }

        $assignedSubjects = $assignedQuery->latest()->get();

        // Get Available Class Sections for current S.Y. and Level filter
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

        // Get Available Subjects
        $subjectsQuery = Subject::with('educationLevel');
        if ($selectedLevel) {
            $subjectsQuery->whereHas('educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });
        }
        $subjects = $subjectsQuery->get();

        // Get All Teachers
        $teachers = Teacher::with(['user', 'educationLevel'])->get();

        // Counts for layout compatibility
        $totalAccounts = User::count();
        $totalFaculty = Teacher::count();
        $totalStudents = \App\Models\Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('superadmins.assigned_subjects.index', compact(
            'assignedSubjects',
            'classSections',
            'subjects',
            'teachers',
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
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Check if subject already assigned to this section
        $exists = ClassSectionSubject::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['subject_id' => 'This subject is already assigned to the selected class section!']);
        }

        ClassSectionSubject::create($validated);

        return redirect()->back()->with('success', 'Subject assigned to class section successfully!');
    }

    public function update(Request $request, $id)
    {
        $assignedSubject = ClassSectionSubject::findOrFail($id);

        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Check duplicate excluding current entry
        $exists = ClassSectionSubject::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['subject_id' => 'This subject is already assigned to the selected class section!']);
        }

        $assignedSubject->update($validated);

        return redirect()->back()->with('success', 'Assigned subject details updated successfully!');
    }

    public function destroy($id)
    {
        $assignedSubject = ClassSectionSubject::findOrFail($id);
        $assignedSubject->delete();

        return redirect()->back()->with('success', 'Assigned subject removed successfully!');
    }
}
