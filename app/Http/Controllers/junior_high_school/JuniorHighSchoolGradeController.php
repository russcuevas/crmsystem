<?php

namespace App\Http\Controllers\junior_high_school;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassSectionSubject;
use App\Models\GradingCategory;
use App\Models\GradingTask;
use App\Models\StudentTaskScore;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\Attendance;
use App\Models\Grade;

class JuniorHighSchoolGradeController extends Controller
{
    public function JuniorHighSchoolGradePage()
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

        $selectedSubjectId = request('section_subject_id');
        $selectedPeriod = request('academic_period', '1st Quarter');

        // Handled Subjects ONLY for this teacher
        $handledSubjectsQuery = ClassSectionSubject::with([
            'classSection.gradeLevel.educationLevel',
            'subject',
            'teacher'
        ])->where('teacher_id', $teacher->id);

        if ($activeSchoolYear) {
            $handledSubjectsQuery->whereHas('classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id));
        }

        $handledSubjects = $handledSubjectsQuery->get();

        $currentSectionSubject = null;
        $enrolledStudents = collect();
        $categories = collect();
        $tasks = collect();
        $scores = collect();
        $attendanceDates = collect();
        $attendances = collect();
        $computedGrades = collect();

        if ($selectedSubjectId) {
            $currentSectionSubject = $handledSubjects->firstWhere('id', $selectedSubjectId);
        }

        if (!$currentSectionSubject && $handledSubjects->isNotEmpty()) {
            $currentSectionSubject = $handledSubjects->first();
        }

        if ($currentSectionSubject) {
            $classSectionId = $currentSectionSubject->class_section_id;

            // Enrolled students in section
            $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($classSectionId, $activeSchoolYear) {
                $q->where('class_section_id', $classSectionId)
                  ->where('status', 'enrolled');
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->with('user')->get();

            // Categories & Tasks
            $categories = GradingCategory::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->with('gradingTasks')
                ->get();

            $categoryIds = $categories->pluck('id');
            $tasks = GradingTask::whereIn('grading_category_id', $categoryIds)->get();
            $taskIds = $tasks->pluck('id');

            $scores = StudentTaskScore::whereIn('grading_task_id', $taskIds)
                ->with('enrollment')
                ->get()
                ->keyBy(function ($item) {
                    return ($item->enrollment ? $item->enrollment->student_id : null) . '_' . $item->grading_task_id;
                });

            // Attendance Records
            $attendanceDates = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->with('enrollment')
                ->get()
                ->keyBy(function ($item) {
                    return ($item->enrollment ? $item->enrollment->student_id : null) . '_' . \Carbon\Carbon::parse($item->attendance_date)->format('Y-m-d');
                });

            // Final Computed Grades
            $computedGrades = Grade::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get()
                ->keyBy('student_id');
            // Categories Data for JS
            $categoriesData = $categories->map(function ($c) {
                return [
                    'id' => $c->id,
                    'weight' => floatval($c->weight),
                    'tasks' => $c->gradingTasks->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'max_score' => floatval($t->max_score),
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();
        } else {
            $categoriesData = [];
        }

        return view('junior_high_school.grades.index', compact(
            'teacher',
            'handledSubjects',
            'currentSectionSubject',
            'selectedPeriod',
            'enrolledStudents',
            'categories',
            'categoriesData',
            'tasks',
            'scores',
            'attendanceDates',
            'attendances',
            'computedGrades',
            'activeSchoolYear'
        ));
    }

    public function updateTaskScore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grading_task_id' => 'required|exists:grading_tasks,id',
            'score' => 'nullable|numeric|min:0',
        ]);

        $task = GradingTask::with('gradingCategory.classSectionSubject')->findOrFail($validated['grading_task_id']);
        if ($validated['score'] !== null && $validated['score'] > $task->max_score) {
            return response()->json([
                'success' => false,
                'message' => "Score cannot exceed maximum score of {$task->max_score}."
            ], 422);
        }

        $sectionId = $task->gradingCategory->classSectionSubject->class_section_id ?? null;
        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $sectionId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student enrollment record not found.'
            ], 404);
        }

        $studentScore = StudentTaskScore::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'grading_task_id' => $validated['grading_task_id'],
            ],
            [
                'score' => $validated['score'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Score updated successfully.',
            'data' => $studentScore,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create($validated);

        return redirect()->back()->with('success', 'Grading category added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = GradingCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Grading category updated successfully!');
    }

    public function destroyCategory($id)
    {
        $category = GradingCategory::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Grading category deleted successfully!');
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'grading_category_id' => 'required|exists:grading_categories,id',
            'task_name' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:1',
        ]);

        GradingTask::create($validated);

        return redirect()->back()->with('success', 'Task/Assessment added successfully!');
    }

    public function updateTask(Request $request, $id)
    {
        $task = GradingTask::findOrFail($id);

        $validated = $request->validate([
            'task_name' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:1',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function destroyTask($id)
    {
        $task = GradingTask::findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    public function storeAttendanceDate(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
        ]);

        $sectionSubject = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);

        $enrollments = Enrollment::where('class_section_id', $sectionSubject->class_section_id)
            ->where('status', 'enrolled')
            ->get();

        foreach ($enrollments as $enr) {
            Attendance::firstOrCreate([
                'enrollment_id' => $enr->id,
                'class_section_subject_id' => $sectionSubject->id,
                'attendance_date' => $validated['attendance_date'],
            ], [
                'status' => 'present',
            ]);
        }

        return redirect()->back()->with('success', 'Attendance date session added!');
    }

    public function updateAttendanceStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
        ]);

        $sectionSubject = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);
        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $sectionSubject->class_section_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Student enrollment record not found.'], 404);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'class_section_subject_id' => $sectionSubject->id,
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'status' => $validated['status'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance status updated.',
            'data' => $attendance,
        ]);
    }

    public function destroyAttendanceDate(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
        ]);

        Attendance::where('class_section_subject_id', $validated['class_section_subject_id'])
            ->where('attendance_date', $validated['attendance_date'])
            ->delete();

        return redirect()->back()->with('success', 'Attendance session deleted!');
    }

    public function computeTotalGrades(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
        ]);

        $sectionSubject = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);
        $categories = GradingCategory::where('class_section_subject_id', $sectionSubject->id)
            ->where('academic_period', $validated['academic_period'])
            ->with('gradingTasks')
            ->get();

        $enrolments = Enrollment::where('class_section_id', $sectionSubject->class_section_id)
            ->where('status', 'enrolled')
            ->with('student')
            ->get();

        foreach ($enrolments as $enr) {
            if (!$enr->student) continue;

            $finalQuarterGrade = 0;

            foreach ($categories as $cat) {
                $categoryTasks = $cat->gradingTasks;
                $catTotalMaxScore = $categoryTasks->sum('max_score');

                if ($catTotalMaxScore > 0) {
                    $taskIds = $categoryTasks->pluck('id');
                    $catTotalStudentScore = StudentTaskScore::where('enrollment_id', $enr->id)
                        ->whereIn('grading_task_id', $taskIds)
                        ->sum('score');

                    $percentage = ($catTotalStudentScore / $catTotalMaxScore) * 100;
                    $weightedContribution = ($percentage * $cat->weight) / 100;
                    $finalQuarterGrade += $weightedContribution;
                }
            }

            $computedScore = round($finalQuarterGrade, 2);
            $remarks = $computedScore >= 75 ? 'Passed' : 'Failed';

            Grade::updateOrCreate(
                [
                    'student_id' => $enr->student_id,
                    'class_section_subject_id' => $sectionSubject->id,
                    'academic_period' => $validated['academic_period'],
                ],
                [
                    'quarter_grade' => $computedScore,
                    'final_grade' => $computedScore,
                    'remarks' => $remarks,
                ]
            );
        }

        return redirect()->back()->with('success', 'Grades computed and published successfully!');
    }
}
