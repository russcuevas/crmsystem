<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSectionSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_id',
        'subject_id',
        'teacher_id',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function gradingCategories()
    {
        return $this->hasMany(GradingCategory::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
