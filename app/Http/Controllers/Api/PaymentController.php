<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Student;

class PaymentController extends Controller
{
    //
    public function addPayment(Request $request)
    {
        //call the getStudent function and subtract the amount to the balance
        $student = Student::findOrFail($request->student_id);

        if ($student->balance >= $request->amount) {
            Payment::create($request->all());
            $student->balance -= $request->amount;
            $student->save();

            return response()->json([
                'message' => 'Payment added',
                'payment' => $request->all(),
            ]);
        } else {
            return response()->json([
                'statusCode' => 400,
                'status' => 'failed',
                'message' => 'Already paid the remaining balance',
            ]);
        }
    }

    public function getTotalPayment(int $college_id)
    {
        $all_payment = Payment::join(
            'students',
            'students.student_id',
            '=',
            'payments.student_id'
        )
            ->join(
                'programs',
                'programs.program_id',
                '=',
                'students.program_id'
            )
            ->join(
                'colleges',
                'colleges.college_id',
                '=',
                'programs.college_id'
            )
            ->where('colleges.college_id', $college_id)
            ->sum('amount');

        return response()->json([
            'total_payment' => $all_payment,
        ]);
    }

    public function getPaymentByStudentId(Request $request)
    {
        $payment = Payment::findOrFail('student_id', $request->student_id);

        return response()->json([
            'message' => 'Student record retrieved',
            'payment' => $payment,
        ]);
    }

    public function getAllPayment(int $college_id)
    {
        $payment = Payment::join(
            'students',
            'students.student_id',
            '=',
            'payments.student_id'
        )
            ->join(
                'programs',
                'programs.program_id',
                '=',
                'students.program_id'
            )
            ->join(
                'colleges',
                'colleges.college_id',
                '=',
                'programs.college_id'
            )
            ->join('admins', 'admins.college_id', '=', 'colleges.college_id')
            ->select(
                'payments.ar_no',
                'payments.amount',
                'payments.desc',
                'payments.created_at',
                'students.student_id',
                'students.first_name',
                'students.last_name',
                'sem.semester_name',
                'sem.acad_year',
                'programs.program_name',
                'colleges.college_name',
                'admins.first_name as admin_first_name',
                'admins.last_name as admin_last_name'
            )
            ->join(
                'semesters',
                'payments.semester_id',
                '=',
                'semesters.semester_id'
            )
            ->join(
                'programs as pr',
                'pr.program_id',
                '=',
                'students.program_id'
            )
            ->join('colleges as c', 'c.college_id', '=', 'pr.college_id')
            ->join(
                'semesters as sem',
                'sem.semester_id',
                '=',
                'payments.semester_id'
            )
            ->where('c.college_id', $college_id)
            ->groupBy(
                'payments.ar_no',
                'payments.amount',
                'payments.desc',
                'payments.created_at',
                'students.student_id',
                'students.first_name',
                'sem.semester_name',
                'sem.acad_year',
                'students.last_name',
                'programs.program_name',
                'colleges.college_name',
                'admins.first_name',
                'admins.last_name'
            )
            ->distinct()
            ->get();

        return response()->json([
            'message' => 'All payments retrieved',
            'payments' => $payment,
        ]);
    }
}
