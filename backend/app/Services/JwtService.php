<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtService
{
    private string $secret;
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', env('APP_KEY'));

        if (empty($this->secret)) {
            throw new \RuntimeException('JWT_SECRET hoặc APP_KEY chưa được cấu hình');
        }
    }

    /**
     * Tạo access token JWT chứa thông tin user
     */
    public function createAccessToken(User $user, string $sessionId, int $ttlMinutes = 60): string
    {
        $issuedAt = time();
        $expireAt = $issuedAt + ($ttlMinutes * 60);

        $payload = [
            'iss' => env('APP_URL', 'http://localhost'),
            'iat' => $issuedAt,
            'exp' => $expireAt,
            'sub' => $user->id,

            // Thông tin user trong token
            'user' => [
                'id' => $user->id,
                'org_id' => $user->org_id,
                'email' => $user->email,
                'phone' => $user->phone,
                'name' => $user->name,
                'is_active' => $user->is_active,
            ],

            // Session ID để liên kết với UserSession
            'session_id' => $sessionId,
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Verify và decode JWT token
     */
    public function verifyToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {
            throw new \RuntimeException('Token không hợp lệ: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin user từ token (không verify DB)
     */
    public function getUserFromToken(string $token): ?object
    {
        try {
            $decoded = $this->verifyToken($token);
            return $decoded->user ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Kiểm tra token đã hết hạn chưa
     */
    public function isExpired(string $token): bool
    {
        try {
            $decoded = $this->verifyToken($token);
            return ($decoded->exp ?? 0) < time();
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Lấy session_id từ token
     */
    public function getSessionId(string $token): ?string
    {
        try {
            $decoded = $this->verifyToken($token);
            return $decoded->session_id ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
