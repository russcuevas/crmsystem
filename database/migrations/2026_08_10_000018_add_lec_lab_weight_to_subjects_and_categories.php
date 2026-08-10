<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('has_lab')->default(false)->after('semester');
            $table->decimal('lecture_weight', 5, 2)->default(100.00)->after('has_lab');
            $table->decimal('lab_weight', 5, 2)->default(0.00)->after('lecture_weight');
        });

        Schema::table('grading_categories', function (Blueprint $table) {
            $table->enum('component_type', ['lecture', 'laboratory', 'general'])->default('general')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['has_lab', 'lecture_weight', 'lab_weight']);
        });

        Schema::table('grading_categories', function (Blueprint $table) {
            $table->dropColumn('component_type');
        });
    }
};
