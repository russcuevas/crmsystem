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
            'password' => Hash::make('123456789'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'status' => 'active',
        ]);



        // 2. School Years
        $sy2324 = SchoolYear::create(['school_year' => '2023-2024', 'is_active' => false]);
        $sy2425 = SchoolYear::create(['school_year' => '2024-2025', 'is_active' => false]);
        $sy2526 = SchoolYear::create(['school_year' => '2025-2026', 'is_active' => false]);
        $sy2526 = SchoolYear::create(['school_year' => '2026-2027', 'is_active' => true]);


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
    }
}
