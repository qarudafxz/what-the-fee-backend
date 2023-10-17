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

    protected $casts = [
        'student_id' => 'string',
    ];
}
