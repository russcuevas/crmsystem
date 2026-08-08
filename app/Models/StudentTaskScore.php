<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTaskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_task_id',
        'enrollment_id',
        'score',
        'remarks',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function gradingTask()
    {
        return $this->belongsTo(GradingTask::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
