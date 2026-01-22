<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\AuthSessionService;
use Illuminate\Http\Request;

class RefreshController extends Controller
{
    public function __invoke(Request $request, AuthSessionService $auth)
    {
        $data = $request->validate([
            'session_id' => ['required', 'uuid'],
            'refresh_token' => ['required', 'string'],
        ]);

        $session = UserSession::find($data['session_id']);
        if (!$session) {
            return response()->json(['message' => 'Session không tồn tại.'], 404);
        }

        $user = User::find($session->user_id);
        if (!$user || !$user->is_active) {
            return response()->json(['message' => 'Tài khoản không hợp lệ.'], 403);
        }

        try {
            $tokens = $auth->refresh($session, $user, $data['refresh_token']);

            AuditLogger::log($user->org_id, $user->id, 'AUTH_REFRESH_SUCCESS', 'user_sessions', $session->id, [], $request);

            return response()->json([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
        } catch (\RuntimeException $e) {
            AuditLogger::log($session->org_id, $session->user_id, 'AUTH_REFRESH_FAILED', 'user_sessions', $session->id, [
                'reason' => $e->getMessage(),
            ], $request);

            return response()->json(['message' => 'Refresh token không hợp lệ/hết hạn.'], 401);
        }
    }
}
