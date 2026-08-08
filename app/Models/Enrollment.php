<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_section_id',
        'school_year_id',
        'grade_level_id',
        'status',
        'enrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function taskScores()
    {
        return $this->hasMany(StudentTaskScore::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
