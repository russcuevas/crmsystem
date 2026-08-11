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

            // Attendance Records (Filtered by Quarter/Academic Period)
            $attendanceDates = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
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

        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with('gradeLevel')
            ->get();
        $isAdviser = $advisorySections->isNotEmpty();

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
            'activeSchoolYear',
            'advisorySections',
            'isAdviser'
        ));
    }

    public function JuniorHighSchoolAdvisoryGradePage(Request $request)
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

        // Advisory Sections for this teacher
        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['gradeLevel', 'schoolYear'])
            ->get();

        $isAdviser = $advisorySections->isNotEmpty();

        $selectedSectionId = $request->input('class_section_id');
        $selectedPeriod = $request->input('academic_period', '1st Quarter');

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

        if ($currentSection) {
            // All subjects assigned to this class section (regardless of who teaches them)
            $sectionSubjects = ClassSectionSubject::where('class_section_id', $currentSection->id)
                ->with(['subject', 'teacher'])
                ->get();

            $subjectIds = $sectionSubjects->pluck('id');

            // Enrolled students in this advisory section
            $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($currentSection, $activeSchoolYear) {
                $q->where('class_section_id', $currentSection->id)
                  ->where('status', 'enrolled');
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->with('user')->get();

            // Fetch all published grades for these subjects
            $allGrades = Grade::whereIn('class_section_subject_id', $subjectIds)
                ->with('enrollment')
                ->get();

            $periods = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

            foreach ($enrolledStudents as $student) {
                $studentGrades = [];
                $qGradesForStudent = [];
                $validGradesForGwa = [];
                $hasFailingGrade = false;
                $hasPendingGrade = false;
                $passedCount = 0;
                $failedCount = 0;

                foreach ($sectionSubjects as $secSub) {
                    // Match grade by enrollment->student_id
                    $subGrades = $allGrades->filter(function ($g) use ($student, $secSub) {
                        $matchStudent = ($g->enrollment && $g->enrollment->student_id == $student->id);
                        return $matchStudent && $g->class_section_subject_id == $secSub->id;
                    });

                    // Build 4 quarters breakdown
                    $qGradesMap = [];
                    foreach ($periods as $p) {
                        $gP = $subGrades->firstWhere('academic_period', $p);
                        $qGradesMap[$p] = ($gP && $gP->final_grade !== null) ? floatval($gP->final_grade) : null;
                    }
                    $qGradesForStudent[$secSub->id] = $qGradesMap;

                    if ($selectedPeriod === 'All Quarters') {
                        $nonNullQGrades = array_filter($qGradesMap, fn($v) => !is_null($v));
                        if (count($nonNullQGrades) > 0) {
                            $avgSubjectGrade = round(array_sum($nonNullQGrades) / count($nonNullQGrades), 2);
                            $studentGrades[$secSub->id] = $avgSubjectGrade;
                            $validGradesForGwa[] = $avgSubjectGrade;
                            if ($avgSubjectGrade >= 75) {
                                $passedCount++;
                            } else {
                                $failedCount++;
                                $hasFailingGrade = true;
                            }
                        } else {
                            $studentGrades[$secSub->id] = null;
                            $hasPendingGrade = true;
                        }
                    } else {
                        $gP = $subGrades->firstWhere('academic_period', $selectedPeriod);
                        if ($gP && $gP->final_grade !== null) {
                            $gradeVal = floatval($gP->final_grade);
                            $studentGrades[$secSub->id] = $gradeVal;
                            $validGradesForGwa[] = $gradeVal;
                            if ($gradeVal >= 75) {
                                $passedCount++;
                            } else {
                                $failedCount++;
                                $hasFailingGrade = true;
                            }
                        } else {
                            $studentGrades[$secSub->id] = null;
                            $hasPendingGrade = true;
                        }
                    }
                }

                $gradesMatrix[$student->id] = $studentGrades;
                $quarterGradesMatrix[$student->id] = $qGradesForStudent;

                $gwa = count($validGradesForGwa) > 0 ? round(array_sum($validGradesForGwa) / count($validGradesForGwa), 2) : null;

                $remarks = 'Pending';
                if ($gwa !== null) {
                    if ($hasFailingGrade || $gwa < 75) {
                        $remarks = 'Failed';
                    } else if (!$hasPendingGrade && count($validGradesForGwa) === $sectionSubjects->count()) {
                        $remarks = 'Passed';
                    } else {
                        $remarks = ($gwa >= 75) ? 'Passed (Partial)' : 'Failed';
                    }
                }

                $studentSummaries[$student->id] = [
                    'gwa' => $gwa,
                    'remarks' => $remarks,
                    'passed_count' => $passedCount,
                    'failed_count' => $failedCount,
                    'pending_count' => $sectionSubjects->count() - ($passedCount + $failedCount),
                ];
            }

            $totalStudents = $enrolledStudents->count();
            $gwas = array_filter(array_column($studentSummaries, 'gwa'), fn($v) => !is_null($v));
            $classAvg = count($gwas) > 0 ? round(array_sum($gwas) / count($gwas), 2) : 0;
            $passedCount = count(array_filter($studentSummaries, fn($s) => str_contains($s['remarks'], 'Passed')));
            $failedCount = count(array_filter($studentSummaries, fn($s) => $s['remarks'] === 'Failed'));
            $pendingCount = $totalStudents - ($passedCount + $failedCount);

            $classStats = [
                'total_students' => $totalStudents,
                'class_average' => $classAvg,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount,
                'pending_count' => max(0, $pendingCount),
            ];
        }

        return view('junior_high_school.grades.advisory', compact(
            'teacher',
            'activeSchoolYear',
            'advisorySections',
            'isAdviser',
            'currentSection',
            'selectedPeriod',
            'sectionSubjects',
            'enrolledStudents',
            'gradesMatrix',
            'quarterGradesMatrix',
            'studentSummaries',
            'classStats'
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
            'academic_period' => 'required|string',
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
                'academic_period' => $validated['academic_period'],
                'attendance_date' => $validated['attendance_date'],
            ], [
                'status' => 'P',
            ]);
        }

        return redirect()->back()->with('success', 'Attendance date session added!');
    }

    public function updateAttendanceStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'nullable|string',
            'attendance_date' => 'required|date',
            'status' => 'required|in:P,L,A,AEL,E,C,present,absent,late,excused,excused_letter,cutting',
        ]);

        $sectionSubject = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);
        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $sectionSubject->class_section_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Student enrollment record not found.'], 404);
        }

        $period = $request->input('academic_period', '1st Quarter');

        $attendance = Attendance::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'class_section_subject_id' => $sectionSubject->id,
                'academic_period' => $period,
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
            'academic_period' => 'required|string',
            'attendance_date' => 'required|date',
        ]);

        Attendance::where('class_section_subject_id', $validated['class_section_subject_id'])
            ->where('academic_period', $validated['academic_period'])
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
                    'enrollment_id' => $enr->id,
                    'class_section_subject_id' => $sectionSubject->id,
                    'academic_period' => $validated['academic_period'],
                ],
                [
                    'final_grade' => $computedScore,
                    'remarks' => $remarks,
                ]
            );
        }

        return redirect()->back()->with('success', 'Grades computed and published successfully!');
    }

    public function printReportCard(Request $request, $student_id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $student = Student::with('user')->findOrFail($student_id);

        // Find enrollment
        $enrollmentQuery = Enrollment::where('student_id', $student->id)->where('status', 'enrolled');
        if ($request->filled('class_section_id')) {
            $enrollmentQuery->where('class_section_id', $request->input('class_section_id'));
        }
        if ($activeSchoolYear) {
            $enrollmentQuery->where('school_year_id', $activeSchoolYear->id);
        }

        $enrollment = $enrollmentQuery->with(['classSection.gradeLevel', 'classSection.adviser'])->first();
        if (!$enrollment) {
            $enrollment = Enrollment::where('student_id', $student->id)->with(['classSection.gradeLevel', 'classSection.adviser'])->latest()->first();
        }

        $currentSection = $enrollment ? $enrollment->classSection : null;

        $sectionSubjects = collect();
        $gradesBySubject = [];
        $gwa = null;
        $remarks = 'Pending';

        if ($currentSection) {
            $sectionSubjects = ClassSectionSubject::where('class_section_id', $currentSection->id)
                ->with(['subject', 'teacher'])
                ->get();

            $subjectIds = $sectionSubjects->pluck('id');
            $studentEnrollmentIds = Enrollment::where('student_id', $student->id)->pluck('id');

            $allGrades = Grade::whereIn('class_section_subject_id', $subjectIds)
                ->whereIn('enrollment_id', $studentEnrollmentIds)
                ->get();

            $periods = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            $subjectAverages = [];

            foreach ($sectionSubjects as $secSub) {
                $subGrades = $allGrades->where('class_section_subject_id', $secSub->id);
                $qGrades = [];
                foreach ($periods as $p) {
                    $gObj = $subGrades->firstWhere('academic_period', $p);
                    $qGrades[$p] = ($gObj && $gObj->final_grade !== null) ? floatval($gObj->final_grade) : null;
                }

                $nonNull = array_filter($qGrades, fn($v) => !is_null($v));
                $finalGrade = count($nonNull) > 0 ? round(array_sum($nonNull) / count($nonNull), 2) : null;
                $subRemarks = $finalGrade !== null ? ($finalGrade >= 75 ? 'Passed' : 'Failed') : '';

                $gradesBySubject[$secSub->id] = [
                    'q1' => $qGrades['1st Quarter'],
                    'q2' => $qGrades['2nd Quarter'],
                    'q3' => $qGrades['3rd Quarter'],
                    'q4' => $qGrades['4th Quarter'],
                    'final_grade' => $finalGrade,
                    'remarks' => $subRemarks,
                ];

                if ($finalGrade !== null) {
                    $subjectAverages[] = $finalGrade;
                }
            }

            if (count($subjectAverages) > 0) {
                $gwa = round(array_sum($subjectAverages) / count($subjectAverages), 2);
                $hasFailing = count(array_filter($subjectAverages, fn($v) => $v < 75)) > 0;
                $remarks = ($gwa >= 75 && !$hasFailing) ? 'Passed' : 'Failed';
            }
        }

        return view('junior_high_school.grades.print.print_card', compact(
            'student',
            'enrollment',
            'currentSection',
            'activeSchoolYear',
            'sectionSubjects',
            'gradesBySubject',
            'gwa',
            'remarks'
        ));
    }
}
