<?php

namespace App\Services;

class AuthServices
{
    public function login(array $data)
    {
        return JWTAuth::attempt($data);
    }
    public function generateTokens(User $user): array
    {
        $accessToken = JWTAuth::claims($this->buildCustomClaims($user))->fromUser($user);
        $refreshToken = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => Carbon::now()->addDays(30)
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => JWTAuth::factory()->getTTL()
        ];
    }
    private function buildCustomClaims(User $user): array
    {
        return [
            'role' => $user->role,
        ];
    }


}
