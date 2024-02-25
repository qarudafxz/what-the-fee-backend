<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    //
    public function getNotifications(string $student_id)
    {
        return response()->json([
            'notifications' => Notification::where('student_id', $student_id)
                ->orderBy('notifications.created_at', 'DESC')
                ->get(),
        ]);
    }

    public function deleteAllNotifications(string $student_id)
    {
        Notification::where('student_id', $student_id)->delete();
        return response()->json([
            'message' => 'All notifications deleted.',
        ]);
    }
}
