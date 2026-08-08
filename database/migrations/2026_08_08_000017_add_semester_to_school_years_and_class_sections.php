<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('class_sections', 'semester')) {
            Schema::table('class_sections', function (Blueprint $table) {
                $table->string('semester')->default('1st Semester')->after('section_name');
            });
        }

        if (!Schema::hasColumn('enrollments', 'semester')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('semester')->default('1st Semester')->after('grade_level_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('class_sections', 'semester')) {
            Schema::table('class_sections', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }

        if (Schema::hasColumn('enrollments', 'semester')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }
    }
};
