<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Đăng ký user mới trong org
 * Chỉ Owner/Manager có quyền sử dụng
 */
class RegisterUserController extends Controller
{
    public function __invoke(Request $request)
    {
        $actor = $request->user();

        // Kiểm tra quyền: chỉ Owner/Manager mới được tạo user
        if (!in_array($actor->role, ['OWNER', 'MANAGER'])) {
            return response()->json([
                'message' => 'Chỉ Owner hoặc Manager mới có quyền đăng ký người dùng mới.'
            ], 403);
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['MANAGER', 'STAFF', 'TENANT'])],
        ]);

        // Validation: Phải có ít nhất email hoặc phone
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json([
                'message' => 'Vui lòng cung cấp ít nhất email hoặc số điện thoại.'
            ], 422);
        }

        // Kiểm tra email/phone đã tồn tại trong org chưa
        if (!empty($data['email'])) {
            $existingEmail = User::where('org_id', $actor->org_id)
                ->where('email', $data['email'])
                ->first();
            if ($existingEmail) {
                return response()->json([
                    'message' => 'Email đã được sử dụng trong tổ chức này.'
                ], 422);
            }
        }

        if (!empty($data['phone'])) {
            $existingPhone = User::where('org_id', $actor->org_id)
                ->where('phone', $data['phone'])
                ->first();
            if ($existingPhone) {
                return response()->json([
                    'message' => 'Số điện thoại đã được sử dụng trong tổ chức này.'
                ], 422);
            }
        }

        // Owner không thể tạo Owner khác
        if ($data['role'] === 'OWNER') {
            return response()->json([
                'message' => 'Không thể tạo Owner mới thông qua API này.'
            ], 403);
        }

        return DB::transaction(function () use ($data, $request, $actor) {
            $user = User::create([
                'id' => (string) Str::uuid(),
                'org_id' => $actor->org_id,
                'role' => $data['role'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'is_active' => true,
                'failed_login_count' => 0,
                'invited_by_user_id' => $actor->id,
            ]);

            // Gán role tương ứng
            $roleName = match ($data['role']) {
                'MANAGER' => 'Manager',
                'STAFF' => 'Staff',
                'TENANT' => 'Tenant',
                default => 'Staff',
            };
            $user->syncRoles([$roleName]);

            AuditLogger::log($actor->org_id, $actor->id, 'USER_CREATED', 'users', $user->id, [
                'created_user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                ],
            ], $request);

            return response()->json([
                'message' => 'Tạo người dùng thành công.',
                'user' => $user,
            ], 201);
        });
    }
}
