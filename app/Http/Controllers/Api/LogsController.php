<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;

class LogsController extends Controller
{
    //
    public function getLogs()
    {
        $logs = Log::join('admins', 'admins.admin_id', '=', 'logs.admin_id')
            ->select(
                'logs.*',
                'admins.first_name as admin_first_name',
                'admins.last_name as admin_last_name'
            )
            ->orderBy('logs.created_at', 'DESC')
            ->get();

        return response()->json($logs);
    }
}
