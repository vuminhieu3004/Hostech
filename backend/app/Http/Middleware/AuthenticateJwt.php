<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use App\Models\User;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token không được cung cấp.'], 401);
        }

        try {
            /** @var JwtService $jwtSvc */
            $jwtSvc = resolve(JwtService::class);

            // Verify và decode token
            $decoded = $jwtSvc->verifyToken($token);

            // Kiểm tra token đã hết hạn chưa
            if (($decoded->exp ?? 0) < time()) {
                return response()->json(['message' => 'Token đã hết hạn.'], 401);
            }

            // Lấy user từ token payload (không cần query DB)
            $userFromToken = $decoded->user ?? null;

            if (!$userFromToken) {
                return response()->json(['message' => 'Token không chứa thông tin user.'], 401);
            }

            // Optional: Verify session vẫn còn active
            $sessionId = $decoded->session_id ?? null;
            if ($sessionId) {
                $session = UserSession::find($sessionId);

                if (!$session || $session->status !== 'ACTIVE' || $session->revoked_at) {
                    return response()->json(['message' => 'Session đã bị thu hồi hoặc không hợp lệ.'], 401);
                }

                // Cập nhật last_seen_at
                $session->last_seen_at = now();
                $session->save();
            }

            // Inject user vào request (dạng object từ token)
            // Frontend có thể lấy trực tiếp từ token hoặc từ response này
            $request->merge([
                'auth_user' => $userFromToken,
                'auth_user_id' => $userFromToken->id,
                'auth_org_id' => $userFromToken->org_id,
                'auth_session_id' => $sessionId,
            ]);

            // Set auth user để dùng auth()->user() (nếu cần)
            // Tạo User model instance từ data trong token (không load từ DB)
            $userModel = new User((array) $userFromToken);
            $userModel->exists = true;
            $request->setUserResolver(fn() => $userModel);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token không hợp lệ.',
                'error' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
