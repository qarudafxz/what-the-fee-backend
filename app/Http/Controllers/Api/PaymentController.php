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
        $student = Student::findOrFail($request->student_id);
        Payment::create($request->all());
        //call the getStudent function and subtract the amount to the balance
        $student->balance -= $request->amount;
        $student->save();

        return response()->json([
            'message' => 'Payment added',
            'payment' => $request->all(),
        ]);
    }
}
