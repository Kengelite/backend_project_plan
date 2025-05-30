<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class EmployeeAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $employee = $request->employee();
            $token = $employee->createToken('my-app-token', ['admin'])->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Incorrect credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->employee()->currentAccessToken()->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'User logged out successfully'
            ]
        );
    }
}
