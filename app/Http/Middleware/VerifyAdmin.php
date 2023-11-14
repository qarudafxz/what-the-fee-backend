<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Admin;

class VerifyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //get first the admin from the request headers with admin_id key
        $admin = Admin::findOrFail($request->header('admin_id'));

        if ($admin->role != 'admin' && $admin->role != 'super') {
            return response()->json(
                [
                    'message' => 'You are not an admin',
                ],
                401
            );
        }

        //validate the token from headers
        if (!$request->header('Authorization')) {
            return response()->json(
                [
                    'message' => 'Invalid token',
                ],
                401
            );
        }

        return $next($request);
    }
}
