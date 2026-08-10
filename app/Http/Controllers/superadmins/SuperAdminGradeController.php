<?php

namespace App\Http\Controllers\superadmins;

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
use Illuminate\Http\Request;

class SuperAdminGradeController extends Controller
{
    public function SuperAdminGradePage()
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

        // Strictly filter subjects by semester when viewing semestral level (SHS or COLLEGE)
        if (in_array(strtoupper($selectedLevel), ['SHS', 'COLLEGE'])) {
            $semKey = ($selectedSemester == '2nd Semester') ? '2nd' : '1st';
            $subjectQuery->whereHas('subject', function ($sq) use ($semKey, $selectedSemester) {
                $sq->where('semester', 'LIKE', '%' . $semKey . '%')
                  ->orWhere('semester', $selectedSemester)
                  ->orWhereNull('semester');
            });
        }

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

        if ($currentSectionSubject) {
            $levelCode = $currentSectionSubject->classSection->gradeLevel->educationLevel->code ?? '';

            if (in_array($levelCode, ['BED', 'JHS'])) {
                $defaultPeriods = collect(['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter']);
            } else {
                $defaultPeriods = collect(['Prelim', 'Midterm', 'Finals']);
            }

            $dbPeriods = GradingCategory::where('class_section_subject_id', $currentSectionSubject->id)
                ->distinct()
                ->pluck('academic_period');

            $availablePeriods = $defaultPeriods->merge($dbPeriods)->unique()->values();

            $categoriesQuery = GradingCategory::where('class_section_subject_id', $currentSectionSubject->id)
                ->with(['gradingTasks.studentTaskScores']);

            if ($selectedPeriod) {
                $categoriesQuery->where('academic_period', $selectedPeriod);
            }

            $categories = $categoriesQuery->get();

            // Filter enrolled students by section, school year, and semester
            $enrolledQuery = Enrollment::where('class_section_id', $currentSectionSubject->class_section_id)
                ->where('school_year_id', $currentSectionSubject->classSection->school_year_id ?? ($activeSchoolYear->id ?? null));

            if (in_array($levelCode, ['SHS', 'COLLEGE'])) {
                $enrolledQuery->where(function ($q) use ($selectedSemester) {
                    $q->where('semester', $selectedSemester)
                      ->orWhereNull('semester');
                });
            }

            $enrolledStudents = $enrolledQuery->with(['student.user', 'taskScores'])->get();

            // Fetch Distinct Attendance Dates and Records
            $attendanceDates = Attendance::where('class_section_subject_id', $currentSectionSubject->id)
                ->select('attendance_date')
                ->distinct()
                ->orderBy('attendance_date', 'asc')
                ->pluck('attendance_date');

            $attendances = Attendance::where('class_section_subject_id', $currentSectionSubject->id)->get();
        }

        $totalAccounts = User::count();
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

        return view('superadmins.grades.index', compact(
            'classSectionSubjects',
            'currentSectionSubject',
            'categories',
            'enrolledStudents',
            'availablePeriods',
            'selectedPeriod',
            'selectedSemester',
            'attendanceDates',
            'attendances',
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
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        GradingCategory::create([
            'class_section_subject_id' => $request->class_section_subject_id,
            'academic_period' => $request->academic_period,
            'name' => $request->name,
            'weight' => $request->weight,
        ]);

        return back()->with('success', 'Grading Category added successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'academic_period' => 'required|string',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $category = GradingCategory::findOrFail($id);
        $category->update([
            'academic_period' => $request->academic_period,
            'name' => $request->name,
            'weight' => $request->weight,
        ]);

        return back()->with('success', 'Grading Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = GradingCategory::findOrFail($id);
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
        $task->delete();

        return back()->with('success', 'Grading Task deleted successfully.');
    }

    public function storeAttendanceDate(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
        ]);

        $css = ClassSectionSubject::findOrFail($request->class_section_subject_id);

        $enrolledStudents = Enrollment::where('class_section_id', $css->class_section_id)->get();

        foreach ($enrolledStudents as $enr) {
            Attendance::firstOrCreate(
                [
                    'class_section_subject_id' => $css->id,
                    'enrollment_id' => $enr->id,
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
            'status' => 'required|string',
        ]);

        $status = strtoupper(trim($request->status));
        $validStatuses = ['P', 'L', 'A', 'AEL', 'E', 'C'];
        if (!in_array($status, $validStatuses)) {
            $status = 'P';
        }

        $att = Attendance::updateOrCreate(
            [
                'class_section_subject_id' => $request->class_section_subject_id,
                'enrollment_id' => $request->enrollment_id,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => $status,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance status updated successfully.',
            'status' => $att->status,
        ]);
    }

    public function destroyAttendanceDate(Request $request)
    {
        $request->validate([
            'class_section_subject_id' => 'required|exists:class_section_subjects,id',
            'attendance_date' => 'required|date',
        ]);

        Attendance::where('class_section_subject_id', $request->class_section_subject_id)
            ->where('attendance_date', $request->attendance_date)
            ->delete();

        return back()->with('success', 'Attendance column deleted successfully.');
    }
}
