<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
class AdminController extends Controller
{
    //
    public function getAllAdminSpecificCollege(int $college_id)
    {
        $admins = Admin::where('college_id', $college_id)
        //admin_id if local
            ->select('student_id', 'email', 'first_name', 'last_name', 'role')
            ->get();

        return response()->json([
            'statusCode' => 200,
            'status' => 'success',
            'message' => 'Admin found',
            'admins' => $admins,
        ]);
    }
}
