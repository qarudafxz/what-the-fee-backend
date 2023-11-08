<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Student;

class StudentController extends Controller
{
    //
    public function registerStudentWithSanctum()
    {
    }

    public function studentLogin(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function getAllStudentsPerProgram()
    {
        //retrieve only the student id, student name and program
        $students = Student::select('id', 'name', 'program')->get();
        return response()->json($students);
    }
}
