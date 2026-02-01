<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'session_id' => ['nullable', 'uuid'],
        ]);

        // revoke access token hiện tại
        $request->user()->currentAccessToken()?->delete();

        if (!empty($data['session_id'])) {
            UserSession::where('id', $data['session_id'])
                ->where('user_id', $user->id)
                ->update(['revoked_at' => now(), 'refresh_token_hash' => null]);
        }

        AuditLogger::log($user->org_id, $user->id, 'AUTH_LOGOUT', 'users', $user->id, [
            'session_id' => $data['session_id'] ?? null,
        ], $request);

        return response()->json(['message' => 'Đã đăng xuất.']);
    }
}
