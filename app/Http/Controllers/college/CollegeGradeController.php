<?php

namespace App\Http\Controllers\college;

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
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\Attendance;
use App\Models\Grade;

class CollegeGradeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('college.login.page')->with('error', 'Teacher profile not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedSubjectId = request('section_subject_id');
        $selectedPeriod = request('academic_period');
        $selectedSemester = request('semester', '1st Semester');

        $subjectQuery = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->with([
                'classSection.gradeLevel.educationLevel',
                'classSection.course',
                'subject',
                'teacher'
            ]);

        if ($activeSchoolYear) {
            $subjectQuery->whereHas('classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id));
        }

        // Filter for College Level
        $subjectQuery->whereHas('classSection.gradeLevel.educationLevel', fn($q) => $q->whereIn('code', ['COLLEGE', 'COL']));

        // Semester filter
        $semKey = ($selectedSemester == '2nd Semester') ? '2nd' : '1st';
        $subjectQuery->whereHas('subject', function ($sq) use ($semKey, $selectedSemester) {
            $sq->where('semester', 'LIKE', '%' . $semKey . '%')
              ->orWhere('semester', $selectedSemester)
              ->orWhereNull('semester');
        });

        $classSectionSubjects = $subjectQuery->get();

        $currentSectionSubject = null;
        if ($selectedSubjectId) {
            $currentSectionSubject = $classSectionSubjects->where('id', $selectedSubjectId)->first();
        }

        if (!$currentSectionSubject && $classSectionSubjects->count() > 0) {
            $currentSectionSubject = $classSectionSubjects->first();
        }

        $categories = collect();
        $enrolledStudents = collect();
        $availablePeriods = collect(['Prelim', 'Midterm', 'Finals']);
        $attendanceDates = collect();
        $attendances = collect();
        $savedGrades = collect();

        if ($currentSectionSubject) {
            if (!$selectedPeriod) {
                $selectedPeriod = $availablePeriods->first();
            }

            // Load Categories & Tasks for the selected period
            $categories = GradingCategory::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->with(['tasks.scores'])
                ->get();

            // Load Enrolled Students
            $enrolledStudents = Enrollment::where('class_section_id', $currentSectionSubject->class_section_id)
                ->with(['student.user', 'taskScores'])
                ->get()
                ->sortBy(fn($e) => $e->student->last_name ?? '');

            // Attendance Tracking
            $attendanceDates = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get();

            // Saved Final Calculated Grades for the selected period
            $savedGrades = Grade::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get()
                ->keyBy('enrollment_id');
        }

        return view('college.grades.index', compact(
            'classSectionSubjects',
            'currentSectionSubject',
            'categories',
            'enrolledStudents',
            'availablePeriods',
            'selectedPeriod',
            'selectedSemester',
            'attendanceDates',
            'attendances',
            'savedGrades',
            'activeSchoolYear'
        ));
    }

    public function updateTaskScore(Request $request)
    {
        $request->validate([
            'grading_task_id' => 'required|exists:grading_tasks,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'score' => 'nullable|numeric|min:0',
        ]);

        $scoreRecord = StudentTaskScore::updateOrCreate(
            [
                'grading_task_id' => $request->grading_task_id,
                'enrollment_id' => $request->enrollment_id,
            ],
            [
                'score' => $request->score,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Task score updated successfully.',
            'score' => $scoreRecord->score,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'category_name' => 'required|string|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create([
            'class_section_subject_id' => $validated['class_section_subject_id'],
            'academic_period' => $validated['academic_period'],
            'name' => $validated['category_name'],
            'weight' => $validated['weight_percentage'],
        ]);

        return redirect()->back()->with('success', 'Grading category added successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = GradingCategory::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $category->update([
            'name' => $validated['category_name'],
            'weight' => $validated['weight_percentage'],
        ]);

        return redirect()->back()->with('success', 'Grading category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = GradingCategory::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Grading category deleted successfully.');
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'grading_category_id' => 'required|exists:grading_categories,id',
            'task_name' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:1',
        ]);

        GradingTask::create($validated);

        return redirect()->back()->with('success', 'Grading task added successfully.');
    }

    public function updateTask(Request $request, $id)
    {
        $task = GradingTask::findOrFail($id);

        $validated = $request->validate([
            'grading_category_id' => 'nullable|exists:grading_categories,id',
            'task_name' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:1',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Grading task updated successfully.');
    }

    public function destroyTask($id)
    {
        $task = GradingTask::findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Grading task deleted successfully.');
    }

    public function updateAttendanceStatus(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'attendance_date' => 'required|date',
            'status' => 'required|string|in:P,L,A,AEL,E,C',
            'academic_period' => 'required|string',
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'class_section_subject_id' => $request->class_section_subject_id,
                'enrollment_id' => $request->enrollment_id,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => strtoupper($request->status),
                'academic_period' => $request->academic_period,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance status updated.',
            'status' => $attendance->status
        ]);
    }

    public function storeAttendanceDate(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'attendance_date' => 'required|date',
        ]);

        $enrollments = Enrollment::whereHas('classSection.classSectionSubjects', function ($q) use ($validated) {
            $q->where('id', $validated['class_section_subject_id']);
        })->get();

        foreach ($enrollments as $enrollment) {
            Attendance::firstOrCreate([
                'class_section_subject_id' => $validated['class_section_subject_id'],
                'enrollment_id' => $enrollment->id,
                'attendance_date' => $validated['attendance_date'],
            ], [
                'status' => 'P',
                'academic_period' => $validated['academic_period'],
            ]);
        }

        return redirect()->back()->with('success', 'Attendance session created successfully.');
    }

    public function destroyAttendanceDate(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
            'academic_period' => 'nullable|string',
        ]);

        $query = Attendance::where('class_section_subject_id', $request->class_section_subject_id)
            ->where('attendance_date', $request->attendance_date);

        if ($request->filled('academic_period')) {
            $query->where('academic_period', $request->academic_period);
        }

        $query->delete();

        return redirect()->back()->with('success', 'Attendance session deleted successfully.');
    }

    public function computeTotalGrades(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
        ]);

        $cssId = $request->class_section_subject_id;
        $css = ClassSectionSubject::with(['classSection.gradeLevel.educationLevel', 'subject'])->findOrFail($cssId);

        $enrollments = Enrollment::where('class_section_id', $css->class_section_id)->get();
        $periods = ['Prelim', 'Midterm', 'Finals'];

        $results = [];

        foreach ($enrollments as $enrollment) {
            $periodGrades = [];
            $validPeriodGrades = [];

            foreach ($periods as $period) {
                $categories = GradingCategory::where('class_section_subject_id', $cssId)
                    ->where('academic_period', $period)
                    ->with('tasks')
                    ->get();

                if ($categories->isEmpty()) {
                    Grade::where('enrollment_id', $enrollment->id)
                        ->where('class_section_subject_id', $cssId)
                        ->where('academic_period', $period)
                        ->delete();
                    $periodGrades[$period] = null;
                    continue;
                }

                $totalPeriodWeightedScore = 0;
                $totalCategoryWeights = 0;

                foreach ($categories as $cat) {
                    $catWeight = (float)$cat->weight_percentage;
                    $tasks = $cat->tasks;

                    if ($tasks->isEmpty()) continue;

                    $catStudentScoreSum = 0;
                    $catMaxScoreSum = 0;

                    foreach ($tasks as $t) {
                        $scoreObj = StudentTaskScore::where('grading_task_id', $t->id)
                            ->where('enrollment_id', $enrollment->id)
                            ->first();

                        $scoreVal = $scoreObj && $scoreObj->score !== null ? (float)$scoreObj->score : 0;
                        $catStudentScoreSum += $scoreVal;
                        $catMaxScoreSum += (float)$t->max_score;
                    }

                    if ($catMaxScoreSum > 0) {
                        $catPercentage = ($catStudentScoreSum / $catMaxScoreSum) * 100;
                        $totalPeriodWeightedScore += ($catPercentage * ($catWeight / 100));
                        $totalCategoryWeights += $catWeight;
                    }
                }

                if ($totalCategoryWeights > 0) {
                    $finalPeriodGrade = ($totalPeriodWeightedScore / ($totalCategoryWeights / 100));
                    $roundedPeriodGrade = round($finalPeriodGrade, 2);
                    $periodGrades[$period] = $roundedPeriodGrade;
                    $validPeriodGrades[] = $roundedPeriodGrade;

                    Grade::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'class_section_subject_id' => $cssId,
                            'academic_period' => $period,
                        ],
                        [
                            'final_grade' => $roundedPeriodGrade,
                            'remarks' => $roundedPeriodGrade >= 75 ? 'Passed' : 'Failed',
                        ]
                    );
                } else {
                    Grade::where('enrollment_id', $enrollment->id)
                        ->where('class_section_subject_id', $cssId)
                        ->where('academic_period', $period)
                        ->delete();
                    $periodGrades[$period] = null;
                }
            }

            // Compute Final Subject Grade
            if (!empty($validPeriodGrades)) {
                $subjectGradeVal = round(array_sum($validPeriodGrades) / count($validPeriodGrades), 2);
                $remarksVal = $subjectGradeVal >= 75 ? 'Passed' : 'Failed';

                Grade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'class_section_subject_id' => $cssId,
                        'academic_period' => 'Subject Grade',
                    ],
                    [
                        'final_grade' => $subjectGradeVal,
                        'remarks' => $remarksVal,
                    ]
                );
            } else {
                $subjectGradeVal = null;
                $remarksVal = 'Incomplete';
                Grade::where('enrollment_id', $enrollment->id)
                    ->where('class_section_subject_id', $cssId)
                    ->where('academic_period', 'Subject Grade')
                    ->delete();
            }

            $results[] = [
                'enrollment_id' => $enrollment->id,
                'period_grades' => $periodGrades,
                'subject_grade' => $subjectGradeVal !== null ? number_format($subjectGradeVal, 2) : '-',
                'remarks' => $remarksVal,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'College subject total grades calculated and saved successfully!',
            'results' => $results,
        ]);
    }

    public function resetTotalGrades(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
        ]);

        Grade::where('class_section_subject_id', $request->class_section_subject_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'All computed total grades for this class subject have been reset successfully.'
        ]);
    }
}
