<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Student;

class PaymentController extends Controller
{
    //
    public function addPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'amount' => 'required|numeric',
            'semester_id' => 'required',
            'acad_year' => 'required',
        ]);
        //call the getStudent function and subtract the amount to the balance
        $student = Student::findOrFail($request->student_id);

        //if the remaining balance of the student is greater than the payable amount
        if ($request->amount < $student->balance && $request->amount != 0) {
            $payment = new Payment();
            $payment->fill($request->except('desc'));
            $payment->desc = 'partial';
            $payment->save();

            $student->balance -= $request->amount;
            $student->save();

            return response()->json([
                'message' => 'Payment added',
                'payment' => $payment,
            ]);
        } elseif ($request->amount >= $student->balance) {
            $payment = new Payment();
            $payment->fill($request->except('desc'));
            $payment->desc = 'full';
            $payment->save();
            $student->balance -= $request->amount;
            $student->save();

            return response()->json([
                'message' => 'Payment added',
                'payment' => $payment,
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

    public function getAllPayment()
    {
        return response()->json([
            'message' => 'All payments retrieved',
            'payments' => Payment::with([
                'semester',
                'student',
                'collector',
                'program',
            ])->get(),
        ]);
    }

    public function filterStudent(Request $request)
    {
        $student = Student::where(
            $request->parameters,
            $request->input_value
        )->first();

        return response()->json([
            'message' => 'Student record retrieved',
            'student' => $student,
        ]);
    }

    public function getLastPaymentAr()
    {
        $last_payment = Payment::select('ar_no')
            ->orderBy('ar_no', 'desc')
            ->first();

        return response()->json([
            'message' => 'Last payment retrieved',
            'last_payment' => $last_payment,
        ]);
    }

    public function searchStudent(string $student_id)
    {
        $student = Student::with(['program'])
            ->where('student_id', $student_id)
            ->firstOrFail();

        return response()->json([
            'message' => 'Student found',
            'student' => $student,
        ]);
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
