<?php

namespace App\Http\Controllers\admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSectionSubject;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\User;

class AdminAssignedSubjectController extends Controller
{
    public function AdminAssignedSubjectPage()
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
            'subject.subSubjects',
            'teacher.user'
        ])->whereHas('subject', function ($q) {
            $q->whereNull('parent_subject_id');
        });

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

        foreach ($assignedSubjects as $assigned) {
            if ($assigned->subject && ($assigned->subject->is_parent || $assigned->subject->subSubjects->isNotEmpty())) {
                $subSubjectIds = $assigned->subject->subSubjects->pluck('id');
                $assigned->assignedSubSubjects = ClassSectionSubject::where('class_section_id', $assigned->class_section_id)
                    ->whereIn('subject_id', $subSubjectIds)
                    ->with(['subject', 'teacher.user'])
                    ->get();
            } else {
                $assigned->assignedSubSubjects = collect();
            }
        }

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

        $selectedSem = request('semester');
        $subjectsQuery = Subject::with('educationLevel')->whereNull('parent_subject_id');
        if ($selectedLevel) {
            $subjectsQuery->whereHas('educationLevel', function ($q) use ($selectedLevel) {
                $q->where('code', $selectedLevel);
            });

            if (in_array(strtoupper($selectedLevel), ['SHS', 'COLLEGE']) && $selectedSem) {
                $semKey = ($selectedSem == '2nd Semester') ? '2nd' : '1st';
                $subjectsQuery->where(function ($q) use ($semKey, $selectedSem) {
                    $q->where('semester', 'LIKE', '%' . $semKey . '%')
                      ->orWhere('semester', $selectedSem)
                      ->orWhereNull('semester');
                });
            }
        }
        $subjects = $subjectsQuery->get();

        $teachers = Teacher::with(['user', 'educationLevel'])->get();

        $totalAccounts = User::whereNotIn('role', ['superadmin', 'admin'])->count();
        $totalFaculty = Teacher::count();
        $totalStudents = \App\Models\Student::count();
        $totalSubjects = Subject::count();
        $totalSections = ClassSection::count();

        return view('admins.assigned_subjects.index', compact(
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

        $subject = Subject::with('subSubjects')->findOrFail($validated['subject_id']);

        $exists = ClassSectionSubject::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['subject_id' => 'This subject is already assigned to the selected class section!']);
        }

        $parentAssignment = ClassSectionSubject::create($validated);

        if ($subject->is_parent || $subject->subSubjects->isNotEmpty()) {
            foreach ($subject->subSubjects as $subSubject) {
                ClassSectionSubject::updateOrCreate(
                    [
                        'class_section_id' => $validated['class_section_id'],
                        'subject_id' => $subSubject->id,
                    ],
                    [
                        'teacher_id' => $validated['teacher_id'],
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Subject and sub-subjects assigned to class section successfully!');
    }

    public function update(Request $request, $id)
    {
        $assignedSubject = ClassSectionSubject::with('subject.subSubjects')->findOrFail($id);

        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $exists = ClassSectionSubject::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['subject_id' => 'This subject is already assigned to the selected class section!']);
        }

        $assignedSubject->update($validated);

        $subject = $assignedSubject->subject;
        if ($subject && ($subject->is_parent || $subject->subSubjects->isNotEmpty())) {
            foreach ($subject->subSubjects as $subSubject) {
                ClassSectionSubject::updateOrCreate(
                    [
                        'class_section_id' => $validated['class_section_id'],
                        'subject_id' => $subSubject->id,
                    ],
                    [
                        'teacher_id' => $validated['teacher_id'],
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Assigned subject details updated successfully!');
    }

    public function destroy($id)
    {
        $assignedSubject = ClassSectionSubject::with('subject.subSubjects')->findOrFail($id);
        $subject = $assignedSubject->subject;

        if ($subject && ($subject->is_parent || $subject->subSubjects->isNotEmpty())) {
            $subSubjectIds = $subject->subSubjects->pluck('id');
            ClassSectionSubject::where('class_section_id', $assignedSubject->class_section_id)
                ->whereIn('subject_id', $subSubjectIds)
                ->delete();
        }

        $assignedSubject->delete();

        return redirect()->back()->with('success', 'Assigned subject removed successfully!');
    }
}
