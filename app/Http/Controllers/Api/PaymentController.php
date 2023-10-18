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

        if (!$student->balance >= $request->amount) {
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
}
