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

    public function getStudentBalance(string $student_id)
    {
        $student = Student::where('student_id', $student_id)->select(
            'student_id',
            'first_name',
            'last_name',
            'balance'
        );

        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
            ]);
        }

        return response()->json([
            'message' => 'Successfully retrieved student ',
            $student,
            ' remaining balance',
            'student' => $student,
        ]);
    }
}
