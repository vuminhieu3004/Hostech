<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Services\OtpService;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthSessionService
{
    public function startOtpChallenge(User $user, array $device, string $ip, ?string $ua, ?string $loginMethod = null): array
    {
        $sessionId = (string) Str::uuid();

        /** @var OtpService $otpSvc */
        $otpSvc = resolve(OtpService::class);

        $otp = $otpSvc->generate(6);
        $ttl = $otpSvc->ttlSeconds();

        // Kiểm tra user có email hoặc phone không
        if (!$user->email && !$user->phone) {
            throw new \RuntimeException('USER_HAS_NO_CONTACT_METHOD');
        }

        UserSession::create([
            'id' => $sessionId,
            'org_id' => $user->org_id,
            'user_id' => $user->id,

            'device_id' => $device['device_id'] ?? null,
            'device_name' => $device['device_name'] ?? null,
            'device_platform' => $device['device_platform'] ?? null,
            'device_fingerprint' => $device['device_fingerprint'] ?? null,

            'is_trusted' => false,
            'ip' => $ip,
            'user_agent' => $ua,
            'last_seen_at' => now(),
            'created_at' => now(),

            'status' => 'PENDING_OTP',
            'otp_hash' => $otpSvc->hash($otp),
            'otp_sent_at' => now(),
            'otp_expires_at' => now()->addSeconds($ttl),
            'otp_attempts' => 0,
        ]);

        // Luôn gửi OTP qua email (vì SMS chưa tích hợp)
        // Trong tương lai khi tích hợp SMS, có thể dùng $loginMethod để chọn
        $method = $otpSvc->send($user->email, $user->phone, $otp);

        $res = [
            'session_id' => $sessionId,
            'otp_ttl' => $ttl,
            'otp_method' => $method, // 'sms' hoặc 'email'
        ];

        // Chỉ trả OTP cho local để test
        if (app()->environment('local')) {
            $res['dev_otp'] = $otp;
        }

        return $res;
    }

    public function resendOtp(string $orgId, string $sessionId): array
    {
        $session = UserSession::where('org_id', $orgId)
            ->where('id', $sessionId)
            ->firstOrFail();

        if ($session->revoked_at || $session->status !== 'PENDING_OTP') {
            throw new \RuntimeException('SESSION_NOT_PENDING');
        }

        // Lấy user
        $user = User::where('org_id', $orgId)->where('id', $session->user_id)->firstOrFail();

        if (!$user->email && !$user->phone) {
            throw new \RuntimeException('USER_HAS_NO_CONTACT_METHOD');
        }

        /** @var OtpService $otpSvc */
        $otpSvc = resolve(OtpService::class);

        $otp = $otpSvc->generate(6);
        $ttl = $otpSvc->ttlSeconds();

        $session->otp_hash = $otpSvc->hash($otp);
        $session->otp_sent_at = now();
        $session->otp_expires_at = now()->addSeconds($ttl);
        $session->otp_attempts = 0;
        $session->last_seen_at = now();
        $session->save();

        // Gửi lại OTP
        $method = $otpSvc->send($user->email, $user->phone, $otp);

        $res = [
            'session_id' => $session->id,
            'otp_ttl' => $ttl,
            'otp_method' => $method,
        ];

        if (app()->environment('local')) {
            $res['dev_otp'] = $otp;
        }

        return $res;
    }

    public function verifyOtpAndCreateSession(string $orgId, string $sessionId, string $otp): array
    {
        $session = UserSession::where('org_id', $orgId)
            ->where('id', $sessionId)
            ->firstOrFail();

        if ($session->revoked_at || $session->status !== 'PENDING_OTP') {
            throw new \RuntimeException('SESSION_NOT_PENDING');
        }

        if (!$session->otp_expires_at || now()->gt($session->otp_expires_at)) {
            throw new \RuntimeException('OTP_EXPIRED');
        }

        $max = (int) env('OTP_MAX_ATTEMPTS', 5);
        if ((int) ($session->otp_attempts ?? 0) >= $max) {
            throw new \RuntimeException('OTP_TOO_MANY_ATTEMPTS');
        }

        /** @var OtpService $otpSvc */
        $otpSvc = resolve(OtpService::class);

        if (!$session->otp_hash || !$otpSvc->check($otp, $session->otp_hash)) {
            $session->otp_attempts = (int) ($session->otp_attempts ?? 0) + 1;
            $session->save();
            throw new \RuntimeException('OTP_INVALID');
        }

        // OTP OK => cấp token + refresh
        $user = User::where('org_id', $orgId)
            ->where('id', $session->user_id)
            ->firstOrFail();

        if (!$user->is_active) {
            throw new \RuntimeException('USER_BLOCKED');
        }

        $refreshTtlDays = (int) env('REFRESH_TTL_DAYS', 30);
        $accessTtlMinutes = (int) env('JWT_TTL_MINUTES', 60);

        // Tạo JWT access token chứa thông tin user
        /** @var JwtService $jwtSvc */
        $jwtSvc = resolve(JwtService::class);
        $plainAccessToken = $jwtSvc->createAccessToken($user, $session->id, $accessTtlMinutes);

        // refresh token
        $plainRefresh = Str::random(64);
        $refreshHash = Hash::make($plainRefresh);

        $session->status = 'ACTIVE';
        $session->otp_verified_at = now();
        $session->refresh_token_hash = $refreshHash;
        $session->expires_at = now()->addDays($refreshTtlDays);
        $session->last_seen_at = now();
        $session->save();

        // cập nhật last_login_at sau khi OTP OK (chuẩn hơn)
        $user->last_login_at = now();
        $user->save();

        return [
            'session_id' => $session->id,
            'access_token' => $plainAccessToken,
            'refresh_token' => $plainRefresh,
            'expires_at' => $session->expires_at,
        ];
    }

    public function refresh(UserSession $session, User $user, string $refreshToken): array
    {
        if ($session->status !== 'ACTIVE') {
            throw new \RuntimeException('SESSION_NOT_ACTIVE');
        }

        if (!$session->refresh_token_hash || !Hash::check($refreshToken, $session->refresh_token_hash)) {
            throw new \RuntimeException('INVALID_REFRESH_TOKEN');
        }

        if ($session->revoked_at || ($session->expires_at && now()->gt($session->expires_at))) {
            throw new \RuntimeException('REFRESH_EXPIRED_OR_REVOKED');
        }

        $accessTtlMinutes = (int) env('JWT_TTL_MINUTES', 60);

        // Tạo JWT access token mới
        /** @var JwtService $jwtSvc */
        $jwtSvc = resolve(JwtService::class);
        $plainAccessToken = $jwtSvc->createAccessToken($user, $session->id, $accessTtlMinutes);

        // rotate refresh token
        $plainRefresh = Str::random(64);
        $session->refresh_token_hash = Hash::make($plainRefresh);
        $session->last_seen_at = now();
        $session->save();

        return [
            'access_token' => $plainAccessToken,
            'refresh_token' => $plainRefresh,
        ];
    }

    /**
     * Skip OTP và đăng nhập trực tiếp (dùng khi OTP_ENABLED=false)
     */
    public function skipOtpAndLogin(User $user, array $device, string $ip, ?string $ua): array
    {
        $sessionId = (string) Str::uuid();
        $refreshTtlDays = (int) env('REFRESH_TTL_DAYS', 30);
        $accessTtlMinutes = (int) env('JWT_TTL_MINUTES', 60);

        // Tạo session ACTIVE luôn (không cần OTP)
        $plainRefresh = Str::random(64);
        $refreshHash = Hash::make($plainRefresh);

        UserSession::create([
            'id' => $sessionId,
            'org_id' => $user->org_id,
            'user_id' => $user->id,

            'device_id' => $device['device_id'] ?? null,
            'device_name' => $device['device_name'] ?? null,
            'device_platform' => $device['device_platform'] ?? null,
            'device_fingerprint' => $device['device_fingerprint'] ?? null,

            'is_trusted' => false,
            'ip' => $ip,
            'user_agent' => $ua,
            'last_seen_at' => now(),
            'created_at' => now(),

            'status' => 'ACTIVE',
            'refresh_token_hash' => $refreshHash,
            'expires_at' => now()->addDays($refreshTtlDays),
        ]);

        // Tạo JWT access token
        /** @var JwtService $jwtSvc */
        $jwtSvc = resolve(JwtService::class);
        $plainAccessToken = $jwtSvc->createAccessToken($user, $sessionId, $accessTtlMinutes);

        // Cập nhật last_login_at
        $user->last_login_at = now();
        $user->save();

        return [
            'session_id' => $sessionId,
            'access_token' => $plainAccessToken,
            'refresh_token' => $plainRefresh,
            'expires_at' => now()->addDays($refreshTtlDays),
        ];
    }
}
