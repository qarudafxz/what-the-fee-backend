<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    //
    public function getAllStudentsPerProgram()
    {
        $students = Student::all();
        return response()->json($students);
    }
}
