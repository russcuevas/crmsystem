<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'class_section_subject_id',
        'academic_period',
        'final_grade',
        'remarks',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function classSectionSubject()
    {
        return $this->belongsTo(ClassSectionSubject::class);
    }
}
