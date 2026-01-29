<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Services\AuditLogger;
use App\Services\InviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InviteController extends Controller
{
    public function __construct(protected InviteService $inviteService) {}

    /**
     * GET /invites - List all invites
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Invite::query()
            ->forOrg($user->org_id)
            ->with(['createdBy:id,full_name', 'acceptedBy:id,full_name', 'property:id,name']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by target_role
        if ($request->filled('target_role')) {
            $query->where('target_role', $request->input('target_role'));
        }

        // Filter by created_by
        if ($request->filled('created_by')) {
            $query->where('created_by_user_id', $request->input('created_by'));
        }

        // Search by email or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('target_email', 'like', "%{$search}%")
                    ->orWhere('target_phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $invites = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invites->items(),
            'meta' => [
                'current_page' => $invites->currentPage(),
                'last_page' => $invites->lastPage(),
                'per_page' => $invites->perPage(),
                'total' => $invites->total(),
            ]
        ]);
    }

    /**
     * POST /invites - Create new invite
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Only Owner/Manager can create invites
        if (!in_array($user->role, ['OWNER', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ Owner hoặc Manager mới có quyền tạo lời mời'
            ], 403);
        }

        $data = $request->validate([
            'target_role' => ['required', Rule::in(['MANAGER', 'STAFF', 'TENANT'])],
            'target_email' => ['nullable', 'email', 'max:255'],
            'target_phone' => ['nullable', 'string', 'max:30'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:100'],
            'property_id' => ['nullable', 'uuid', 'exists:properties,id'],
            'room_id' => ['nullable', 'uuid', 'exists:rooms,id'],
            'message' => ['nullable', 'string', 'max:500'],
            'send_email' => ['nullable', 'boolean'],
            'send_sms' => ['nullable', 'boolean'],
        ]);

        // Must have email or phone
        if (empty($data['target_email']) && empty($data['target_phone'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp email hoặc số điện thoại'
            ], 422);
        }

        // Validate property belongs to org
        if (!empty($data['property_id'])) {
            $propertyExists = DB::table('properties')
                ->where('id', $data['property_id'])
                ->where('org_id', $user->org_id)
                ->exists();

            if (!$propertyExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property không tồn tại hoặc không thuộc organization'
                ], 404);
            }
        }

        // Validate room belongs to org
        if (!empty($data['room_id'])) {
            $roomExists = DB::table('rooms')
                ->where('id', $data['room_id'])
                ->where('org_id', $user->org_id)
                ->exists();

            if (!$roomExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room không tồn tại hoặc không thuộc organization'
                ], 404);
            }
        }

        return DB::transaction(function () use ($data, $user, $request) {
            $invite = $this->inviteService->create([
                'org_id' => $user->org_id,
                'created_by_user_id' => $user->id,
                'target_role' => $data['target_role'],
                'target_email' => $data['target_email'] ?? null,
                'target_phone' => $data['target_phone'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'max_uses' => $data['max_uses'] ?? 1,
                'property_id' => $data['property_id'] ?? null,
                'room_id' => $data['room_id'] ?? null,
                'meta' => isset($data['message']) ? ['message' => $data['message']] : null,
            ]);

            // Send notifications
            if (!empty($data['send_email']) && $invite->target_email) {
                $this->inviteService->sendEmail($invite);
            }

            if (!empty($data['send_sms']) && $invite->target_phone) {
                $this->inviteService->sendSms($invite);
            }

            AuditLogger::log(
                $user->org_id,
                $user->id,
                'INVITE_CREATED',
                'invites',
                $invite->id,
                ['invite' => $invite->toArray()],
                $request
            );

            return response()->json([
                'success' => true,
                'data' => $invite->load(['property:id,name']),
                'message' => 'Tạo lời mời thành công'
            ], 201);
        });
    }

    /**
     * GET /invites/{invite} - Show invite detail
     */
    public function show(Request $request, Invite $invite)
    {
        $user = $request->user();

        if ($invite->org_id !== $user->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $invite->load(['createdBy:id,full_name', 'acceptedBy:id,full_name', 'property:id,name']);

        return response()->json([
            'success' => true,
            'data' => $invite
        ]);
    }

    /**
     * DELETE /invites/{invite} - Revoke invite
     */
    public function destroy(Request $request, Invite $invite)
    {
        $user = $request->user();

        if ($invite->org_id !== $user->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        if ($invite->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể thu hồi lời mời đang pending'
            ], 422);
        }

        $this->inviteService->revoke($invite);

        AuditLogger::log(
            $user->org_id,
            $user->id,
            'INVITE_REVOKED',
            'invites',
            $invite->id,
            ['invite_code' => $invite->code],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Thu hồi lời mời thành công'
        ]);
    }

    /**
     * POST /invites/{invite}/resend - Resend invite notification
     */
    public function resend(Request $request, Invite $invite)
    {
        $user = $request->user();

        if ($invite->org_id !== $user->org_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        if (!$invite->canBeUsed()) {
            return response()->json([
                'success' => false,
                'message' => 'Lời mời không còn hiệu lực'
            ], 422);
        }

        $data = $request->validate([
            'method' => ['required', Rule::in(['email', 'sms', 'both'])],
        ]);

        if (in_array($data['method'], ['email', 'both']) && $invite->target_email) {
            $this->inviteService->sendEmail($invite);
        }

        if (in_array($data['method'], ['sms', 'both']) && $invite->target_phone) {
            $this->inviteService->sendSms($invite);
        }

        AuditLogger::log(
            $user->org_id,
            $user->id,
            'INVITE_RESENT',
            'invites',
            $invite->id,
            ['method' => $data['method']],
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Gửi lại lời mời thành công'
        ]);
    }
}
