<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model implements Authenticatable
{
    use AuthenticatableTrait, HasFactory, HasApiTokens, Notifiable;

    protected $primaryKey = 'student_id';
    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'student_id' => 'string',
    ];

    protected $hidden = ['password'];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    // Additional methods for the Authenticatable interface
    public function getAuthIdentifierName()
    {
        return 'student_id';
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute('student_id');
    }

    public function getAuthPassword()
    {
        return $this->getAttribute('password');
    }

    // need to implement other methods as well, depending on your requirements.
}
