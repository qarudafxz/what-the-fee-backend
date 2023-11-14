<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipt;

class ReceiptController extends Controller
{
    public function getReceipts()
    {
        return response()->json([
            'receipts' => Receipt::select(['receipt_id', 'ar_no'])->get(),
        ]);
    }

    public function getFullDetailsOfReceipt(string $ar_no)
    {
        $receipts = Receipt::join(
            'payments',
            'payments.ar_no',
            '=',
            'receipts.ar_no'
        )
            ->join(
                'students',
                'students.student_id',
                '=',
                'payments.student_id'
            )
            ->join(
                'semesters',
                'semesters.semester_id',
                '=',
                'payments.semester_id'
            )
            ->join('admins', 'admins.admin_id', '=', 'payments.admin_id')
            ->select(
                'payments.date',
                'receipts.ar_no as ar_no',
                'students.first_name as first_name',
                'students.last_name as last_name',
                'students.student_id as student_id',
                'payments.amount as amount',
                'semesters.semester_name as semester',
                'semesters.acad_year as acad_year',
                'admins.admin_id as admin_id'
            )
            ->where('receipts.ar_no', $ar_no)
            ->get();

        if (!$receipts) {
            return response()->json([
                'message' => 'Receipt not found',
            ]);
        }

        return response()->json([
            'message' => 'Receipt retrieved',
            'receipt' => $receipts,
        ]);
    }
}
