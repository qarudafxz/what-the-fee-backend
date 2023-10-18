<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
class AdminController extends Controller
{
    //
    public function getAllAdminSpecificCollege(Request $request)
    {
        $admins = Admin::where('college_id', $request->college_id)
            ->where('role', $request->role)
            ->select('admin_id', 'email', 'first_name', 'last_name', 'role')
            ->get();

        return response()->json([
            'statusCode' => 200,
            'status' => 'success',
            'message' => 'Admin found',
            'admins' => $admins,
        ]);
    }
}
