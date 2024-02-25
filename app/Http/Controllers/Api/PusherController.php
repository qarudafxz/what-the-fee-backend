<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pusher\Pusher;

use Illuminate\Support\Facades\Auth;

class PusherController extends Controller
{
    public function pusherAuth(Request $request)
    {
        $user = Auth::user(); // Retrieve the authenticated user

        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        // Extract the Authorization header
        $authHeader = $request->header('Authorization');

        $options = [
            'cluster' => 'ap1',
            'useTLS' => true,
        ];

        $pusher = new Pusher(
            '7c7a03a437d59fe674fe',
            'd015e15e3b63a3a2a50a',
            '1717668',
            $options
        );

        try {
            // Authenticate the user and include the Authorization header
            $auth = $pusher->socket_auth($channelName, $socketId, $authHeader);
            return response($auth);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Failed to authenticate.'],
                403
            );
        }
    }
}
