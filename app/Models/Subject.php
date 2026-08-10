<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'education_level_id',
        'units',
        'semester',
        'has_lab',
        'lecture_weight',
        'lab_weight',
        'course_id',
    ];

    protected $casts = [
        'has_lab' => 'boolean',
        'lecture_weight' => 'decimal:2',
        'lab_weight' => 'decimal:2',
    ];

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classSectionSubjects()
    {
        return $this->hasMany(ClassSectionSubject::class);
    }
}
