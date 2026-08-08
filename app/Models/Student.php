<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lrn',
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birthday',
        'phone_number',
        'gender',
        'province',
        'city',
        'barangay',
        'status',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
