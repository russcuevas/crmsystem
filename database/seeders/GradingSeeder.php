<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassSectionSubject;
use App\Models\GradingCategory;
use App\Models\GradingTask;
use App\Models\StudentTaskScore;
use App\Models\Grade;
use App\Models\Enrollment;
use Carbon\Carbon;

class GradingSeeder extends Seeder
{
    /**
     * Run the grading seeder to populate sample categories, tasks, task scores, and final grades.
     */
    public function run(): void
    {
        // Fetch existing class section subjects and enrollments
        $cssCollege = ClassSectionSubject::whereHas('classSection.gradeLevel.educationLevel', function ($q) {
            $q->whereIn('code', ['COLLEGE', 'SHS']);
        })->first();

        $cssJhs = ClassSectionSubject::whereHas('classSection.gradeLevel.educationLevel', function ($q) {
            $q->whereIn('code', ['BED', 'JHS']);
        })->first();

        // -------------------------------------------------------------
        // 1. SEMESTRAL GRADING EXAMPLE (COLLEGE / SHS): Prelim, Midterm, Finals
        // -------------------------------------------------------------
        if ($cssCollege) {
            $enrollmentsCollege = Enrollment::where('class_section_id', $cssCollege->class_section_id)->get();

            $periods = ['Prelim', 'Midterm', 'Finals'];

            foreach ($periods as $period) {
                // Categories
                $catWritten = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssCollege->id,
                    'academic_period' => $period,
                    'name' => 'Written Works',
                ], ['weight' => 25.00]);

                $catPerf = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssCollege->id,
                    'academic_period' => $period,
                    'name' => 'Performance Tasks',
                ], ['weight' => 45.00]);

                $catExam = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssCollege->id,
                    'academic_period' => $period,
                    'name' => $period . ' Major Exam',
                ], ['weight' => 30.00]);

                // Tasks
                $taskWritten = GradingTask::firstOrCreate([
                    'grading_category_id' => $catWritten->id,
                    'task_name' => $period . ' Quiz 1',
                ], [
                    'description' => $period . ' Written Quiz',
                    'max_score' => 50,
                    'task_date' => Carbon::now()->subWeeks(3),
                    'status' => 'graded',
                ]);

                $taskPerf = GradingTask::firstOrCreate([
                    'grading_category_id' => $catPerf->id,
                    'task_name' => $period . ' Project/Lab',
                ], [
                    'description' => $period . ' Performance Task',
                    'max_score' => 100,
                    'task_date' => Carbon::now()->subWeeks(2),
                    'status' => 'graded',
                ]);

                $taskExam = GradingTask::firstOrCreate([
                    'grading_category_id' => $catExam->id,
                    'task_name' => $period . ' Major Examination',
                ], [
                    'description' => $period . ' Major Exam Sheet',
                    'max_score' => 100,
                    'task_date' => Carbon::now()->subWeek(),
                    'status' => 'graded',
                ]);

                // Scores & Period Grades for Enrolled Students
                foreach ($enrollmentsCollege as $index => $enrollment) {
                    $scoreOffset = ($index % 2 == 0) ? 0 : -5;

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskWritten->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 48 + $scoreOffset, 'remarks' => 'Good performance']);

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskPerf->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 95 + $scoreOffset, 'remarks' => 'Well executed']);

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskExam->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 92 + $scoreOffset, 'remarks' => 'Passed']);

                    Grade::firstOrCreate([
                        'enrollment_id' => $enrollment->id,
                        'class_section_subject_id' => $cssCollege->id,
                        'academic_period' => $period,
                    ], [
                        'final_grade' => 93.50 + $scoreOffset,
                        'remarks' => 'Passed',
                    ]);
                }
            }
        }

        // -------------------------------------------------------------
        // 2. QUARTERLY GRADING EXAMPLE (BED / JHS): 1st - 4th Quarters
        // -------------------------------------------------------------
        if ($cssJhs) {
            $enrollmentsJhs = Enrollment::where('class_section_id', $cssJhs->class_section_id)->get();

            $quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

            foreach ($quarters as $quarter) {
                // Categories
                $catWritten = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssJhs->id,
                    'academic_period' => $quarter,
                    'name' => 'Written Works',
                ], ['weight' => 30.00]);

                $catPerf = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssJhs->id,
                    'academic_period' => $quarter,
                    'name' => 'Performance Tasks',
                ], ['weight' => 50.00]);

                $catExam = GradingCategory::firstOrCreate([
                    'class_section_subject_id' => $cssJhs->id,
                    'academic_period' => $quarter,
                    'name' => 'Quarterly Assessment',
                ], ['weight' => 20.00]);

                // Tasks
                $taskWritten = GradingTask::firstOrCreate([
                    'grading_category_id' => $catWritten->id,
                    'task_name' => $quarter . ' Lagumang Pagsusulit',
                ], [
                    'description' => $quarter . ' Written Quiz',
                    'max_score' => 30,
                    'task_date' => Carbon::now()->subWeeks(3),
                    'status' => 'graded',
                ]);

                $taskPerf = GradingTask::firstOrCreate([
                    'grading_category_id' => $catPerf->id,
                    'task_name' => $quarter . ' Performance Task',
                ], [
                    'description' => $quarter . ' Practical Task',
                    'max_score' => 50,
                    'task_date' => Carbon::now()->subWeeks(2),
                    'status' => 'graded',
                ]);

                $taskExam = GradingTask::firstOrCreate([
                    'grading_category_id' => $catExam->id,
                    'task_name' => $quarter . ' Examination',
                ], [
                    'description' => $quarter . ' Assessment Paper',
                    'max_score' => 50,
                    'task_date' => Carbon::now()->subWeek(),
                    'status' => 'graded',
                ]);

                // Scores & Quarter Grades
                foreach ($enrollmentsJhs as $index => $enrollment) {
                    $scoreOffset = ($index % 2 == 0) ? 0 : -3;

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskWritten->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 28 + $scoreOffset, 'remarks' => 'Mahusay']);

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskPerf->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 47 + $scoreOffset, 'remarks' => 'Magaling']);

                    StudentTaskScore::firstOrCreate([
                        'grading_task_id' => $taskExam->id,
                        'enrollment_id' => $enrollment->id,
                    ], ['score' => 46 + $scoreOffset, 'remarks' => 'Napakagaling']);

                    Grade::firstOrCreate([
                        'enrollment_id' => $enrollment->id,
                        'class_section_subject_id' => $cssJhs->id,
                        'academic_period' => $quarter,
                    ], [
                        'final_grade' => 92.00 + $scoreOffset,
                        'remarks' => 'Passed - Outstanding',
                    ]);
                }
            }
        }
    }
}
