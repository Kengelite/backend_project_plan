<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = $request->user();
            $fullname = $user->name;
            $role = $user->role;
            $userID = $user->id;
            $token = $user->createToken('my-app-token', [$role])->plainTextToken;

            return response()->json(['token' => $token, 'fullname' => $fullname, 'id' => $userID, "role" => $role], 200);
        }

        return response()->json(['message' => 'Incorrect credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'User logged out successfully'
            ]
        );
    }
}
