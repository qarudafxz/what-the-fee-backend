<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //if request token is empty, return unauthorized
        //headers token is the token that is passed in the header

        if (empty($request->headers->get('token'))) {
            return response()->json(
                [
                    'message' => 'Unauthorized user. Please login first',
                ],
                401
            );
        }

        return $next($request);
    }
}
