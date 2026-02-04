<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\AuthSessionService;
use Illuminate\Http\Request;

class OtpRequestController extends Controller
{
    public function __invoke(Request $request, AuthSessionService $auth)
    {
        $data = $request->validate([
            'org_id' => ['required', 'uuid'],
            'session_id' => ['required', 'uuid'],
        ]);

        $res = $auth->resendOtp($data['org_id'], $data['session_id']);

        AuditLogger::log($data['org_id'], null, 'AUTH_OTP_RESEND', 'user_sessions', $data['session_id'], [
            'session_id' => $data['session_id'],
        ], $request);

        return response()->json([
            'message' => 'Đã gửi lại OTP.',
            'session_id' => $res['session_id'],
            'otp_ttl' => $res['otp_ttl'],
            'dev_otp' => $res['dev_otp'] ?? null,
        ]);
    }
}
