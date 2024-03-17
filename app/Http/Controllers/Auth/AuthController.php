<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Thêm dòng này
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            // Đăng nhập thất bại
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Đăng nhập thành công, trả về token
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => Auth::factory()->getTTL() * 60 // Thời gian sống của token (tùy chọn)
            'username' => auth()->user()->name, // Lấy name của người dùng và thêm vào phản hồi
        ]);
    }
}


