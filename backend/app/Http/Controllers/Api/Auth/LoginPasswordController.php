<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\AuthSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginPasswordController extends Controller
{
    public function __invoke(Request $request, AuthSessionService $auth)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],

            'device_id' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_platform' => ['nullable', 'string', 'max:50'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
        ]);

        $login = trim($data['login']);

        // 1) Tìm user theo login (email/phone) trên toàn hệ thống
        $candidates = User::query()
            ->with(['org:id,name']) // cần relationship user->org
            ->where('email', $login)
            ->orWhere('phone', $login)
            ->get();

        // Không tìm thấy: không có org_id để ghi audit DB => ghi log file + trả lỗi
        if ($candidates->isEmpty()) {
            logger()->warning('AUTH_LOGIN_PASSWORD_USER_NOT_FOUND', [
                'login' => $login,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Sai thông tin đăng nhập.'], 422);
        }

        // 2) Nếu 1 login thuộc nhiều org => trả danh sách org để user chọn
        $orgs = $candidates
            ->map(fn($u) => [
                'org_id' => $u->org_id,
                'org_name' => $u->org?->name,
            ])
            ->unique('org_id')
            ->values();

        if ($orgs->count() > 1) {
            return response()->json([
                'message' => 'Tài khoản thuộc nhiều tổ chức. Vui lòng chọn tổ chức để đăng nhập.',
                'orgs' => $orgs,
            ], 409);
        }

        // 3) Chỉ còn đúng 1 user
        /** @var User $user */
        $user = $candidates->first();
        $orgId = $user->org_id;

        // Audit attempt (kể cả fail) - giờ đã có org_id
        AuditLogger::log($orgId, $user->id, 'AUTH_LOGIN_PASSWORD_ATTEMPT', 'users', $user->id, [
            'login' => $login,
            'ip' => $request->ip(),
            'device_id' => $data['device_id'] ?? null,
        ], $request);

        // blocked vĩnh viễn
        if (!$user->is_active) {
            AuditLogger::log($orgId, $user->id, 'AUTH_LOGIN_BLOCKED', 'users', $user->id, [], $request);
            return response()->json(['message' => 'Tài khoản đã bị khoá.'], 403);
        }

        // lock tạm do sai nhiều lần
        if ($user->locked_until && now()->lt($user->locked_until)) {
            return response()->json([
                'message' => 'Tài khoản đang bị khoá tạm do đăng nhập sai nhiều lần.',
                'locked_until' => $user->locked_until,
            ], 423);
        }

        // check password
        if (!$user->password_hash || !Hash::check($data['password'], $user->password_hash)) {
            $max = (int) env('LOGIN_MAX_ATTEMPTS', 5);

            $user->failed_login_count = (int) $user->failed_login_count + 1;

            if ($user->failed_login_count >= $max) {
                $user->locked_until = now()->addMinutes((int) env('LOGIN_LOCK_MINUTES', 15));
                $user->failed_login_count = 0; // reset sau khi lock
            }

            $user->save();

            AuditLogger::log($orgId, $user->id, 'AUTH_LOGIN_PASSWORD_FAILED', 'users', $user->id, [
                'failed_login_count' => $user->failed_login_count,
                'locked_until' => $user->locked_until,
            ], $request);

            return response()->json(['message' => 'Sai thông tin đăng nhập.'], 422);
        }

        // password OK => reset counters
        $user->failed_login_count = 0;
        $user->locked_until = null;
        $user->save();

        // 4) Start OTP challenge (gửi OTP qua email theo service của bạn)
        $challenge = $auth->startOtpChallenge($user, [
            'device_id' => $data['device_id'] ?? null,
            'device_name' => $data['device_name'] ?? 'web',
            'device_platform' => $data['device_platform'] ?? 'Web',
            'device_fingerprint' => $data['device_fingerprint'] ?? null,
        ], $request->ip(), $request->userAgent());

        AuditLogger::log($orgId, $user->id, 'AUTH_LOGIN_PASSWORD_OK_OTP_SENT', 'user_sessions', $challenge['session_id'], [
            'session_id' => $challenge['session_id'],
        ], $request);

        return response()->json([
            'message' => 'Mật khẩu đúng. OTP đã được gửi, vui lòng xác nhận để hoàn tất đăng nhập.',
            'org_id' => $orgId, // tiện cho FE giữ context
            'session_id' => $challenge['session_id'],
            'otp_ttl' => $challenge['otp_ttl'],
            'dev_otp' => $challenge['dev_otp'] ?? null, // chỉ local
        ]);
    }
}
