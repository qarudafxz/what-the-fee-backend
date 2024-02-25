<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Receipt;
use App\Models\Admin;
use App\Models\Log;
use App\Models\Student;
use App\Models\Notification;

use Pusher\Pusher;

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

    public function archiveReceipt(string $ar_no, Request $request)
    {
        $receipt = Receipt::where('ar_no', $ar_no)->first();

        $admin = Admin::where(
            'admin_id',
            $request->header('admin_id')
        )->first();

        if (!$receipt) {
            return response()->json(
                [
                    'message' => 'Receipt not found',
                ],
                404
            );
        }

        $archive = new Archive();
        $archive->receipt_id = $receipt->receipt_id;
        $archive->ar_no = $receipt->ar_no;

        $log = new Log();
        $log->label = 'archived the receipt of number ' . $archive->ar_no;
        $log->method = 'ARCHIVED';
        $log->admin_id = $admin->admin_id;
        $log->ar_no = $archive->ar_no;
        $log->save();

        $archive->save();

        $receipt->delete();

        return response()->json([
            'message' => 'Receipt archived',
        ]);
    }

    public function getArchivedReceipts()
    {
        $archives = Archive::all();

        if (!$archives) {
            return response()->json(
                [
                    'message' => 'No archived receipts',
                ],
                404
            );
        }

        return response()->json([
            'message' => 'Archived receipts retrieved',
            'archives' => $archives,
        ]);
    }

    public function restoreReceiptFromArchives(string $ar_no, Request $request)
    {
        $archive = Archive::where('ar_no', $ar_no)->first();

        if (!$archive) {
            return response()->json(
                [
                    'message' => 'Receipt not found',
                ],
                404
            );
        }

        $receipt = new Receipt();
        $receipt->receipt_id = $archive->receipt_id;
        $receipt->ar_no = $archive->ar_no;

        $admin = Admin::where(
            'admin_id',
            $request->header('admin_id')
        )->first();

        $log = new Log();
        $log->label = 'restore the receipt of number ' . $archive->ar_no;
        $log->method = 'RESTORED';
        $log->admin_id = $admin->admin_id;
        $log->ar_no = $archive->ar_no;

        $receipt->save();
        $log->save();
        $archive->delete();

        return response()->json([
            'message' => 'Receipt restored',
        ]);
    }

    public function undoArchivingOfReceipt()
    {
        //Get the last receipt that was archived
        $archive = Archive::orderBy('created_at', 'desc')->first();

        if (!$archive) {
            return response()->json(
                [
                    'message' => 'No archived receipts',
                ],
                404
            );
        }

        $receipt = new Receipt();
        $receipt->receipt_id = $archive->receipt_id;
        $receipt->ar_no = $archive->ar_no;

        $receipt->save();
        $archive->delete();

        return response()->json([
            'message' => 'Receipt restored',
        ]);
    }

    //This is the last function that is needed to fix
    //Will work on this tomorrow
    public function sendReceiptViaPusher(string $ar_no, Request $request)
    {
        /* 
        
            TODO: From the admin panel, the admin must enter the student's id to match with the pusher's database

            # parameters consists of ar_no to tell what receipt to send
            # and student_id to match with the pusher's database

            # the pusher's database must have a student_id column to match with the student's id    
        */

        $request->validate([
            'student_id' => 'required|string|max:9',
            'note' => 'required|string',
        ]);

        //get the full details of the receipt
        $receipt = Receipt::join(
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

        if (!$receipt) {
            return response()->json(
                [
                    'message' => 'Receipt not found',
                ],
                404
            );
        }

        //get the student's info
        $student = Student::where('student_id', $request->student_id)->first();

        if (!$student) {
            return response()->json(
                [
                    'message' => 'Student not found',
                ],
                404
            );
        }

        //connect to pusher
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    //provide the cluster here to specify where the pusher is located
                    'cluster' => config(
                        'broadcasting.connections.pusher.options.cluster'
                    ),
                    'useTLS' => true,
                ]
            );

            //set the channel and event
            $channel = 'private-student-' . $student->student_id;
            $event = 'client-receipt-received';
            $unique_id = uniqid();

            $data = [
                'id' => $unique_id,
                'receipt' => $receipt,
                'message' => 'You have a new receipt!',
                'note' => $request->note,
                'created_at' => now(),
            ];

            //add the notification on the database
            //so it would be visible on the student's notification page

            $notification = new Notification();
            $notification->student_id = $student->student_id;
            $notification->receipt = $receipt;
            $notification->note = $request->note;
            $notification->save();

            $pusher->trigger($channel, $event, $data);

            return response()->json([
                'message' => 'Receipt sent via Pusher',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending receipt via Pusher',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
