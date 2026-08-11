<?php

namespace App\Http\Controllers\admins;

use App\Http\Controllers\Controller;
use App\Models\ClassSectionSubject;
use App\Models\GradingCategory;
use App\Models\GradingTask;
use App\Models\StudentTaskScore;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Http\Request;

class AdminGradeController extends Controller
{
    public function AdminGradePage()
    {
        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            $activeSchoolYear = SchoolYear::first();
        }

        $selectedLevel = request('level');
        $selectedSubjectId = request('section_subject_id');
        $selectedPeriod = request('academic_period');
        $selectedSemester = request('semester', '1st Semester');
        $educationLevelsList = EducationLevel::all();

        $subjectQuery = ClassSectionSubject::with([
            'classSection.gradeLevel.educationLevel',
            'subject',
            'teacher'
        ]);

        if ($activeSchoolYear) {
            $subjectQuery->whereHas('classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id));
        }

        if ($selectedLevel) {
            $subjectQuery->whereHas('classSection.gradeLevel.educationLevel', fn($q) => $q->where('code', $selectedLevel));
        }

        if (in_array(strtoupper($selectedLevel), ['SHS', 'COLLEGE'])) {
            $semKey = ($selectedSemester == '2nd Semester') ? '2nd' : '1st';
            $subjectQuery->whereHas('subject', function ($sq) use ($semKey, $selectedSemester) {
                $sq->where('semester', 'LIKE', '%' . $semKey . '%')
                  ->orWhere('semester', $selectedSemester)
                  ->orWhereNull('semester');
            });
        }

        $subjectQuery->whereHas('subject', fn($sq) => $sq->whereNull('parent_subject_id'));

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
        $availablePeriods = collect();
        $attendanceDates = collect();
        $attendances = collect();
        $subSectionSubjects = collect();
        $selectedSubSubjectId = request('sub_subject_id');
        $activeSubSectionSubject = null;
        $isParentSubject = false;
        $mapehSummaryGrades = collect();

        if ($currentSectionSubject) {
            $levelCode = $currentSectionSubject->classSection->gradeLevel->educationLevel->code ?? '';

            if (in_array(strtoupper($levelCode), ['BED', 'JHS', 'ELEM', 'ELEMENTARY'])) {
                $defaultPeriods = collect(['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter']);
            } else {
                $defaultPeriods = collect(['Prelim', 'Midterm', 'Finals']);
            }

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

            $categoriesQuery = GradingCategory::where('class_section_subject_id', $targetSectionSubject->id)
                ->with(['gradingTasks.studentTaskScores']);

            if ($selectedPeriod) {
                $categoriesQuery->where('academic_period', $selectedPeriod);
            }

            $categories = $categoriesQuery->get();

            $enrolledQuery = Enrollment::where('class_section_id', $currentSectionSubject->class_section_id)
                ->where('school_year_id', $currentSectionSubject->classSection->school_year_id ?? ($activeSchoolYear->id ?? null));

            if (in_array($levelCode, ['SHS', 'COLLEGE'])) {
                $enrolledQuery->where(function ($q) use ($selectedSemester) {
                    $q->where('semester', $selectedSemester)
                      ->orWhereNull('semester');
                });
            }

            $enrolledStudents = $enrolledQuery->with(['student.user', 'taskScores'])->get();

            $attendanceDatesQuery = Attendance::where('class_section_subject_id', $targetSectionSubject->id);
            $attendancesQuery = Attendance::where('class_section_subject_id', $targetSectionSubject->id);

            if ($selectedPeriod) {
                $attendanceDatesQuery->where('academic_period', $selectedPeriod);
                $attendancesQuery->where('academic_period', $selectedPeriod);
            }

            $attendanceDates = $attendanceDatesQuery->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = $attendancesQuery->get();
            $savedGrades = Grade::where('class_section_subject_id', $targetSectionSubject->id)->get();

            if ($isParentSubject) {
                $allSectionSubjectIds = $subSectionSubjects->pluck('id')->push($currentSectionSubject->id);
                $mapehSummaryGrades = Grade::whereIn('class_section_subject_id', $allSectionSubjectIds)
                    ->where('academic_period', $selectedPeriod)
                    ->get();
            }
        } else {
            $savedGrades = collect();
        }

        $totalAccounts = User::whereNotIn('role', ['superadmin', 'admin'])->count();
        if ($activeSchoolYear) {
            $totalFaculty = Teacher::whereHas('advisedClassSections', fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
                ->orWhereHas('classSectionSubjects.classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
                ->distinct()->count();
            $totalStudents = Student::whereHas('enrollments', fn($q) => $q->where('school_year_id', $activeSchoolYear->id))->distinct()->count();
            $totalSections = ClassSection::where('school_year_id', $activeSchoolYear->id)->count();
            $totalSubjects = Subject::whereHas('classSectionSubjects.classSection', fn($q) => $q->where('school_year_id', $activeSchoolYear->id))->distinct()->count();
        } else {
            $totalFaculty = Teacher::count();
            $totalStudents = Student::count();
            $totalSubjects = Subject::count();
            $totalSections = ClassSection::count();
        }

        return view('admins.grades.index', compact(
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
            'subSectionSubjects',
            'activeSubSectionSubject',
            'isParentSubject',
            'mapehSummaryGrades',
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
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'academic_period' => 'required|string',
            'name' => 'required|string|max:255',
            'component_type' => 'nullable|in:lecture,laboratory,general',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create([
            'class_section_subject_id' => $request->class_section_subject_id,
            'academic_period' => $request->academic_period,
            'name' => $request->name,
            'component_type' => $request->component_type ?? 'general',
            'weight' => $request->weight,
        ]);

        return back()->with('success', 'Grading Category added successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'academic_period' => 'required|string',
            'name' => 'required|string|max:255',
            'component_type' => 'nullable|in:lecture,laboratory,general',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $category = GradingCategory::findOrFail($id);
        $category->update([
            'academic_period' => $request->academic_period,
            'name' => $request->name,
            'component_type' => $request->component_type ?? 'general',
            'weight' => $request->weight,
        ]);

        return back()->with('success', 'Grading Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = GradingCategory::findOrFail($id);
        $taskIds = $category->gradingTasks()->pluck('id');
        StudentTaskScore::whereIn('grading_task_id', $taskIds)->delete();
        $category->gradingTasks()->delete();
        $category->delete();

        return back()->with('success', 'Grading Category deleted successfully.');
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'grading_category_id' => 'required|exists:grading_categories,id',
            'task_name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'task_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        GradingTask::create([
            'grading_category_id' => $request->grading_category_id,
            'task_name' => $request->task_name,
            'max_score' => $request->max_score,
            'task_date' => $request->task_date,
            'description' => $request->description,
            'status' => 'graded',
        ]);

        return back()->with('success', 'Grading Task added successfully.');
    }

    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'grading_category_id' => 'required|exists:grading_categories,id',
            'task_name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'task_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $task = GradingTask::findOrFail($id);
        $task->update([
            'grading_category_id' => $request->grading_category_id,
            'task_name' => $request->task_name,
            'max_score' => $request->max_score,
            'task_date' => $request->task_date,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Grading Task updated successfully.');
    }

    public function destroyTask($id)
    {
        $task = GradingTask::findOrFail($id);
        StudentTaskScore::where('grading_task_id', $task->id)->delete();
        $task->delete();

        return back()->with('success', 'Grading Task deleted successfully.');
    }

    public function storeAttendanceDate(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
            'academic_period' => 'nullable|string',
        ]);

        $css = ClassSectionSubject::findOrFail($request->class_section_subject_id);
        $period = $request->input('academic_period', '1st Quarter');

        $enrolledStudents = Enrollment::where('class_section_id', $css->class_section_id)->get();

        foreach ($enrolledStudents as $enr) {
            Attendance::firstOrCreate(
                [
                    'class_section_subject_id' => $css->id,
                    'enrollment_id' => $enr->id,
                    'academic_period' => $period,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => 'P',
                    'remarks' => null,
                ]
            );
        }

        return back()->with('success', 'Attendance date ' . $request->attendance_date . ' added successfully.');
    }

    public function updateAttendanceStatus(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'attendance_date' => 'required|date',
            'academic_period' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $status = strtoupper(trim($request->status));
        $validStatuses = ['P', 'L', 'A', 'AEL', 'E', 'C'];
        if (!in_array($status, $validStatuses)) {
            $status = 'P';
        }

        $period = $request->input('academic_period', '1st Quarter');

        $att = Attendance::updateOrCreate(
            [
                'class_section_subject_id' => $request->class_section_subject_id,
                'enrollment_id' => $request->enrollment_id,
                'academic_period' => $period,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => $status,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance status updated successfully.',
            'attendance' => $att
        ]);
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

        return back()->with('success', 'Attendance column deleted.');
    }

    public function updateSubjectWeights(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'lecture_weight' => 'required|numeric|min:0|max:100',
            'lab_weight' => 'required|numeric|min:0|max:100',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $subject->update([
            'lecture_weight' => $request->lecture_weight,
            'lab_weight' => $request->lab_weight,
        ]);

        return back()->with('success', 'Subject Lec/Lab weights updated successfully to ' . number_format($request->lecture_weight, 0) . '% / ' . number_format($request->lab_weight, 0) . '%.');
    }

    public function computeTotalGrades(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
        ]);

        $subjectId = $request->input('class_section_subject_id');
        $currentSectionSubject = ClassSectionSubject::with(['classSection.gradeLevel.educationLevel', 'subject'])->findOrFail($subjectId);

        $levelCode = strtoupper($currentSectionSubject->classSection->gradeLevel->educationLevel->code ?? '');
        $hasLab = $currentSectionSubject->subject && $currentSectionSubject->subject->has_lab;
        $lecWeightRatio = ($currentSectionSubject->subject->lecture_weight ?? 70) / 100;
        $labWeightRatio = ($currentSectionSubject->subject->lab_weight ?? 30) / 100;

        if (in_array($levelCode, ['BED', 'JHS', 'ELEM', 'ELEMENTARY'])) {
            $periodsList = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
        } else {
            $periodsList = ['Prelim', 'Midterm', 'Finals'];
        }

        $allCategories = GradingCategory::where('class_section_subject_id', $subjectId)
            ->with(['gradingTasks.studentTaskScores'])
            ->get();

        $enrolledStudents = Enrollment::where('class_section_id', $currentSectionSubject->class_section_id)
            ->with(['student.user', 'taskScores'])
            ->get();

        $computedResults = [];

        foreach ($enrolledStudents as $enrollment) {
            $periodGrades = [];
            $sumPeriodGrades = 0;
            $validPeriodCount = 0;

            foreach ($periodsList as $period) {
                $periodCats = $allCategories->where('academic_period', $period);
                $hasTasks = false;
                foreach ($periodCats as $pCat) {
                    if ($pCat->gradingTasks && $pCat->gradingTasks->count() > 0) {
                        $hasTasks = true;
                        break;
                    }
                }

                if (!$hasTasks) {
                    Grade::where('enrollment_id', $enrollment->id)
                        ->where('class_section_subject_id', $subjectId)
                        ->where('academic_period', $period)
                        ->delete();

                    $periodGrades[$period] = null;
                    continue;
                }

                if ($hasLab) {
                    $lecCats = $periodCats->where('component_type', '!=', 'laboratory');
                    $labCats = $periodCats->where('component_type', 'laboratory');

                    $lecTotalWeight = $lecCats->sum('weight');
                    $labTotalWeight = $labCats->sum('weight');

                    $lecCategoryPctSum = 0;
                    foreach ($lecCats as $cat) {
                        $catEarned = 0;
                        $catMax = 0;
                        foreach ($cat->gradingTasks as $task) {
                            $ts = $enrollment->taskScores->where('grading_task_id', $task->id)->first();
                            if ($ts && $ts->score !== null) {
                                $catEarned += $ts->score;
                            }
                            $catMax += $task->max_score;
                        }
                        $lecCategoryPctSum += ($catMax > 0) ? ($catEarned / $catMax) * $cat->weight : 0;
                    }

                    $labCategoryPctSum = 0;
                    foreach ($labCats as $cat) {
                        $catEarned = 0;
                        $catMax = 0;
                        foreach ($cat->gradingTasks as $task) {
                            $ts = $enrollment->taskScores->where('grading_task_id', $task->id)->first();
                            if ($ts && $ts->score !== null) {
                                $catEarned += $ts->score;
                            }
                            $catMax += $task->max_score;
                        }
                        $labCategoryPctSum += ($catMax > 0) ? ($catEarned / $catMax) * $cat->weight : 0;
                    }

                    $lecSubtotal = ($lecTotalWeight > 0) ? ($lecCategoryPctSum / $lecTotalWeight) * 100 : $lecCategoryPctSum;
                    $labSubtotal = ($labTotalWeight > 0) ? ($labCategoryPctSum / $labTotalWeight) * 100 : $labCategoryPctSum;

                    $lecShare = $lecSubtotal * $lecWeightRatio;
                    $labShare = $labSubtotal * $labWeightRatio;

                    $periodFinalGrade = ($lecTotalWeight > 0 && $labTotalWeight > 0)
                        ? ($lecShare + $labShare)
                        : ($lecCategoryPctSum + $labCategoryPctSum);
                } else {
                    $generalPctSum = 0;
                    foreach ($periodCats as $cat) {
                        $catEarned = 0;
                        $catMax = 0;
                        foreach ($cat->gradingTasks as $task) {
                            $ts = $enrollment->taskScores->where('grading_task_id', $task->id)->first();
                            if ($ts && $ts->score !== null) {
                                $catEarned += $ts->score;
                            }
                            $catMax += $task->max_score;
                        }
                        $generalPctSum += ($catMax > 0) ? ($catEarned / $catMax) * $cat->weight : 0;
                    }
                    $periodFinalGrade = $generalPctSum;
                }

                $roundedPeriodGrade = round($periodFinalGrade, 2);
                $periodGrades[$period] = $roundedPeriodGrade;
                $sumPeriodGrades += $roundedPeriodGrade;
                $validPeriodCount++;

                Grade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'class_section_subject_id' => $subjectId,
                        'academic_period' => $period,
                    ],
                    [
                        'final_grade' => $roundedPeriodGrade,
                        'remarks' => $roundedPeriodGrade >= 75 ? 'Passed' : 'Failed',
                    ]
                );
            }

            if ($validPeriodCount > 0) {
                $subjectGrade = round($sumPeriodGrades / $validPeriodCount, 2);
                $remarks = $subjectGrade >= 75 ? 'Passed' : 'Failed';

                Grade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'class_section_subject_id' => $subjectId,
                        'academic_period' => 'Subject Grade',
                    ],
                    [
                        'final_grade' => $subjectGrade,
                        'remarks' => $remarks,
                    ]
                );
            } else {
                $subjectGrade = null;
                $remarks = 'Pending';
                Grade::where('enrollment_id', $enrollment->id)
                    ->where('class_section_subject_id', $subjectId)
                    ->where('academic_period', 'Subject Grade')
                    ->delete();
            }

            $computedResults[] = [
                'enrollment_id' => $enrollment->id,
                'student_number' => $enrollment->student->student_number ?? 'N/A',
                'student_name' => trim(($enrollment->student->first_name ?? '') . ' ' . ($enrollment->student->last_name ?? '')),
                'period_grades' => $periodGrades,
                'subject_grade' => $subjectGrade !== null ? number_format($subjectGrade, 2) : '-',
                'remarks' => $remarks,
            ];
        }

        if ($currentSectionSubject->subject && $currentSectionSubject->subject->parent_subject_id) {
            $parentSubjectId = $currentSectionSubject->subject->parent_subject_id;
            $parentSecSub = ClassSectionSubject::where('class_section_id', $currentSectionSubject->class_section_id)
                ->where('subject_id', $parentSubjectId)
                ->first();

            if ($parentSecSub) {
                $childSubjectIds = Subject::where('parent_subject_id', $parentSubjectId)->pluck('id');
                $childSecSubIds = ClassSectionSubject::where('class_section_id', $currentSectionSubject->class_section_id)
                    ->whereIn('subject_id', $childSubjectIds)
                    ->pluck('id');

                foreach ($enrolledStudents as $enrollment) {
                    $allPeriods = array_merge($periodsList, ['Subject Grade']);
                    foreach ($allPeriods as $pName) {
                        $childGrades = Grade::where('enrollment_id', $enrollment->id)
                            ->whereIn('class_section_subject_id', $childSecSubIds)
                            ->where('academic_period', $pName)
                            ->pluck('final_grade')
                            ->filter(fn($v) => !is_null($v) && $v > 0);

                        if ($childGrades->count() > 0) {
                            $avgGrade = round($childGrades->avg(), 2);
                            Grade::updateOrCreate(
                                [
                                    'enrollment_id' => $enrollment->id,
                                    'class_section_subject_id' => $parentSecSub->id,
                                    'academic_period' => $pName,
                                ],
                                [
                                    'final_grade' => $avgGrade,
                                    'remarks' => $avgGrade >= 75 ? 'Passed' : 'Failed',
                                ]
                            );
                        } else {
                            Grade::where('enrollment_id', $enrollment->id)
                                ->where('class_section_subject_id', $parentSecSub->id)
                                ->where('academic_period', $pName)
                                ->delete();
                        }
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Total grades for ' . ($currentSectionSubject->subject->subject_name ?? 'Subject') . ' computed and saved successfully!',
            'periods' => $periodsList,
            'results' => $computedResults,
        ]);
    }

    public function resetTotalGrades(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
        ]);

        $subjectId = $request->input('class_section_subject_id');
        $currentSectionSubject = ClassSectionSubject::with(['subject'])->findOrFail($subjectId);

        Grade::where('class_section_subject_id', $subjectId)->delete();

        if ($currentSectionSubject->subject && $currentSectionSubject->subject->parent_subject_id) {
            $parentSubjectId = $currentSectionSubject->subject->parent_subject_id;
            $parentSecSub = ClassSectionSubject::where('class_section_id', $currentSectionSubject->class_section_id)
                ->where('subject_id', $parentSubjectId)
                ->first();

            if ($parentSecSub) {
                $childSubjectIds = Subject::where('parent_subject_id', $parentSubjectId)->pluck('id');
                $childSecSubIds = ClassSectionSubject::where('class_section_id', $currentSectionSubject->class_section_id)
                    ->whereIn('subject_id', $childSubjectIds)
                    ->pluck('id');

                $enrolledStudents = Enrollment::where('class_section_id', $currentSectionSubject->class_section_id)->get();
                $periodsList = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter', 'Prelim', 'Midterm', 'Finals', 'Subject Grade'];

                foreach ($enrolledStudents as $enrollment) {
                    foreach ($periodsList as $pName) {
                        $childGrades = Grade::where('enrollment_id', $enrollment->id)
                            ->whereIn('class_section_subject_id', $childSecSubIds)
                            ->where('academic_period', $pName)
                            ->pluck('final_grade')
                            ->filter(fn($v) => !is_null($v) && $v > 0);

                        if ($childGrades->count() > 0) {
                            $avgGrade = round($childGrades->avg(), 2);
                            Grade::updateOrCreate(
                                [
                                    'enrollment_id' => $enrollment->id,
                                    'class_section_subject_id' => $parentSecSub->id,
                                    'academic_period' => $pName,
                                ],
                                [
                                    'final_grade' => $avgGrade,
                                    'remarks' => $avgGrade >= 75 ? 'Passed' : 'Failed',
                                ]
                            );
                        } else {
                            Grade::where('enrollment_id', $enrollment->id)
                                ->where('class_section_subject_id', $parentSecSub->id)
                                ->where('academic_period', $pName)
                                ->delete();
                        }
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Computed grades for ' . ($currentSectionSubject->subject->subject_name ?? 'Subject') . ' have been reset/deleted.',
        ]);
    }
}
