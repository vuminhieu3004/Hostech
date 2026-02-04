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

            // Inject user vào request (convert token object to array to avoid InputBag errors)
            $authUserValue = is_object($userFromToken) ? (array) $userFromToken : $userFromToken;

            $request->merge([
                'auth_user' => $authUserValue,
                'auth_user_id' => $userFromToken->id ?? null,
                'auth_org_id' => $userFromToken->org_id ?? null,
                'auth_session_id' => $sessionId,
            ]);

            // Tạo User model instance từ data trong token (không load từ DB)
            $userArray = (array) $userFromToken;
            $userId = $userArray['id'] ?? null;
            unset($userArray['id']); // Remove id from attributes

            $userModel = new User($userArray);
            $userModel->id = $userId; // Set primary key explicitly
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
