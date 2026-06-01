<?php

namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Models\RefreshToken;
use App\Services\AuthServices;



class AuthController extends Controller
{
    public function login( LoginRequest $request)
    {
        $data = $request->validated();
        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['Неверный email или пароль.'],
            ]);

        }
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'token' => (new AuthServices())->generateTokens($user),
        ]);


    }
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->refreshTokens()->delete();
        return response()->json([
            'message' => 'Вы успешно вышли из системы',
        ]);
    }
    public function refresh(RefreshTokenRequest $request)
    {
        $data = $request->validated();
        $refreshToken = RefreshToken::where('token', $data['refresh_token'])->first();
        if (!$refreshToken) {
            return response()->json([
                'message' => 'Refresh token is invalid',
            ], 401);
        }
        if ($refreshToken->expires_at < now())
        {
            $refreshToken->delete();
            return response()->json([
                'message' => 'Refresh token is expired',
            ], 401);
        }
        $user = $refreshToken->user;
        $refreshToken->delete();
        return response()->json([
            'user' => $user,
            'token' => (new AuthServices())->generateTokens($user),
        ]);
    }



}
