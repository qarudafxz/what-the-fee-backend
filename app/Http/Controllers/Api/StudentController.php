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

    public function searchStudent(string $student_id)
    {
        $student = Student::find($student_id);
        return response()->json([
            'message' => 'Student found',
            'student' => $student,
        ]);
    }
}
