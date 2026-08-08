<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_category_id')->constrained('grading_categories')->cascadeOnDelete();
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->decimal('max_score', 8, 2);
            $table->date('task_date')->nullable();
            $table->string('status')->default('graded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_tasks');
    }
};
