<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_category_id',
        'task_name',
        'description',
        'max_score',
        'task_date',
        'status',
    ];

    protected $casts = [
        'task_date' => 'date',
        'max_score' => 'decimal:2',
    ];

    public function gradingCategory()
    {
        return $this->belongsTo(GradingCategory::class);
    }

    public function studentTaskScores()
    {
        return $this->hasMany(StudentTaskScore::class);
    }
}
