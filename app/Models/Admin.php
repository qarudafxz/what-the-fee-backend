<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    //change primaryKey to admin_id if env setup for db is local
    protected $primaryKey = 'student_id';
    protected $guarded = [];

    public $incrementing = false;

    public $hidden = ['password'];
}
