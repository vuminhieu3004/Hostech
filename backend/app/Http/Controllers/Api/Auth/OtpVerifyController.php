<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Services\AuditLogger;
use App\Services\AuthSessionService;
use Illuminate\Http\Request;

class OtpVerifyController extends Controller
{
    public function __invoke(Request $request, AuthSessionService $auth)
    {
        $data = $request->validate([
            'session_id' => ['required', 'uuid'],
            'otp' => ['required', 'string', 'min:4', 'max:10'],

            // optional: nếu muốn đối chiếu email/phone
            'login' => ['nullable', 'string'],
        ]);

        // Lấy session -> suy ra org_id và user_id
        $session = UserSession::query()
            ->with(['user:id,org_id,email,phone,is_active']) // cần relation user() trong UserSession
            ->where('id', $data['session_id'])
            ->firstOrFail();

        // Nếu client gửi login (email/phone), đối chiếu để chắc session thuộc đúng user
        if (!empty($data['login'])) {
            $login = trim($data['login']);
            $match = ($session->user?->email === $login) || ($session->user?->phone === $login);

            if (!$match) {
                return response()->json(['message' => 'Session không khớp tài khoản.'], 422);
            }
        }

        // Gọi service bằng org_id lấy từ session
        $tokens = $auth->verifyOtpAndCreateSession(
            $session->org_id,
            $session->id,
            $data['otp']
        );

        AuditLogger::log($session->org_id, $session->user_id, 'AUTH_OTP_VERIFY_SUCCESS', 'user_sessions', $session->id, [
            'session_id' => $tokens['session_id'],
        ], $request);

        return response()->json([
            'session_id' => $tokens['session_id'],
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'refresh_expires_at' => $tokens['expires_at'],
        ]);
    }
}
