<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('parent_subject_id')->nullable()->after('course_id')->constrained('subjects')->nullOnDelete();
            $table->boolean('is_parent')->default(false)->after('parent_subject_id');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['parent_subject_id']);
            $table->dropColumn(['parent_subject_id', 'is_parent']);
        });
    }
};
