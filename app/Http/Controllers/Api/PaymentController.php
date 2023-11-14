<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Receipt;
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

        return response()->json([
            'message' => 'Payment added',
            'payment' => $payment,
        ]);
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
        $student = Student::where('student_id', $student_id)
            ->select('student_id', 'first_name', 'last_name', 'balance')
            ->get();

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

    public function getStudentLogs(string $student_id)
    {
        $studentPayment = Payment::where('student_id', $student_id)
            ->with(['collector'])
            ->get();

        return response()->json([
            'message' => 'Payment retrieved',
            'payments' => $studentPayment,
        ]);
    }

    public function getTotalPaymentOfStudent(string $student_id)
    {
        $total_payment = Payment::where('student_id', $student_id)->sum(
            'amount'
        );

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

    public function updatePaymentRecord(Request $request, string $ar_no)
    {
    }
}
