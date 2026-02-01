<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Services\AuditLogger;
use App\Services\InviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Public endpoint để accept invite (không cần auth)
 */
class AcceptInviteController extends Controller
{
    public function __construct(protected InviteService $inviteService) {}

    /**
     * GET /invites/verify/{code} - Verify invite code (public)
     */
    public function verify(Request $request, string $code)
    {
        $data = $request->validate([
            'org_id' => ['required', 'uuid'],
        ]);

        $invite = $this->inviteService->validate($code, $data['org_id']);

        if (!$invite) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Mã mời không hợp lệ hoặc đã hết hạn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'valid' => true,
            'data' => [
                'code' => $invite->code,
                'target_role' => $invite->target_role,
                'target_email' => $invite->target_email,
                'target_phone' => $invite->target_phone,
                'expires_at' => $invite->expires_at->toIso8601String(),
                'property_name' => $invite->property?->name,
                'message' => $invite->meta['message'] ?? null,
            ]
        ]);
    }

    /**
     * POST /invites/accept - Accept invite and create account (public)
     */
    public function accept(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'org_id' => ['required', 'uuid'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Validate invite
        $invite = $this->inviteService->validate($data['code'], $data['org_id']);

        if (!$invite) {
            return response()->json([
                'success' => false,
                'message' => 'Mã mời không hợp lệ hoặc đã hết hạn'
            ], 422);
        }

        // Verify email/phone matches invite (if provided in invite)
        if ($invite->target_email && $invite->target_email !== $data['email']) {
            return response()->json([
                'success' => false,
                'message' => 'Email không khớp với lời mời'
            ], 422);
        }

        if ($invite->target_phone && $invite->target_phone !== $data['phone']) {
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại không khớp với lời mời'
            ], 422);
        }

        // Must provide email or phone
        $finalEmail = $invite->target_email ?? $data['email'] ?? null;
        $finalPhone = $invite->target_phone ?? $data['phone'] ?? null;

        if (!$finalEmail && !$finalPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp email hoặc số điện thoại'
            ], 422);
        }

        // Check if user already exists
        if ($finalEmail) {
            $existingUser = \App\Models\User::where('org_id', $invite->org_id)
                ->where('email', $finalEmail)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email đã được sử dụng trong tổ chức này'
                ], 422);
            }
        }

        if ($finalPhone) {
            $existingUser = \App\Models\User::where('org_id', $invite->org_id)
                ->where('phone', $finalPhone)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại đã được sử dụng trong tổ chức này'
                ], 422);
            }
        }

        return DB::transaction(function () use ($data, $invite, $request) {
            // Accept invite and create user
            $user = $this->inviteService->accept($invite, [
                'full_name' => $data['full_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password_hash' => Hash::make($data['password']),
            ]);

            AuditLogger::log(
                $invite->org_id,
                $user->id,
                'INVITE_ACCEPTED',
                'invites',
                $invite->id,
                [
                    'user_id' => $user->id,
                    'invite_code' => $invite->code,
                ],
                $request
            );

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ], 201);
        });
    }
}
