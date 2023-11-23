<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'ar_no';
    protected $fillable = [
        'id',
        'ar_no',
        'desc',
        'student_id',
        'amount',
        'date',
        'admin_id',
        'semester_id',
        'acad_year',
    ];

    public $incrementing = false;

    protected $casts = [
        'student_id' => 'string',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'student_id', 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'semester_id');
    }

    public function collector()
    {
        return $this->hasOne(Admin::class, 'admin_id', 'admin_id');
    }

    public function program()
    {
        return $this->hasOneThrough(
            Program::class,
            Student::class,
            'student_id',
            'program_id',
            'student_id',
            'program_id'
        );
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'admin_id', 'admin_id');
    }
}
