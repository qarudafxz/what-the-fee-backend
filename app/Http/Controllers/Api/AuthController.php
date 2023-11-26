<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:9|unique:students',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:students',
            'password' => 'required|string',
            'program_id' => 'required|int',
            'year_level_code' => 'required|int',
        ]);

        $student = new Student([
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'program_id' => $request->program_id,
            'year_level_code' => $request->year_level_code,
        ]);

        if ($student->save()) {
            $token = $student->createToken('Personal Access Token');

            return response()->json(
                [
                    'message' => 'Successfully created user!',
                    'accessToken' => $token,
                ],
                201
            );
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:9',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $credentials = request(['student_id', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ],
                401
            );
        }

        $student = Auth::user();
        $tokenResult = $student->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'student' => $student,
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
