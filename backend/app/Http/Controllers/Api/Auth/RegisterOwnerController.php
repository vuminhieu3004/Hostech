<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\AuthSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterOwnerController extends Controller
{
    public function __invoke(Request $request, AuthSessionService $auth)
    {
        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],

            'device_id' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_platform' => ['nullable', 'string', 'max:50'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
        ]);
        // Validation: Phải có ít nhất email hoặc phone
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json([
                'message' => 'Vui lòng cung cấp ít nhất email hoặc số điện thoại.'
            ], 422);
        }

        // Kiểm tra email/phone đã tồn tại chưa
        if (!empty($data['email'])) {
            $existingEmail = User::where('email', $data['email'])->first();
            if ($existingEmail) {
                return response()->json([
                    'message' => 'Email đã được sử dụng.'
                ], 422);
            }
        }

        if (!empty($data['phone'])) {
            $existingPhone = User::where('phone', $data['phone'])->first();
            if ($existingPhone) {
                return response()->json([
                    'message' => 'Số điện thoại đã được sử dụng.'
                ], 422);
            }
        }
        // Validation: Phải có ít nhất email hoặc phone
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json([
                'message' => 'Vui lòng cung cấp ít nhất email hoặc số điện thoại.'
            ], 422);
        }

        // Kiểm tra email/phone đã tồn tại chưa
        if (!empty($data['email'])) {
            $existingEmail = User::where('email', $data['email'])->first();
            if ($existingEmail) {
                return response()->json([
                    'message' => 'Email đã được sử dụng.'
                ], 422);
            }
        }

        if (!empty($data['phone'])) {
            $existingPhone = User::where('phone', $data['phone'])->first();
            if ($existingPhone) {
                return response()->json([
                    'message' => 'Số điện thoại đã được sử dụng.'
                ], 422);
            }
        }

        return DB::transaction(function () use ($data, $request, $auth) {
            $org = Org::create([
                'id' => (string) Str::uuid(),
                'name' => $data['org_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'timezone' => 'Asia/Bangkok',
                'currency' => 'VND',
            ]);

            $user = User::create([
                'id' => (string) Str::uuid(),
                'org_id' => $org->id,
                'role' => 'OWNER',
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'is_active' => true,
                'failed_login_count' => 0,
            ]);

            $user->syncRoles(['Owner']);

            // Gửi OTP qua email hoặc SMS tùy theo thông tin user cung cấp
            $challenge = $auth->startOtpChallenge($user, [
                'device_id' => $data['device_id'] ?? null,
                'device_name' => $data['device_name'] ?? 'web',
                'device_platform' => $data['device_platform'] ?? 'Web',
                'device_fingerprint' => $data['device_fingerprint'] ?? null,
            ], $request->ip(), $request->userAgent());

            AuditLogger::log($org->id, $user->id, 'AUTH_REGISTER_OWNER', 'users', $user->id, [
                'email' => $user->email,
                'phone' => $user->phone,
            ], $request);

            AuditLogger::log($org->id, $user->id, 'AUTH_REGISTER_OWNER_OTP_SENT', 'user_sessions', $challenge['session_id'], [
                'session_id' => $challenge['session_id'],
                'otp_method' => $challenge['otp_method'] ?? 'email',
            ], $request);

            return response()->json([
                'message' => 'Đăng ký thành công. OTP đã được gửi để xác nhận đăng nhập.',
                'org' => $org,
                'user' => $user,
                'session_id' => $challenge['session_id'],
                'otp_ttl' => $challenge['otp_ttl'],
                'otp_method' => $challenge['otp_method'] ?? 'email',
                'dev_otp' => $challenge['dev_otp'] ?? null,
            ], 201);
        });
    }
}
