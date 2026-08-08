<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_subject_id')->constrained('class_section_subjects')->cascadeOnDelete();
            $table->string('academic_period');
            $table->string('name');
            $table->decimal('weight', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_categories');
    }
};
