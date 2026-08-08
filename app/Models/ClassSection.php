<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'grade_level_id',
        'course_id',
        'section_name',
        'semester',
        'class_adviser_id',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function adviser()
    {
        return $this->belongsTo(Teacher::class, 'class_adviser_id');
    }

    public function classSectionSubjects()
    {
        return $this->hasMany(ClassSectionSubject::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
