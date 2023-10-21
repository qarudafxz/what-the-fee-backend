<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    //
    public function getAllStudentsPerProgram()
    {
        //retrieve only the student id, student name and program
        $students = Student::select('id', 'name', 'program')->get();
        return response()->json($students);
    }
}
