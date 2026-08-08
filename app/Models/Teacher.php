<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'education_level_id',
        'position',
        'birthday',
        'phone_number',
        'province',
        'city',
        'barangay',
        'gender',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function advisedClassSections()
    {
        return $this->hasMany(ClassSection::class, 'class_adviser_id');
    }

    public function classSectionSubjects()
    {
        return $this->hasMany(ClassSectionSubject::class);
    }
}
