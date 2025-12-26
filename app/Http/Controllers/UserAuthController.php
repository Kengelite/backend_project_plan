<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

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
            $urlImg = $user->url_img;
            $token = $user->createToken('my-app-token', [$role])->plainTextToken;

            return response()->json(['token' => $token, 'fullname' => $fullname, 'id' => $userID, "role" => $role, "img" => $urlImg], 200);
        }

        return response()->json(['message' => 'Incorrect credentials'], 401);
    }

    public function loginwithgmail(Request $request)
    {
        // 1. ตรวจสอบว่ามี token ส่งมาไหม

        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->token;
        try {

            // 2. โหลด Public Keys ของ Google
            //    เพื่อใช้ตรวจสอบว่าตัว token ที่ผู้ใช้ส่งมา "ถูกเซ็นจริง"
            //    Google เปลี่ยน key เป็นระยะๆ ดังนั้นต้องโหลดจาก URL นี้เสมอ
            $jwks = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v3/certs'), true);


            // 3. ตรวจสอบว่าตัว token ถูกต้องหรือไม่
            //    - ถ้า token ถูกปลอม / หมดอายุ / แก้ไข จะ decode ไม่ได้
            //    - ถ้าเป็น token ที่ Google เซ็นจริง → decode ผ่าน
            try {
                $decoded = JWT::decode($token, JWK::parseKeySet($jwks));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
            // Email จาก Google
            $email = $decoded->email;

            // ตรวจสิทธิ์ในระบบ (เช่นดูว่ามีในตาราง users ไหม)
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['error' => 'No permission'], 403);
            }

            // return response()->json([
            //     'status' => 'ok',
            //     'email' => $email,
            //     'message' => $jwks
            // ]);
            // $user = $request->user();
            $fullname = $user->name;
            $role = $user->role;
            $userID = $user->id;
            $urlImg = $decoded->picture;
            $token = $user->createToken('my-app-token', [$role])->plainTextToken;

            return response()->json(['token' => $token, 'fullname' => $fullname, 'id' => $userID, "role" => $role, "img" => $urlImg], 200);
        } catch (\Exception $e) {
            // กรณี Token ไม่ถูกต้อง หรือ ติดต่อ Google ไม่ได้
            return response()->json([
                'message' => 'Invalid Google Token or Login Failed',
                'error' => $e->getMessage()
            ], 401);
        }
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
