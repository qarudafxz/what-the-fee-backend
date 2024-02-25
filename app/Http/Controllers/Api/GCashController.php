<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use App\Models\Payment;
use App\Models\Student;

class GCashController extends Controller
{
    //
    public function pay(string $student_id, string $amount)
    {
        //provide step by step instructions on how this api functions

        //get the student id and amount from the request
        //convert the amount to integer
        $amount = (int) $amount;

        //check if the student id exists in the database
        $student = Student::where('student_id', $student_id)->first();

        //check if the student has a balance

        if ($student->balance == 0.0) {
            return response()->json(
                [
                    'message' => 'You have already paid the remaining balance.',
                ],
                422
            );
        }

        //check if the amount has a value
        if ($amount <= 0) {
            return response()->json(
                [
                    'message' => 'Please enter an amount.',
                ],
                422
            );
        }

        //provide a meta data for the paymongo api
        //this is the required format for the paymongo api
        $require = [
            'data' => [
                'type' => 'checkout_session',
                'description' => 'CCISLSG College Fee Payment',
                'attributes' => [
                    'line_items' => [
                        [
                            'name' => 'CCISLSG College Fee Payment',
                            'quantity' => 1,
                            'amount' => $amount * 100,
                            'currency' => 'PHP',
                            'description' => 'CCISLSG College Fee Payment',
                        ],
                    ],
                    'statement_descriptor' => 'College Fee Payment',
                    'payment_method_types' => ['gcash'],
                    'payment_method_allowed' => ['gcash'],
                    'metadata' => [
                        'student-id' => $student_id,
                    ],
                ],
                'success_url' => 'http://localhost:5173/dashboard',
                'cancel_url' => 'http://localhost:5173/dashboard',
            ],
        ];

        //send the request to the paymongo api
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => 'Basic ' . env('PAYMONGO_SECRET_KEY'),
        ])->post('https://api.paymongo.com/v1/checkout_sessions', $require);

        //check if the request is successful
        if ($response) {
            $checkout_url = $response->json()['data']['attributes'][
                'checkout_url'
            ];

            $student = Student::findOrFail($student_id);

            $payment = new Payment();

            //get the last payment ar_no
            $last_payment = Payment::orderBy('created_at', 'desc')->first();

            //since the format of the ar_no is "AR" followed by number and is a string type
            //we need to remove the "AR" and convert the number to integer

            $deductibleAmount = min($amount, $student->balance);

            $last_payment_ar_no = (int) substr(
                $last_payment->ar_no,
                2,
                strlen($last_payment->ar_no)
            );

            //increment the ar_no by 1
            $last_payment_ar_no++;

            //add the "AR" to the ar_no
            $payment->ar_no = 'AR' . $last_payment_ar_no;
            $payment->student_id = $student_id;
            $deductibleAmount < $student->balance
                ? ($payment->desc = 'partial')
                : ($payment->desc = 'full');
            $payment->amount = $amount;
            $payment->date = date('Y-m-d');
            $payment->semester_id = 1;
            $payment->acad_year = '2023-2024';

            $payment->save();

            $student->balance -= $deductibleAmount;
            $student->save();

            return response()->json([
                'checkout_url' => $checkout_url,
            ]);
        }
    }
}
