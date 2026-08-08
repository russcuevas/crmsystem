<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classSections()
    {
        return $this->hasMany(ClassSection::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
