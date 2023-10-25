<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'arr_no';
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
        //change student_id to admin_id later
        return $this->hasOne(Admin::class, 'student_id', 'student_id');
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
}
