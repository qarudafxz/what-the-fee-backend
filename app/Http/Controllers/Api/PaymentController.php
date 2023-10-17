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
        $payment = new Payment();
        $payment->id = $request->id;
        $payment->ar_no = $request->ar_no;
        $payment->desc = $request->desc;
        $payment->student_id = $request->student_id;
        $payment->amount = $request->amount;
        $payment->date = $request->date;
        $payment->admin_id = $request->admin_id;
        $payment->semester_id = $request->semester_id;
        $payment->acad_year = $request->acad_year;
        $payment->save();

        //call the getStudent function and subtract the amount to the balance
        $student = $this->getStudent($request->student_id);
        $student->balance -= $request->amount;
        $student->save();

        return response()->json([
            'message' => 'Payment added',
        ]);
    }

    protected function getStudent($student_id)
    {
        $student = Student::find($student_id);

        return $student;
    }
}
