<?php

namespace App\Http\Controllers\senior_high_school;

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

class SeniorHighSchoolGradeController extends Controller
{
    public function SeniorHighSchoolGradePage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('senior_high_school.login.page')->with('error', 'Teacher profile not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedSemester = request('semester', session('active_semester', '1st Semester'));
        session(['active_semester' => $selectedSemester]);

        $selectedSubjectId = request('section_subject_id');
        $selectedPeriod = request('academic_period', 'Prelim');

        // Handled Subjects ONLY for this teacher
        $handledSubjectsQuery = ClassSectionSubject::with([
            'classSection.gradeLevel.educationLevel',
            'classSection.course',
            'subject',
            'teacher'
        ])->where('teacher_id', $teacher->id);

        // Filter subjects by semester for SHS
        $semKey = ($selectedSemester == '2nd Semester') ? '2nd' : '1st';
        $handledSubjectsQuery->whereHas('subject', function ($sq) use ($semKey, $selectedSemester) {
            $sq->where('semester', 'LIKE', '%' . $semKey . '%')
              ->orWhere('semester', $selectedSemester)
              ->orWhereNull('semester');
        });

        // Filter handled subjects to top-level subjects ONLY (excluding sub-subjects from main list)
        $handledSubjectsQuery->whereHas('subject', fn($sq) => $sq->whereNull('parent_subject_id'));

        $handledSubjects = $handledSubjectsQuery->get();

        $currentSectionSubject = null;
        $enrolledStudents = collect();
        $categories = collect();
        $tasks = collect();
        $scores = collect();
        $attendanceDates = collect();
        $attendances = collect();
        $computedGrades = collect();
        $subSectionSubjects = collect();
        $selectedSubSubjectId = request('sub_subject_id');
        $activeSubSectionSubject = null;
        $isParentSubject = false;
        $mapehSummaryGrades = collect();
        $availablePeriods = collect(['Prelim', 'Midterm', 'Finals']);

        if ($selectedSubjectId) {
            $currentSectionSubject = $handledSubjects->firstWhere('id', $selectedSubjectId);
        }

        if (!$currentSectionSubject && $handledSubjects->isNotEmpty()) {
            $currentSectionSubject = $handledSubjects->first();
        }

        if ($currentSectionSubject) {
            $classSectionId = $currentSectionSubject->class_section_id;

            $defaultPeriods = collect(['Prelim', 'Midterm', 'Finals']);
            $dbPeriods = GradingCategory::where('class_section_subject_id', $currentSectionSubject->id)
                ->distinct()
                ->pluck('academic_period');

            $availablePeriods = $defaultPeriods->merge($dbPeriods)->unique()->values();

            if (!$selectedPeriod || !$availablePeriods->contains($selectedPeriod)) {
                $selectedPeriod = $availablePeriods->first();
            }

            $isParentSubject = $currentSectionSubject->subject && ($currentSectionSubject->subject->is_parent || Subject::where('parent_subject_id', $currentSectionSubject->subject_id)->exists());

            if ($isParentSubject) {
                $subSectionSubjects = ClassSectionSubject::where('class_section_id', $currentSectionSubject->class_section_id)
                    ->whereHas('subject', fn($q) => $q->where('parent_subject_id', $currentSectionSubject->subject_id))
                    ->with(['subject'])
                    ->get();

                if ($selectedSubSubjectId === 'summary') {
                    $activeSubSectionSubject = 'summary';
                } elseif ($selectedSubSubjectId) {
                    $activeSubSectionSubject = $subSectionSubjects->firstWhere('id', $selectedSubSubjectId);
                }

                if (!$activeSubSectionSubject && $subSectionSubjects->isNotEmpty()) {
                    $activeSubSectionSubject = $subSectionSubjects->first();
                }
            }

            $targetSectionSubject = ($isParentSubject && $activeSubSectionSubject && $activeSubSectionSubject !== 'summary')
                ? $activeSubSectionSubject
                : $currentSectionSubject;

            // Enrolled students in section
            $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($classSectionId, $activeSchoolYear) {
                $q->where('class_section_id', $classSectionId)
                  ->whereIn('status', ['enrolled', 'active']);
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->with('user')->get();

            // Categories & Tasks
            $categories = GradingCategory::where('class_section_subject_id', $targetSectionSubject->id)
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

            // Attendance Records for Target Component
            $attendanceDates = Attendance::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = Attendance::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->with('enrollment')
                ->get()
                ->keyBy(function ($item) {
                    return ($item->enrollment ? $item->enrollment->student_id : null) . '_' . \Carbon\Carbon::parse($item->attendance_date)->format('Y-m-d');
                });

            // Final Computed Grades for Target Component
            $computedGrades = Grade::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get()
                ->keyBy('student_id');

            if ($isParentSubject) {
                $allSectionSubjectIds = $subSectionSubjects->pluck('id')->push($currentSectionSubject->id);
                $mapehSummaryGrades = Grade::whereIn('class_section_subject_id', $allSectionSubjectIds)
                    ->where('academic_period', $selectedPeriod)
                    ->get();
            }

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

        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->get();

        return view('senior_high_school.grades.index', compact(
            'teacher',
            'handledSubjects',
            'currentSectionSubject',
            'enrolledStudents',
            'categories',
            'tasks',
            'scores',
            'attendanceDates',
            'attendances',
            'computedGrades',
            'categoriesData',
            'selectedSemester',
            'selectedPeriod',
            'availablePeriods',
            'advisorySections',
            'subSectionSubjects',
            'activeSubSectionSubject',
            'isParentSubject',
            'mapehSummaryGrades',
            'activeSchoolYear'
        ));
    }

    public function SeniorHighSchoolAdvisoryGradePage(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('senior_high_school.login.page')->with('error', 'Teacher profile not found.');
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedSemester = $request->input('semester', session('active_semester', '1st Semester'));
        session(['active_semester' => $selectedSemester]);

        $selectedPeriod = $request->input('academic_period', 'Prelim');

        // Advisory Sections for this teacher
        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['gradeLevel', 'course', 'schoolYear'])
            ->get();

        $isAdviser = $advisorySections->isNotEmpty();

        $selectedSectionId = $request->input('class_section_id');

        $currentSection = null;
        if ($selectedSectionId) {
            $currentSection = $advisorySections->firstWhere('id', $selectedSectionId);
        }
        if (!$currentSection && $isAdviser) {
            $currentSection = $advisorySections->first();
        }

        $sectionSubjects = collect();
        $enrolledStudents = collect();
        $gradesMatrix = [];
        $quarterGradesMatrix = [];
        $studentSummaries = [];
        $classStats = [
            'total_students' => 0,
            'class_average' => 0,
            'passed_count' => 0,
            'failed_count' => 0,
            'pending_count' => 0,
        ];

        $periods = ['Prelim', 'Midterm', 'Finals'];

        if ($currentSection) {
            $semKey = ($selectedSemester == '2nd Semester') ? '2nd' : '1st';

            $sectionSubjects = ClassSectionSubject::where('class_section_id', $currentSection->id)
                ->whereHas('subject', function ($sq) use ($semKey, $selectedSemester) {
                    $sq->whereNull('parent_subject_id')
                      ->where(function ($q) use ($semKey, $selectedSemester) {
                          $q->where('semester', 'LIKE', '%' . $semKey . '%')
                            ->orWhere('semester', $selectedSemester)
                            ->orWhereNull('semester');
                      });
                })
                ->with(['subject', 'teacher'])
                ->get();

            $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($currentSection, $activeSchoolYear) {
                $q->where('class_section_id', $currentSection->id)
                  ->whereIn('status', ['enrolled', 'active']);
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->with('user')->get();

            $allSectionSubjectIds = ClassSectionSubject::where('class_section_id', $currentSection->id)->pluck('id');
            $allGrades = Grade::whereIn('class_section_subject_id', $allSectionSubjectIds)->get();

            foreach ($enrolledStudents as $student) {
                $studentSumGrades = 0;
                $studentCountGrades = 0;

                foreach ($sectionSubjects as $css) {
                    $stGrade = $allGrades->where('student_id', $student->id)
                        ->where('class_section_subject_id', $css->id)
                        ->where('academic_period', $selectedPeriod)
                        ->first();

                    $gradesMatrix[$student->id][$css->id] = $stGrade ? floatval($stGrade->final_quarter_grade) : null;

                    foreach ($periods as $p) {
                        $pGrade = $allGrades->where('student_id', $student->id)
                            ->where('class_section_subject_id', $css->id)
                            ->where('academic_period', $p)
                            ->first();

                        $quarterGradesMatrix[$student->id][$css->id][$p] = $pGrade ? floatval($pGrade->final_quarter_grade) : null;
                    }

                    if ($stGrade && !is_null($stGrade->final_quarter_grade)) {
                        $studentSumGrades += floatval($stGrade->final_quarter_grade);
                        $studentCountGrades++;
                    }
                }

                $genAvg = $studentCountGrades > 0 ? round($studentSumGrades / $studentCountGrades, 2) : null;
                $remarks = $genAvg ? ($genAvg >= 75 ? 'PASSED' : 'FAILED') : 'PENDING';

                $studentSummaries[$student->id] = [
                    'general_average' => $genAvg,
                    'remarks' => $remarks
                ];

                if ($genAvg) {
                    if ($genAvg >= 75) $classStats['passed_count']++;
                    else $classStats['failed_count']++;
                } else {
                    $classStats['pending_count']++;
                }
            }

            $classStats['total_students'] = $enrolledStudents->count();

            $validAvgs = collect($studentSummaries)->pluck('general_average')->filter();
            $classStats['class_average'] = $validAvgs->isNotEmpty() ? round($validAvgs->avg(), 2) : 0;
        }

        return view('senior_high_school.grades.advisory', compact(
            'teacher',
            'advisorySections',
            'currentSection',
            'sectionSubjects',
            'enrolledStudents',
            'selectedSemester',
            'selectedPeriod',
            'periods',
            'gradesMatrix',
            'quarterGradesMatrix',
            'studentSummaries',
            'classStats',
            'isAdviser',
            'activeSchoolYear'
        ));
    }

    public function printReportCard($student_id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $student = Student::with(['user', 'educationLevel', 'gradeLevel', 'course', 'enrollments.classSection.schoolYear'])->findOrFail($student_id);

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['classSection.gradeLevel', 'classSection.course', 'classSection.adviser', 'schoolYear'])
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'No active enrollment record found for student.');
        }

        $classSection = $enrollment->classSection;

        $sectionSubjects = ClassSectionSubject::where('class_section_id', $classSection->id)
            ->whereHas('subject', fn($sq) => $sq->whereNull('parent_subject_id'))
            ->with(['subject', 'teacher'])
            ->get();

        $allSectionSubjectIds = ClassSectionSubject::where('class_section_id', $classSection->id)->pluck('id');
        $studentEnrollmentIds = Enrollment::where('student_id', $student->id)->pluck('id');

        $grades = Grade::whereIn('enrollment_id', $studentEnrollmentIds)
            ->whereIn('class_section_subject_id', $allSectionSubjectIds)
            ->get();

        $periods = ['Prelim', 'Midterm', 'Finals'];

        $reportCardData = [];
        $firstSemAvgs = [];
        $secondSemAvgs = [];
        $yearlyAvgs = [];

        foreach ($sectionSubjects as $css) {
            $subjectGrades = [];
            foreach ($periods as $p) {
                $g = $grades->where('class_section_subject_id', $css->id)->where('academic_period', $p)->first();
                $subjectGrades[$p] = ($g && $g->final_grade !== null) ? floatval($g->final_grade) : null;
            }

            $prelim = $subjectGrades['Prelim'];
            $midterm = $subjectGrades['Midterm'];
            $finals = $subjectGrades['Finals'];

            $validPeriods = collect([$prelim, $midterm, $finals])->filter();
            $finalRating = $validPeriods->isNotEmpty() ? round($validPeriods->avg(), 2) : null;

            if ($finalRating) $yearlyAvgs[] = $finalRating;

            $reportCardData[] = [
                'subject' => $css->subject,
                'teacher' => $css->teacher,
                'grades' => $subjectGrades,
                'final_rating' => $finalRating,
                'remarks' => $finalRating ? ($finalRating >= 75 ? 'PASSED' : 'FAILED') : ''
            ];
        }

        $overallFinalRating = !empty($yearlyAvgs) ? round(array_sum($yearlyAvgs) / count($yearlyAvgs), 2) : null;

        return view('senior_high_school.grades.print.report_card', compact(
            'student',
            'enrollment',
            'classSection',
            'reportCardData',
            'overallFinalRating'
        ));
    }

    public function updateTaskScore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grading_task_id' => 'required|exists:grading_tasks,id',
            'score' => 'nullable|numeric|min:0',
        ]);

        $task = GradingTask::findOrFail($validated['grading_task_id']);

        if (!is_null($validated['score']) && $validated['score'] > $task->max_score) {
            return response()->json([
                'success' => false,
                'message' => 'Score cannot exceed max score of ' . $task->max_score
            ], 422);
        }

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::where('student_id', $validated['student_id'])->latest()->first();
        }

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student enrollment record not found.'
            ], 404);
        }

        $score = StudentTaskScore::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'grading_task_id' => $task->id,
            ],
            [
                'score' => $validated['score']
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Score updated successfully.',
            'score' => $score->score
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'category_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create([
            'class_section_subject_id' => $validated['class_section_subject_id'],
            'academic_period' => $validated['academic_period'],
            'category_name' => $validated['category_name'],
            'weight' => $validated['weight'],
        ]);

        return redirect()->back()->with('success', 'Grading category added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = GradingCategory::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
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
            'task_name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:1',
        ]);

        GradingTask::create([
            'grading_category_id' => $validated['grading_category_id'],
            'task_name' => $validated['task_name'],
            'max_score' => $validated['max_score'],
        ]);

        return redirect()->back()->with('success', 'Task added successfully!');
    }

    public function updateTask(Request $request, $id)
    {
        $task = GradingTask::findOrFail($id);

        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
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
            'academic_period' => 'required|string',
            'attendance_date' => 'required|date',
        ]);

        $css = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($css, $activeSchoolYear) {
            $q->where('class_section_id', $css->class_section_id)
              ->whereIn('status', ['enrolled', 'active']);
            if ($activeSchoolYear) {
                $q->where('school_year_id', $activeSchoolYear->id);
            }
        })->get();

        foreach ($enrolledStudents as $student) {
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('class_section_id', $css->class_section_id)
                ->first();

            if ($enrollment) {
                Attendance::firstOrCreate([
                    'class_section_subject_id' => $css->id,
                    'academic_period' => $validated['academic_period'],
                    'attendance_date' => $validated['attendance_date'],
                    'enrollment_id' => $enrollment->id,
                ], [
                    'status' => 'Present'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Attendance date column added successfully!');
    }

    public function updateAttendanceStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Late,Absent,Excused,P,L,A,E,AEL,C',
        ]);

        $statusMap = [
            'P' => 'Present',
            'L' => 'Late',
            'A' => 'Absent',
            'E' => 'Excused',
            'AEL' => 'Excused',
            'C' => 'Absent',
            'Present' => 'Present',
            'Late' => 'Late',
            'Absent' => 'Absent',
            'Excused' => 'Excused',
        ];
        $finalStatus = $statusMap[$validated['status']] ?? 'Present';

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::where('student_id', $validated['student_id'])->latest()->first();
        }

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found.'], 404);
        }

        $att = Attendance::updateOrCreate([
            'class_section_subject_id' => $validated['class_section_subject_id'],
            'academic_period' => $validated['academic_period'],
            'attendance_date' => $validated['attendance_date'],
            'enrollment_id' => $enrollment->id,
        ], [
            'status' => $finalStatus
        ]);

        return response()->json(['success' => true, 'message' => 'Attendance status updated.', 'status' => $att->status]);
    }

    public function destroyAttendanceDate(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'attendance_date' => 'required|date',
        ]);

        Attendance::where('class_section_subject_id', $validated['class_section_subject_id'])
            ->where('academic_period', $validated['academic_period'])
            ->where('attendance_date', $validated['attendance_date'])
            ->delete();

        return redirect()->back()->with('success', 'Attendance column removed successfully!');
    }

    public function computeTotalGrades(Request $request)
    {
        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'grades' => 'required|array',
        ]);

        foreach ($validated['grades'] as $studentId => $finalGrade) {
            if (!is_null($finalGrade) && $finalGrade !== '') {
                $numGrade = floatval($finalGrade);
                $remarks = $numGrade >= 75 ? 'PASSED' : 'FAILED';

                Grade::updateOrCreate([
                    'student_id' => $studentId,
                    'class_section_subject_id' => $validated['class_section_subject_id'],
                    'academic_period' => $validated['academic_period'],
                ], [
                    'final_quarter_grade' => $numGrade,
                    'remarks' => $remarks
                ]);
            }
        }

        return redirect()->back()->with('success', 'Grades saved and updated successfully!');
    }
}
