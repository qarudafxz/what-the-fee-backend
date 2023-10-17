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
            ->get();

        return response()->json([
            'message' => 'Admin retrieve',
            'data' => $admins,
        ]);
    }
}
