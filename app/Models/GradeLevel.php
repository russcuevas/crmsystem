<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'education_level_id',
        'name',
        'code',
    ];

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function classSections()
    {
        return $this->hasMany(ClassSection::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
