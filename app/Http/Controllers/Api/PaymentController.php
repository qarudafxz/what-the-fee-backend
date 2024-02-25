<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Receipt;
use App\Models\Permission;
use App\Models\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function addPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'amount' => 'required|numeric',
            'semester_id' => 'required',
            'acad_year' => 'required',
        ]);

        $admin_permission = Permission::findOrFail(
            $request->header('admin_id')
        );

        if (
            !$admin_permission->can_add ||
            $admin_permission->can_add !== true
        ) {
            return response()->json([
                'statusCode' => 400,
                'status' => 'failed',
                'message' => 'Admin not permitted to do such action',
            ]);
        }

        $student = Student::findOrFail($request->student_id);

        if ($student->balance == 0.0) {
            return response()->json([
                'statusCode' => 400,
                'status' => 'failed',
                'message' => 'Already paid the remaining balance',
            ]);
        }

        $deductibleAmount = min($request->amount, $student->balance);

        $payment = new Payment();
        $payment->fill($request->except('desc'));

        if ($deductibleAmount < $student->balance) {
            $payment->desc = 'partial';
        } else {
            $payment->desc = 'full';
        }

        $payment->save();

        $student->balance -= $deductibleAmount;
        $student->save();

        $log = new Log();
        $log->label =
            'added a new payment record with the amount of ' . $payment->amount;
        $log->method = 'POST';
        $log->admin_id = $payment->admin_id;
        $log->ar_no = $payment->ar_no;

        $log->save();

        return response()->json([
            'message' => 'Payment added',
            'payment' => $payment,
        ]);
    }

    //Total College Fee Collection function - Alx
    public function getTotalPayment(int $college_id)
    {
        //provide step by step comment on this function

        //get the total payment of the college

        //join the payments table with students table
        //join the students table with programs table
        //join the programs table with colleges table
        //get the total payment of the college
        //where the college_id is equal to the college_id provided
        //sum the amount of the payments
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
        $last_payment = Payment::join(
            'students',
            'students.student_id',
            '=',
            'payments.student_id'
        )
            ->select('payments.*', 'students.first_name', 'students.last_name')
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

    //Get the remaining balance of the student - Alx
    public function getStudentBalance(string $student_id)
    {
        //provide step by step comment on this function

        //get the student
        //where the student_id is equal to the student_id provided
        //select the student_id, first_name, last_name, and balance

        $student = Student::where('student_id', $student_id)
            ->select('student_id', 'first_name', 'last_name', 'balance')
            ->get();

        //if the student is not found
        //return a json response with a message of 'Student not found'
        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
            ]);
        }

        //return a json response with a message of 'Successfully retrieved student'
        return response()->json([
            'message' => 'Successfully retrieved student ',
            $student,
            ' remaining balance',
            'student' => $student,
        ]);
    }

    public function getPercentageOfLast7daysCollection(int $college_id)
    {
        $last_7_days = Payment::whereBetween('payments.created_at', [
            now()->subDays(7),
            now(),
        ])
            ->join(
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
        $total_payment = Payment::sum('amount');
        $percentage = ($last_7_days / $total_payment) * 100;

        return response()->json([
            'message' => 'Percentage of last 7 days collection',
            'percentage' => $percentage,
            'payment' => $last_7_days,
        ]);
    }

    public function getPercentageOfLast30daysCollection(int $college_id)
    {
        $last_30_days = Payment::whereBetween('payments.created_at', [
            now()->subDays(30),
            now(),
        ])
            ->join(
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

        $total_payment = Payment::sum('amount');
        $percentage = ($last_30_days / $total_payment) * 100;

        return response()->json([
            'message' => 'Percentage of last 30 days collection',
            'percentage' => $percentage,
            'payment' => $last_30_days,
        ]);
    }

    public function getLatestPayee(int $college_id)
    {
        $latest_payee = Payment::join(
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
            ->orderBy('payments.created_at', 'desc')
            ->first();

        return response()->json([
            'message' => 'Latest payee',
            'latest_payee' => $latest_payee,
        ]);
    }

    //Get the student logs - Ch
    public function getStudentLogs(string $student_id)
    {
        //provide step by step comment on this function

        //get the student logs
        //where the student_id is equal to the student_id provided
        //select the student_id, first_name, last_name, and balance
        $studentPayment = Payment::where('student_id', $student_id)
            ->with(['collector'])
            ->get();

        //return a json response with a message of 'Payment retrieved'
        return response()->json([
            'message' => 'Payment retrieved',
            'payments' => $studentPayment,
        ]);
    }

    //Get the total payment of the student - Ch
    public function getTotalPaymentOfStudent(string $student_id)
    {
        //provide step by step comment on this function

        //get the total payment of the student
        //where the student_id is equal to the student_id provided
        //sum the amount of the payments
        $total_payment = Payment::where('student_id', $student_id)->sum(
            'amount'
        );

        //return a json response with a message of 'Total payment retrieve of student'
        return response()->json([
            'message' => 'Total payment retrieve of student',
            'total_payment' => $total_payment,
        ]);
    }

    public function getTotalPaymentPerMonthInCurrentYear(int $college_id)
    {
        $total_payment = Payment::whereYear('payments.date', now()->year)
            ->join(
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
            ->selectRaw(
                'sum(amount) as total_payment, EXTRACT(MONTH FROM payments.date) as payment_month'
            )
            ->groupByRaw('EXTRACT(MONTH FROM payments.date)')
            ->get();

        $monthly_data = [];

        // Convert month number to month name

        //Carbon makes it easy to convert month number to month name
        //!m is the format for month number
        //'F' is the format for month name
        foreach ($total_payment as $payment) {
            $monthName = Carbon::createFromFormat(
                '!m',
                $payment->payment_month
            )->format('F');
            $monthly_data[] = [
                'month' => $monthName,
                'total_payment' => $payment->total_payment,
            ];
        }

        return response()->json([
            'message' => 'Total payment per month in current year',
            'monthly_data' => $monthly_data,
        ]);
    }

    public function savePayment(string $ar_no)
    {
        $payment = Payment::where('ar_no', $ar_no)->firstOrFail();

        $receipt = new Receipt();
        $receipt->ar_no = $payment->ar_no;
        $receipt->save();

        return response()->json([
            'message' => 'Receipt saved',
            'receipt' => $receipt,
        ]);
    }
}
