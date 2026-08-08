<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_subject_id',
        'academic_period',
        'name',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public function classSectionSubject()
    {
        return $this->belongsTo(ClassSectionSubject::class);
    }

    public function gradingTasks()
    {
        return $this->hasMany(GradingTask::class);
    }
}
