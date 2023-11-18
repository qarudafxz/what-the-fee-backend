<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Request as AdminRequest;
use App\Models\Payment;
use App\Models\Log;

class RequestController extends Controller
{
    public function createRequest(Request $request)
    {
        //check the admin, its permission if its allowed to make a request, and persist the data inside the request table
        //the updating of record depends on what method is being used, either UPDATE or DELETE

        $request->validate([
            'ar_no' => 'required|string',
            'request_type' => 'required|string|max:10',
            'desc' => 'required|string|max:255',
            'value_of_request' => 'string|max:255',
            'admin_id' => 'required|string|max:10',
        ]);

        $admin = Admin::where(
            'admin_id',
            $request->header('admin_id')
        )->first();

        $permission_of_admin = Permission::where(
            'admin_id',
            $admin->admin_id
        )->first();

        //check if the method of request is same with the permission given

        $newAdminRequest = new AdminRequest();
        //if the request method is update and the admin is allowed TO DO an update, grant a request first

        if ($admin->role === 'admin') {
            if (
                $request->request_type === 'UPDATE' &&
                $permission_of_admin->can_update === true
            ) {
                $newAdminRequest->fill($request->except('request_type'));
                $newAdminRequest->request_type = 'UPDATE';
            } elseif (
                $request->request_type === 'DELETE' &&
                $permission_of_admin->can_delete === true
            ) {
                $newAdminRequest->fill(
                    $request->except('request_type', 'desc')
                );
                $newAdminRequest->desc = 'Wrong values';
                $newAdminRequest->request_type = 'DELETE';
            } else {
                return response()->json([
                    'error' => true,
                    'message' =>
                        'You are not allowed to make ' .
                        $request->request_type .
                        ' action',
                ]);
            }

            $newAdminRequest->save();

            return response()->json([
                'error' => false,
                'message' =>
                    'Request created. Please wait for approval from your super admin',
                'request' => $newAdminRequest,
            ]);
            //if the admin is superadmin
            //all of the permission is granted for the superadmin
        } else {
            if ($request->request_type === 'DELETE') {
                $payment = Payment::findOrFail($request->ar_no);
                $payment->delete();

                $log = new Log();
                $log->label = 'deleted the record of ' . $request->ar_no;
                $log->method = 'DELETE';
                $log->admin_id = $admin->admin_id;
                $log->ar_no = $request->ar_no;
                $log->save();

                return response()->json([
                    'error' => false,
                    'message' => 'Payment record successfully deleted',
                    'payment' => $payment,
                ]);
            }

            if ($request->request_type === 'UPDATE') {
                //statements
            }
        }
    }

    public function getAllRequests()
    {
        $requests = AdminRequest::select(
            'requests.*',
            'admins.first_name',
            'admins.last_name',
            'admins.email'
        )
            ->join('admins', 'admins.admin_id', '=', 'requests.admin_id')
            ->get();

        return response()->json([
            'error' => false,
            'message' => 'All requests',
            'requests' => $requests,
        ]);
    }

    public function getSelectedRequest(int $request_id)
    {
        $request = AdminRequest::select(
            'requests.*',
            'admins.first_name',
            'admins.last_name',
            'admins.email'
        )
            ->join('admins', 'admins.admin_id', '=', 'requests.admin_id')
            ->where('requests.request_id', $request_id)
            ->first();

        //check if the request is existing
        if (!$request) {
            return response()->json([
                'error' => true,
                'message' => 'Request not found',
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'Request found',
            'request' => $request,
        ]);
    }

    public function declineRequest(int $request_id)
    {
        $adminRequest = AdminRequest::findOrFail($request_id);
        $log = new Log();

        $log->label = "'s request is being declined. ";
        $log->method = 'DECLINED';
        $log->admin_id = $adminRequest->admin_id;
        $log->ar_no = $adminRequest->ar_no;
        $log->save();

        $adminRequest->delete();

        return response()->json([
            'error' => false,
            'message' => 'Request declined',
        ]);
    }

    public function grantRequest(int $request_id, Request $request)
    {
        //find first the request

        $request = AdminRequest::findOrFail($request_id);

        //check the admin who is requesting
        $admin = Admin::where('admin_id', $request->admin_id)->first();

        $permission = Permission::where('admin_id', $admin->admin_id)->first();

        $log = new Log();

        if (
            $permission->can_delete === true &&
            $request->request_type === 'DELETE'
        ) {
            $payment = Payment::findOrFail($request->ar_no);
            $payment->delete();

            $log->label = ' deleted the record of ' . $request->ar_no;
            $log->method = 'DELETE';
            $log->admin_id = $admin->admin_id;
            $log->ar_no = $request->ar_no;
            $log->save();

            $request->delete();

            return response()->json([
                'error' => false,
                'message' => 'Request granted. Record successfully deleted',
                'payment' => $payment,
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' =>
                    'Admin who requested is not allowed to delete a record',
            ]);
        }

        if (
            $permission->can_update === true &&
            $request->request_type === 'UPDATE'
        ) {
            $payment = Payment::findOrFail($request->ar_no);
            //workout the fillables
            // $payment->fill($request->except('request_type'));
            $payment->save();

            $log->label = 'updated the record of ' . $request->ar_no;
            $log->method = 'UPDATE';
            $log->admin_id = $admin->admin_id;
            $log->ar_no = $request->ar_no;
            $log->save();

            $request->delete();

            return response()->json([
                'error' => false,
                'message' => 'Request granted. Record successfully updated',
                'payment' => $payment,
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' =>
                    'Admin who requested is not allowed to update a record',
            ]);
        }
    }
}
