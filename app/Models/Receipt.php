<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $primaryKey = 'receipt_id';

    public function payment()
    {
        return $this->hasOne(Payment::class, 'ar_no', 'ar_no');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'student_id', 'student_id');
    }
}
