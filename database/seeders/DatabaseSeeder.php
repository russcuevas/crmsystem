<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolYear;
use App\Models\EducationLevel;
use App\Models\GradeLevel;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\ClassSectionSubject;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\GradingCategory;
use App\Models\GradingTask;
use App\Models\StudentTaskScore;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@crm.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $teacherUser1 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@crm.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $teacherUser2 = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan.delacruz@crm.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $studentUser1 = User::create([
            'name' => 'Jose Rizal',
            'email' => 'jose.rizal@student.crm.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $studentUser2 = User::create([
            'name' => 'Andres Bonifacio',
            'email' => 'andres.bonifacio@student.crm.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);

        // 2. School Years
        $sy2324 = SchoolYear::create(['school_year' => '2023-2024', 'is_active' => false]);
        $sy2425 = SchoolYear::create(['school_year' => '2024-2025', 'is_active' => true]);
        $sy2526 = SchoolYear::create(['school_year' => '2025-2026', 'is_active' => false]);

        // 3. Education Levels
        $bed = EducationLevel::create([
            'name' => 'Basic Education Level',
            'code' => 'BED',
            'description' => 'Elementary Level Education (Kinder to Grade 6)'
        ]);

        $jhs = EducationLevel::create([
            'name' => 'Junior High School Level',
            'code' => 'JHS',
            'description' => 'Junior High School (Grade 7 to Grade 10)'
        ]);

        $shs = EducationLevel::create([
            'name' => 'Senior High School Level',
            'code' => 'SHS',
            'description' => 'Senior High School (Grade 11 to Grade 12)'
        ]);

        $college = EducationLevel::create([
            'name' => 'College Level',
            'code' => 'COLLEGE',
            'description' => 'Tertiary Higher Education Degree Programs'
        ]);

        // 4. Grade Levels
        // BED
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Kinder', 'code' => 'K']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 1', 'code' => 'I']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 2', 'code' => 'II']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 3', 'code' => 'III']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 4', 'code' => 'IV']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 5', 'code' => 'V']);
        GradeLevel::create(['education_level_id' => $bed->id, 'name' => 'Grade 6', 'code' => 'VI']);

        // JHS
        GradeLevel::create(['education_level_id' => $jhs->id, 'name' => 'Grade 7', 'code' => 'VII']);
        GradeLevel::create(['education_level_id' => $jhs->id, 'name' => 'Grade 8', 'code' => 'VIII']);
        GradeLevel::create(['education_level_id' => $jhs->id, 'name' => 'Grade 9', 'code' => 'IX']);
        $g10 = GradeLevel::create(['education_level_id' => $jhs->id, 'name' => 'Grade 10', 'code' => 'X']);

        // SHS
        $g11 = GradeLevel::create(['education_level_id' => $shs->id, 'name' => 'Grade 11', 'code' => 'XI']);
        GradeLevel::create(['education_level_id' => $shs->id, 'name' => 'Grade 12', 'code' => 'XII']);

        // College
        $firstYear = GradeLevel::create(['education_level_id' => $college->id, 'name' => 'First Year', 'code' => 'I']);
        $secondYear = GradeLevel::create(['education_level_id' => $college->id, 'name' => 'Second Year', 'code' => 'II']);
        GradeLevel::create(['education_level_id' => $college->id, 'name' => 'Third Year', 'code' => 'III']);
        GradeLevel::create(['education_level_id' => $college->id, 'name' => 'Fourth Year', 'code' => 'IV']);

        // 5. Courses / Tracks & Strands
        // SHS Strands
        $abm = Course::create([
            'course_code' => 'ABM',
            'course_name' => 'Accountancy, Business and Management Strand',
            'level' => 'SHS',
            'description' => 'Senior High School strand focusing on business, accounting, and management.'
        ]);

        $stem = Course::create([
            'course_code' => 'STEM',
            'course_name' => 'Science, Technology, Engineering, and Mathematics Strand',
            'level' => 'SHS',
            'description' => 'Senior High School strand focusing on advanced science and mathematics.'
        ]);

        $humss = Course::create([
            'course_code' => 'HUMSS',
            'course_name' => 'Humanities and Social Sciences Strand',
            'level' => 'SHS',
            'description' => 'Senior High School strand focusing on social sciences and communication.'
        ]);

        Course::create(['course_code' => 'TVL', 'course_name' => 'Technical-Vocational-Livelihood Track', 'level' => 'SHS', 'description' => 'Technical-Vocational skills track for SHS.']);
        Course::create(['course_code' => 'GAS', 'course_name' => 'General Academic Strand', 'level' => 'SHS', 'description' => 'General academic strand for SHS.']);

        // College Degree Programs
        $bsit = Course::create([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'level' => 'COLLEGE',
            'description' => 'Information Technology program focusing on software development and networks.'
        ]);

        $bscs = Course::create([
            'course_code' => 'BSCS',
            'course_name' => 'Bachelor of Science in Computer Science',
            'level' => 'COLLEGE',
            'description' => 'Computer Science program focusing on algorithms and computational theory.'
        ]);

        $bshm = Course::create([
            'course_code' => 'BSHM',
            'course_name' => 'Bachelor of Science in Hospitality Management',
            'level' => 'COLLEGE',
            'description' => 'Hospitality and hotel management program.'
        ]);

        Course::create(['course_code' => 'BSTM', 'course_name' => 'Bachelor of Science in Tourism Management', 'level' => 'COLLEGE', 'description' => 'Tourism and hospitality program.']);
        Course::create(['course_code' => 'BSED', 'course_name' => 'Bachelor of Secondary Education', 'level' => 'COLLEGE', 'description' => 'Secondary level teacher education.']);
        Course::create(['course_code' => 'BEED', 'course_name' => 'Bachelor of Elementary Education', 'level' => 'COLLEGE', 'description' => 'Elementary level teacher education.']);
        Course::create(['course_code' => 'BSBA', 'course_name' => 'Bachelor of Science in Business Administration', 'level' => 'COLLEGE', 'description' => 'Business management and administration.']);
        Course::create(['course_code' => 'BSENTREP', 'course_name' => 'Bachelor of Science in Entrepreneurship', 'level' => 'COLLEGE', 'description' => 'Entrepreneurship and innovation.']);
        Course::create(['course_code' => 'BSECE', 'course_name' => 'Bachelor of Science in Electronics Engineering', 'level' => 'COLLEGE', 'description' => 'Electronics and communications engineering.']);

        // 6. Subjects
        $subjComp101 = Subject::create([
            'subject_code' => 'COMP101',
            'subject_name' => 'Introduction to Computer Science',
            'education_level_id' => $college->id,
            'units' => 3,
            'semester' => '1st Semester',
            'course_id' => $bsit->id,
        ]);

        $subjFil101 = Subject::create([
            'subject_code' => 'FIL101',
            'subject_name' => 'Introduction to Filipino',
            'education_level_id' => $jhs->id,
            'units' => 3,
            'semester' => 'All Quarters',
            'course_id' => null,
        ]);

        $subjMath101 = Subject::create([
            'subject_code' => 'Math101',
            'subject_name' => 'Introduction to Mathematics',
            'education_level_id' => $college->id,
            'units' => 3,
            'semester' => '1st Semester',
            'course_id' => $bsit->id,
        ]);

        $subjComp102 = Subject::create([
            'subject_code' => 'COMP102',
            'subject_name' => 'Data Structures & Algorithms',
            'education_level_id' => $college->id,
            'units' => 3,
            'semester' => '2nd Semester',
            'course_id' => $bsit->id,
        ]);

        $subjGenEd = Subject::create([
            'subject_code' => 'GenEd101',
            'subject_name' => 'Introduction to General Education',
            'education_level_id' => $college->id,
            'units' => 3,
            'semester' => '1st Semester',
            'course_id' => null,
        ]);

        // 7. Teachers
        $teacher1 = Teacher::create([
            'user_id' => $teacherUser1->id,
            'teacher_id' => 'TCH-2024-001',
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'Santos',
            'extension_name' => null,
            'education_level_id' => $college->id,
            'position' => 'Assistant Professor',
            'birthday' => '1988-05-15',
            'phone_number' => '09171234567',
            'province' => 'Metro Manila',
            'city' => 'Quezon City',
            'barangay' => 'Diliman',
            'gender' => 'Female',
        ]);

        $teacher2 = Teacher::create([
            'user_id' => $teacherUser2->id,
            'teacher_id' => 'TCH-2024-002',
            'first_name' => 'Juan',
            'middle_name' => 'Ponce',
            'last_name' => 'Dela Cruz',
            'extension_name' => 'Jr.',
            'education_level_id' => $jhs->id,
            'position' => 'Instructor I',
            'birthday' => '1990-10-20',
            'phone_number' => '09189876543',
            'province' => 'Laguna',
            'city' => 'Calamba',
            'barangay' => 'Real',
            'gender' => 'Male',
        ]);

        // 8. Students
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'education_level_id' => $college->id,
            'grade_level_id' => $secondYear->id,
            'course_id' => $bsit->id,
            'lrn' => '123456789012',
            'student_number' => 'STU-2024-0001',
            'first_name' => 'Jose',
            'middle_name' => 'Protacio',
            'last_name' => 'Rizal',
            'extension_name' => null,
            'birthday' => '2004-06-19',
            'phone_number' => '09191112222',
            'gender' => 'Male',
            'province' => 'Laguna',
            'city' => 'Calamba',
            'barangay' => 'Poblacion',
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'education_level_id' => $jhs->id,
            'grade_level_id' => $g10->id,
            'course_id' => null,
            'lrn' => '987654321098',
            'student_number' => 'STU-2024-0002',
            'first_name' => 'Andres',
            'middle_name' => 'Castro',
            'last_name' => 'Bonifacio',
            'extension_name' => null,
            'birthday' => '2005-11-30',
            'phone_number' => '09193334444',
            'gender' => 'Male',
            'province' => 'Metro Manila',
            'city' => 'Tondo',
            'barangay' => 'Barangay 1',
            'status' => 'active',
        ]);

        // 9. Class Sections
        $sectionBsit2a = ClassSection::create([
            'school_year_id' => $sy2425->id,
            'grade_level_id' => $secondYear->id,
            'course_id' => $bsit->id,
            'section_name' => 'BSIT-2A',
            'class_adviser_id' => $teacher1->id,
        ]);

        $sectionJhs10a = ClassSection::create([
            'school_year_id' => $sy2425->id,
            'grade_level_id' => $g10->id,
            'course_id' => null,
            'section_name' => '10-ABM-A',
            'class_adviser_id' => $teacher2->id,
        ]);

        // 10. Class Section Subjects
        $css1 = ClassSectionSubject::create([
            'class_section_id' => $sectionBsit2a->id,
            'subject_id' => $subjComp101->id,
            'teacher_id' => $teacher1->id,
        ]);

        $css2 = ClassSectionSubject::create([
            'class_section_id' => $sectionBsit2a->id,
            'subject_id' => $subjMath101->id,
            'teacher_id' => $teacher1->id,
        ]);

        $css3 = ClassSectionSubject::create([
            'class_section_id' => $sectionJhs10a->id,
            'subject_id' => $subjFil101->id,
            'teacher_id' => $teacher2->id,
        ]);

        $css4 = ClassSectionSubject::create([
            'class_section_id' => $sectionBsit2a->id,
            'subject_id' => $subjComp102->id,
            'teacher_id' => $teacher1->id,
        ]);

        // 11. Enrollments
        $enrollment1 = Enrollment::create([
            'student_id' => $student1->id,
            'class_section_id' => $sectionBsit2a->id,
            'school_year_id' => $sy2425->id,
            'grade_level_id' => $secondYear->id,
            'semester' => '1st Semester',
            'status' => 'active',
            'enrolled_at' => Carbon::now()->subMonths(2),
        ]);

        $enrollment2 = Enrollment::create([
            'student_id' => $student2->id,
            'class_section_id' => $sectionBsit2a->id,
            'school_year_id' => $sy2425->id,
            'grade_level_id' => $secondYear->id,
            'semester' => '2nd Semester',
            'status' => 'active',
            'enrolled_at' => Carbon::now()->subMonths(2),
        ]);

        // 12. Attendance
        Attendance::create([
            'enrollment_id' => $enrollment1->id,
            'class_section_subject_id' => $css1->id,
            'attendance_date' => Carbon::now()->subDays(2),
            'status' => 'present',
            'remarks' => 'On time',
        ]);

        Attendance::create([
            'enrollment_id' => $enrollment2->id,
            'class_section_subject_id' => $css1->id,
            'attendance_date' => Carbon::now()->subDays(2),
            'status' => 'late',
            'remarks' => 'Arrived 10 minutes late',
        ]);

        // 13. Grading Categories (Prelim, Midterm, Finals)
        // Prelim Period
        $catPrelimWritten = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Prelim',
            'name' => 'Written Works',
            'weight' => 25.00,
        ]);
        $catPrelimPerformance = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Prelim',
            'name' => 'Performance Tasks',
            'weight' => 45.00,
        ]);
        $catPrelimExam = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Prelim',
            'name' => 'Prelim Major Exam',
            'weight' => 30.00,
        ]);

        // Midterm Period
        $catMidtermWritten = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Midterm',
            'name' => 'Written Works',
            'weight' => 25.00,
        ]);
        $catMidtermPerformance = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Midterm',
            'name' => 'Performance Tasks',
            'weight' => 45.00,
        ]);
        $catMidtermExam = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Midterm',
            'name' => 'Midterm Major Exam',
            'weight' => 30.00,
        ]);

        // Finals Period
        $catFinalsWritten = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Finals',
            'name' => 'Written Works',
            'weight' => 25.00,
        ]);
        $catFinalsPerformance = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Finals',
            'name' => 'Performance Tasks',
            'weight' => 45.00,
        ]);
        $catFinalsExam = GradingCategory::create([
            'class_section_subject_id' => $css1->id,
            'academic_period' => 'Finals',
            'name' => 'Final Major Exam',
            'weight' => 30.00,
        ]);

        // 14. Grading Tasks (Prelim, Midterm, Finals)
        // Prelim Tasks
        $taskP1 = GradingTask::create([
            'grading_category_id' => $catPrelimWritten->id,
            'task_name' => 'Prelim Quiz 1 - Introduction',
            'description' => 'Syntax and Logic Quiz',
            'max_score' => 50,
            'task_date' => Carbon::now()->subWeeks(6),
            'status' => 'graded',
        ]);
        $taskP2 = GradingTask::create([
            'grading_category_id' => $catPrelimPerformance->id,
            'task_name' => 'Prelim Lab 1 - CLI App',
            'description' => 'Console Application Lab',
            'max_score' => 100,
            'task_date' => Carbon::now()->subWeeks(5),
            'status' => 'graded',
        ]);
        $taskP3 = GradingTask::create([
            'grading_category_id' => $catPrelimExam->id,
            'task_name' => 'Prelim Examination',
            'description' => 'Written and Practical Exam',
            'max_score' => 100,
            'task_date' => Carbon::now()->subWeeks(4),
            'status' => 'graded',
        ]);

        // Midterm Tasks
        $taskM1 = GradingTask::create([
            'grading_category_id' => $catMidtermWritten->id,
            'task_name' => 'Midterm Quiz 1 - OOP Principles',
            'description' => 'Classes and Inheritance Quiz',
            'max_score' => 50,
            'task_date' => Carbon::now()->subWeeks(3),
            'status' => 'graded',
        ]);
        $taskM2 = GradingTask::create([
            'grading_category_id' => $catMidtermPerformance->id,
            'task_name' => 'Midterm Project - CRUD System',
            'description' => 'Database CRUD Implementation',
            'max_score' => 100,
            'task_date' => Carbon::now()->subWeeks(2),
            'status' => 'graded',
        ]);
        $taskM3 = GradingTask::create([
            'grading_category_id' => $catMidtermExam->id,
            'task_name' => 'Midterm Major Examination',
            'description' => 'Midterm Examination Paper',
            'max_score' => 100,
            'task_date' => Carbon::now()->subWeek(),
            'status' => 'graded',
        ]);

        // Finals Tasks
        $taskF1 = GradingTask::create([
            'grading_category_id' => $catFinalsWritten->id,
            'task_name' => 'Finals Quiz 1 - System Design',
            'description' => 'Architecture and Security Quiz',
            'max_score' => 50,
            'task_date' => Carbon::now()->subDays(5),
            'status' => 'graded',
        ]);
        $taskF2 = GradingTask::create([
            'grading_category_id' => $catFinalsPerformance->id,
            'task_name' => 'Final Capstone Project',
            'description' => 'Complete System Integration Project',
            'max_score' => 100,
            'task_date' => Carbon::now()->subDays(2),
            'status' => 'graded',
        ]);
        $taskF3 = GradingTask::create([
            'grading_category_id' => $catFinalsExam->id,
            'task_name' => 'Final Major Examination',
            'description' => 'Comprehensive Final Examination',
            'max_score' => 100,
            'task_date' => Carbon::now(),
            'status' => 'graded',
        ]);

        // 15. Student Task Scores
        // Prelim Scores
        StudentTaskScore::create(['grading_task_id' => $taskP1->id, 'enrollment_id' => $enrollment1->id, 'score' => 48, 'remarks' => 'Excellent']);
        StudentTaskScore::create(['grading_task_id' => $taskP1->id, 'enrollment_id' => $enrollment2->id, 'score' => 42, 'remarks' => 'Good job']);
        StudentTaskScore::create(['grading_task_id' => $taskP2->id, 'enrollment_id' => $enrollment1->id, 'score' => 95, 'remarks' => 'Great code']);
        StudentTaskScore::create(['grading_task_id' => $taskP2->id, 'enrollment_id' => $enrollment2->id, 'score' => 88, 'remarks' => 'Well done']);
        StudentTaskScore::create(['grading_task_id' => $taskP3->id, 'enrollment_id' => $enrollment1->id, 'score' => 92, 'remarks' => 'Outstanding']);
        StudentTaskScore::create(['grading_task_id' => $taskP3->id, 'enrollment_id' => $enrollment2->id, 'score' => 85, 'remarks' => 'Passed']);

        // Midterm Scores
        StudentTaskScore::create(['grading_task_id' => $taskM1->id, 'enrollment_id' => $enrollment1->id, 'score' => 49, 'remarks' => 'Top score']);
        StudentTaskScore::create(['grading_task_id' => $taskM1->id, 'enrollment_id' => $enrollment2->id, 'score' => 44, 'remarks' => 'Good']);
        StudentTaskScore::create(['grading_task_id' => $taskM2->id, 'enrollment_id' => $enrollment1->id, 'score' => 98, 'remarks' => 'Clean code']);
        StudentTaskScore::create(['grading_task_id' => $taskM2->id, 'enrollment_id' => $enrollment2->id, 'score' => 90, 'remarks' => 'Well organized']);
        StudentTaskScore::create(['grading_task_id' => $taskM3->id, 'enrollment_id' => $enrollment1->id, 'score' => 94, 'remarks' => 'Excellent']);
        StudentTaskScore::create(['grading_task_id' => $taskM3->id, 'enrollment_id' => $enrollment2->id, 'score' => 87, 'remarks' => 'Passed']);

        // Finals Scores
        StudentTaskScore::create(['grading_task_id' => $taskF1->id, 'enrollment_id' => $enrollment1->id, 'score' => 50, 'remarks' => 'Perfect score']);
        StudentTaskScore::create(['grading_task_id' => $taskF1->id, 'enrollment_id' => $enrollment2->id, 'score' => 45, 'remarks' => 'Great']);
        StudentTaskScore::create(['grading_task_id' => $taskF2->id, 'enrollment_id' => $enrollment1->id, 'score' => 99, 'remarks' => 'Exceptional']);
        StudentTaskScore::create(['grading_task_id' => $taskF2->id, 'enrollment_id' => $enrollment2->id, 'score' => 92, 'remarks' => 'Great presentation']);
        StudentTaskScore::create(['grading_task_id' => $taskF3->id, 'enrollment_id' => $enrollment1->id, 'score' => 96, 'remarks' => 'Superior']);
        StudentTaskScore::create(['grading_task_id' => $taskF3->id, 'enrollment_id' => $enrollment2->id, 'score' => 89, 'remarks' => 'Very Good']);

        // 16. Period Final Grades
        Grade::create(['enrollment_id' => $enrollment1->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Prelim', 'final_grade' => 94.50, 'remarks' => 'Passed - Excellent']);
        Grade::create(['enrollment_id' => $enrollment2->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Prelim', 'final_grade' => 86.50, 'remarks' => 'Passed - Good']);

        Grade::create(['enrollment_id' => $enrollment1->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Midterm', 'final_grade' => 96.00, 'remarks' => 'Passed - Superior']);
        Grade::create(['enrollment_id' => $enrollment2->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Midterm', 'final_grade' => 88.50, 'remarks' => 'Passed - Very Good']);

        Grade::create(['enrollment_id' => $enrollment1->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Finals', 'final_grade' => 97.20, 'remarks' => 'Passed - Outstanding']);
        Grade::create(['enrollment_id' => $enrollment2->id, 'class_section_subject_id' => $css1->id, 'academic_period' => 'Finals', 'final_grade' => 90.00, 'remarks' => 'Passed - Very Good']);

        // 17. JHS / BED Enrollment & Quarterly Grading (1st Quarter - 4th Quarter)
        $enrollmentJhs1 = Enrollment::create([
            'student_id' => $student2->id,
            'class_section_id' => $sectionJhs10a->id,
            'school_year_id' => $sy2425->id,
            'grade_level_id' => $g10->id,
            'status' => 'active',
            'enrolled_at' => Carbon::now()->subMonths(2),
        ]);

        // JHS Categories (1st Quarter)
        $catJhs1stWritten = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '1st Quarter', 'name' => 'Written Works', 'weight' => 30.00]);
        $catJhs1stPerf = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '1st Quarter', 'name' => 'Performance Tasks', 'weight' => 50.00]);
        $catJhs1stExam = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '1st Quarter', 'name' => 'Quarterly Assessment', 'weight' => 20.00]);

        // JHS Categories (2nd Quarter)
        $catJhs2ndWritten = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '2nd Quarter', 'name' => 'Written Works', 'weight' => 30.00]);
        $catJhs2ndPerf = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '2nd Quarter', 'name' => 'Performance Tasks', 'weight' => 50.00]);
        $catJhs2ndExam = GradingCategory::create(['class_section_subject_id' => $css3->id, 'academic_period' => '2nd Quarter', 'name' => 'Quarterly Assessment', 'weight' => 20.00]);

        // JHS Tasks
        $taskJhs1 = GradingTask::create(['grading_category_id' => $catJhs1stWritten->id, 'task_name' => 'Lagumang Pagsusulit 1', 'description' => 'Talasalitaan at Gramatika', 'max_score' => 30, 'task_date' => Carbon::now()->subWeeks(5), 'status' => 'graded']);
        $taskJhs2 = GradingTask::create(['grading_category_id' => $catJhs1stPerf->id, 'task_name' => 'Pagsulat ng Tula at Pagbigkas', 'description' => 'Performance Task 1', 'max_score' => 50, 'task_date' => Carbon::now()->subWeeks(3), 'status' => 'graded']);
        $taskJhs3 = GradingTask::create(['grading_category_id' => $catJhs1stExam->id, 'task_name' => 'Unang Markahang Pagsusulit', 'description' => '1st Quarter Exam', 'max_score' => 50, 'task_date' => Carbon::now()->subWeeks(1), 'status' => 'graded']);

        // JHS Student Scores & Grades
        StudentTaskScore::create(['grading_task_id' => $taskJhs1->id, 'enrollment_id' => $enrollmentJhs1->id, 'score' => 27, 'remarks' => 'Mahusay']);
        StudentTaskScore::create(['grading_task_id' => $taskJhs2->id, 'enrollment_id' => $enrollmentJhs1->id, 'score' => 46, 'remarks' => 'Magaling ang pagbigkas']);
        StudentTaskScore::create(['grading_task_id' => $taskJhs3->id, 'enrollment_id' => $enrollmentJhs1->id, 'score' => 45, 'remarks' => 'Napakagaling']);

        Grade::create(['enrollment_id' => $enrollmentJhs1->id, 'class_section_subject_id' => $css3->id, 'academic_period' => '1st Quarter', 'final_grade' => 91.00, 'remarks' => 'Passed - Outstanding']);

        // 18. Call Dedicated Grading Seeder
        $this->call(GradingSeeder::class);
    }
}
