<?php

namespace App\Http\Controllers\elementary;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingCategory;
use App\Models\GradingTask;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTaskScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementaryGradeController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        // Elementary section subjects assigned to teacher
        $sectionSubjects = ClassSectionSubject::where('teacher_id', $teacher->id)
            ->whereHas('classSection', function ($q) use ($activeSchoolYear) {
                $q->whereHas('gradeLevel.educationLevel', fn($sq) => $sq->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']));
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })
            ->with(['classSection.gradeLevel', 'subject'])
            ->get();

        $selectedSecSubId = $request->input('class_section_subject_id');
        $targetSectionSubject = null;

        if ($selectedSecSubId) {
            $targetSectionSubject = $sectionSubjects->firstWhere('id', $selectedSecSubId);
        }

        if (!$targetSectionSubject && $sectionSubjects->isNotEmpty()) {
            $targetSectionSubject = $sectionSubjects->first();
        }

        $periods = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
        $selectedPeriod = $request->input('academic_period', '1st Quarter');

        $categories = collect();
        $enrolledStudents = collect();
        $scores = [];
        $computedGrades = [];
        $attendanceDates = collect();
        $attendances = [];
        $targetSecSubId = $targetSectionSubject ? $targetSectionSubject->id : null;

        if ($targetSectionSubject) {
            $classSectionId = $targetSectionSubject->class_section_id;

            // Categories & tasks for selected section subject & period
            $categories = GradingCategory::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->with(['gradingTasks' => function ($q) {
                    $q->orderBy('id', 'asc');
                }])
                ->get();

            // Enrolled students in section
            $enrolledStudents = Student::whereHas('enrollments', function ($q) use ($classSectionId, $activeSchoolYear) {
                $q->where('class_section_id', $classSectionId)
                  ->whereIn('status', ['enrolled', 'active']);
                if ($activeSchoolYear) {
                    $q->where('school_year_id', $activeSchoolYear->id);
                }
            })->with('user')->get();

            $studentEnrollments = Enrollment::where('class_section_id', $classSectionId)
                ->whereIn('status', ['enrolled', 'active'])
                ->get();
            $enrollmentIds = $studentEnrollments->pluck('id');

            // Load scores
            $allTasks = $categories->pluck('gradingTasks')->flatten();
            $taskIds = $allTasks->pluck('id');

            $rawScores = StudentTaskScore::whereIn('enrollment_id', $enrollmentIds)
                ->whereIn('grading_task_id', $taskIds)
                ->get();

            foreach ($rawScores as $sc) {
                $enr = $studentEnrollments->firstWhere('id', $sc->enrollment_id);
                if ($enr) {
                    $scores[$enr->student_id . '_' . $sc->grading_task_id] = $sc;
                }
            }

            // Saved grades for current period
            $savedGrades = Grade::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get();

            foreach ($savedGrades as $sg) {
                $enr = $studentEnrollments->firstWhere('id', $sg->enrollment_id);
                if ($enr) {
                    $computedGrades[$enr->student_id] = $sg;
                }
            }

            // Attendance sessions & status
            $attendanceDates = Attendance::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date')
                ->unique();

            $rawAttendances = Attendance::where('class_section_subject_id', $targetSectionSubject->id)
                ->where('academic_period', $selectedPeriod)
                ->get();

            foreach ($rawAttendances as $att) {
                $enr = $studentEnrollments->firstWhere('id', $att->enrollment_id);
                if ($enr) {
                    $attKey = $enr->student_id . '_' . $att->attendance_date->format('Y-m-d');
                    $attendances[$attKey] = $att;
                }
            }
        }

        // Prepare JSON structure for JS recalculateRow
        $categoriesData = $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'weight' => floatval($cat->weight),
                'tasks' => $cat->gradingTasks->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'max_score' => floatval($t->max_score),
                    ];
                }),
            ];
        });

        return view('elementary.grades.index', compact(
            'teacher',
            'sectionSubjects',
            'targetSectionSubject',
            'targetSecSubId',
            'periods',
            'selectedPeriod',
            'categories',
            'categoriesData',
            'enrolledStudents',
            'scores',
            'computedGrades',
            'attendanceDates',
            'attendances',
            'activeSchoolYear'
        ));
    }

    public function storeCategory(Request $request)
    {
        $categoryName = $request->input('name') ?? $request->input('category_name');
        $request->merge(['name' => $categoryName]);

        $validated = $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create([
            'class_section_subject_id' => $validated['class_section_subject_id'],
            'academic_period' => $validated['academic_period'],
            'name' => $validated['name'],
            'weight' => $validated['weight'],
        ]);

        return redirect()->back()->with('success', 'Grading category added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = GradingCategory::findOrFail($id);

        $categoryName = $request->input('name') ?? $request->input('category_name');
        $request->merge(['name' => $categoryName]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $category->update([
            'name' => $validated['name'],
            'weight' => $validated['weight'],
        ]);

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

        GradingTask::create($validated);

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

    public function updateScore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grading_task_id' => 'required|exists:grading_tasks,id',
            'score' => 'nullable|numeric|min:0',
        ]);

        $task = GradingTask::findOrFail($validated['grading_task_id']);
        $category = $task->gradingCategory;
        $css = $category->classSectionSubject;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_section_id', $css->class_section_id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::where('student_id', $validated['student_id'])->latest()->first();
        }

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment record not found.'], 404);
        }

        $scoreVal = (is_null($validated['score']) || $validated['score'] === '') ? null : floatval($validated['score']);

        $score = StudentTaskScore::updateOrCreate([
            'enrollment_id' => $enrollment->id,
            'grading_task_id' => $validated['grading_task_id'],
        ], [
            'score' => $scoreVal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Score updated successfully.',
            'score' => $score->score
        ]);
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
            'grades' => 'nullable|array',
        ]);

        $sectionSubject = ClassSectionSubject::findOrFail($validated['class_section_subject_id']);

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        $enrolments = Enrollment::where('class_section_id', $sectionSubject->class_section_id)
            ->whereIn('status', ['enrolled', 'active'])
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with('student')
            ->get();

        if (empty($enrolments) || $enrolments->isEmpty()) {
            $enrolments = Enrollment::where('class_section_id', $sectionSubject->class_section_id)
                ->whereIn('status', ['enrolled', 'active'])
                ->with('student')
                ->get();
        }

        // If manual grades array is provided
        if (!empty($validated['grades'])) {
            foreach ($validated['grades'] as $studentId => $finalGrade) {
                if (!is_null($finalGrade) && $finalGrade !== '') {
                    $numGrade = floatval($finalGrade);
                    $remarks = $numGrade >= 75 ? 'Passed' : 'Failed';

                    $enr = $enrolments->firstWhere('student_id', $studentId);
                    if ($enr) {
                        Grade::updateOrCreate([
                            'enrollment_id' => $enr->id,
                            'class_section_subject_id' => $validated['class_section_subject_id'],
                            'academic_period' => $validated['academic_period'],
                        ], [
                            'final_grade' => $numGrade,
                            'remarks' => $remarks
                        ]);
                    }
                }
            }
            return redirect()->back()->with('success', 'Grades saved and published successfully!');
        }

        // Otherwise compute automatically from grading categories & task scores
        $categories = GradingCategory::where('class_section_subject_id', $sectionSubject->id)
            ->where('academic_period', $validated['academic_period'])
            ->with('gradingTasks')
            ->get();

        $hasTasksInPeriod = false;
        foreach ($categories as $cat) {
            if ($cat->gradingTasks && $cat->gradingTasks->count() > 0) {
                $hasTasksInPeriod = true;
                break;
            }
        }

        foreach ($enrolments as $enr) {
            if (!$enr->student) continue;

            if (!$hasTasksInPeriod) {
                Grade::where('enrollment_id', $enr->id)
                    ->where('class_section_subject_id', $sectionSubject->id)
                    ->where('academic_period', $validated['academic_period'])
                    ->delete();
                continue;
            }

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

        // Auto-calculate parent subject grade if sectionSubject is part of a parent subject
        $subjectModel = $sectionSubject->subject;
        $parentSubjectId = $subjectModel ? ($subjectModel->parent_subject_id ?? ($subjectModel->is_parent ? $subjectModel->id : null)) : null;

        if ($parentSubjectId) {
            $parentSectionSubject = ClassSectionSubject::where('class_section_id', $sectionSubject->class_section_id)
                ->whereHas('subject', fn($q) => $q->where('id', $parentSubjectId)->orWhere('is_parent', true))
                ->first();

            $childSectionSubjects = ClassSectionSubject::where('class_section_id', $sectionSubject->class_section_id)
                ->whereHas('subject', fn($q) => $q->where('parent_subject_id', $parentSubjectId))
                ->get();

            if ($parentSectionSubject && $childSectionSubjects->isNotEmpty()) {
                $childSubjectIds = $childSectionSubjects->pluck('id');

                foreach ($enrolments as $enr) {
                    $childGrades = Grade::where('enrollment_id', $enr->id)
                        ->whereIn('class_section_subject_id', $childSubjectIds)
                        ->where('academic_period', $validated['academic_period'])
                        ->pluck('final_grade')
                        ->filter(fn($v) => !is_null($v) && $v > 0);

                    if ($childGrades->isNotEmpty()) {
                        $mapehComputedScore = round($childGrades->avg(), 2);
                        $mapehRemarks = $mapehComputedScore >= 75 ? 'Passed' : 'Failed';

                        Grade::updateOrCreate(
                            [
                                'enrollment_id' => $enr->id,
                                'class_section_subject_id' => $parentSectionSubject->id,
                                'academic_period' => $validated['academic_period'],
                            ],
                            [
                                'final_grade' => $mapehComputedScore,
                                'remarks' => $mapehRemarks,
                            ]
                        );
                    } else {
                        Grade::where('enrollment_id', $enr->id)
                            ->where('class_section_subject_id', $parentSectionSubject->id)
                            ->where('academic_period', $validated['academic_period'])
                            ->delete();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Grades computed and published successfully!');
    }

    public function advisoryClassGrades(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        // Advisory sections for Elementary teacher
        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->whereHas('gradeLevel.educationLevel', fn($q) => $q->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']))
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['gradeLevel', 'schoolYear'])
            ->get();

        $isAdviser = $advisorySections->isNotEmpty();

        $selectedSecId = $request->input('class_section_id');
        $currentSection = null;

        if ($selectedSecId) {
            $currentSection = $advisorySections->firstWhere('id', $selectedSecId);
        }

        if (!$currentSection && $advisorySections->isNotEmpty()) {
            $currentSection = $advisorySections->first();
        }

        $periods = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
        $selectedPeriod = $request->input('academic_period', '1st Quarter');

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
            'pending_count' => 0
        ];

        if ($currentSection) {
            $sectionSubjects = ClassSectionSubject::where('class_section_id', $currentSection->id)
                ->whereHas('subject', fn($sq) => $sq->whereNull('parent_subject_id'))
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
            $studentEnrollments = Enrollment::where('class_section_id', $currentSection->id)
                ->whereIn('status', ['enrolled', 'active'])
                ->get();
            $enrollmentIds = $studentEnrollments->pluck('id');

            $allGrades = Grade::whereIn('class_section_subject_id', $allSectionSubjectIds)
                ->whereIn('enrollment_id', $enrollmentIds)
                ->get();

            foreach ($enrolledStudents as $student) {
                $enr = $studentEnrollments->firstWhere('student_id', $student->id);
                $enrId = $enr ? $enr->id : null;

                $studentSubjectFinalRatings = [];

                foreach ($sectionSubjects as $css) {
                    $periodGrades = [];
                    foreach ($periods as $p) {
                        $pGrade = $allGrades->where('enrollment_id', $enrId)
                            ->where('class_section_subject_id', $css->id)
                            ->where('academic_period', $p)
                            ->first();

                        $val = ($pGrade && !is_null($pGrade->final_grade)) ? floatval($pGrade->final_grade) : null;
                        $quarterGradesMatrix[$student->id][$css->id][$p] = $val;

                        if ($val !== null) {
                            $periodGrades[] = $val;
                        }
                    }

                    $stGradeVal = $quarterGradesMatrix[$student->id][$css->id][$selectedPeriod] ?? null;
                    $gradesMatrix[$student->id][$css->id] = $stGradeVal;

                    if (!empty($periodGrades)) {
                        $subjFinal = round(array_sum($periodGrades) / count($periodGrades), 2);
                        $studentSubjectFinalRatings[] = $subjFinal;
                    }
                }

                $genAvg = !empty($studentSubjectFinalRatings) ? round(array_sum($studentSubjectFinalRatings) / count($studentSubjectFinalRatings), 2) : null;
                $remarks = $genAvg ? ($genAvg >= 75 ? 'Passed' : 'Failed') : 'Pending';

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

        return view('elementary.grades.advisory', compact(
            'teacher',
            'advisorySections',
            'currentSection',
            'sectionSubjects',
            'enrolledStudents',
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

        $student = Student::with(['user', 'gradeLevel'])->findOrFail($student_id);

        // Find enrollment
        $enrollmentQuery = Enrollment::where('student_id', $student->id)->whereIn('status', ['enrolled', 'active']);
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
                    $val = ($gObj && $gObj->final_grade !== null) ? floatval($gObj->final_grade) : null;
                    $qGrades[$p] = ($val !== null && $val > 0) ? $val : null;
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
                $gwa = round(array_sum($subjectAverages) / count($subjectAverages));
                $hasFailing = count(array_filter($subjectAverages, fn($v) => $v < 75)) > 0;
                $remarks = ($gwa >= 75 && !$hasFailing) ? 'Passed' : 'Failed';
            }
        }

        return view('elementary.grades.print.print_card', compact(
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
