<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyUserRole;
use App\Models\User;
use App\Services\PropertyAccess\PropertyAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyStaffController extends Controller
{
    // GET /properties/{property}/staff
    public function index(Request $request, string $property)
    {
        $actor = $request->user();
        $this->assertPropertyInOrg($actor->org_id, $property);

        $rows = DB::table('property_user_roles')
            ->join('users', 'users.id', '=', 'property_user_roles.user_id')
            ->where('property_user_roles.org_id', $actor->org_id)
            ->where('property_user_roles.property_id', $property)
            ->where('property_user_roles.is_active', true)
            ->select(
                'property_user_roles.user_id',
                'property_user_roles.role',
                'property_user_roles.permissions',
                'users.full_name',
                'users.email',
                'users.phone',
                'users.role as user_role'
            )
            ->orderBy('users.full_name')
            ->get();

        return response()->json($rows);
    }

    // POST /properties/{property}/staff/{staff}  (assign/upsert)
    public function assign(Request $request, string $property, User $staff)
    {
        $actor = $request->user();
        $this->assertPropertyInOrg($actor->org_id, $property);

        // không cho cross-org
        if ($staff->org_id !== $actor->org_id) {
            return response()->json(['message' => 'Cross-org forbidden'], 403);
        }

        // chỉ gán STAFF/MANAGER cho property 
        if (!in_array($staff->role, ['STAFF', 'MANAGER'], true)) {
            return response()->json(['message' => 'Target user must be STAFF/MANAGER'], 422);
        }

        $data = $request->validate([
            // role theo property phải nằm trong enum bảng property_user_roles
            'role' => ['nullable', 'in:MANAGER,STAFF'],
            'permissions' => ['nullable', 'array'], // optional override sau này
        ]);

        $row = PropertyUserRole::updateOrCreate(
            [
                'org_id' => $actor->org_id,
                'property_id' => $property,
                'user_id' => $staff->id,
            ],
            [
                'role' => $data['role'] ?? $staff->role, // STAFF/MANAGER
                'is_active' => true,
                'permissions' => $data['permissions'] ?? null,
                'created_at' => now(),
            ]
        );

        app(PropertyAccessService::class)->clearUser($staff);

        return response()->json(['ok' => true, 'assignment' => $row]);
    }

    // DELETE /properties/{property}/staff/{staff}  (revoke)
    public function revoke(Request $request, string $property, User $staff)
    {
        $actor = $request->user();
        $this->assertPropertyInOrg($actor->org_id, $property);

        if ($staff->org_id !== $actor->org_id) {
            return response()->json(['message' => 'Cross-org forbidden'], 403);
        }

        PropertyUserRole::query()
            ->where('org_id', $actor->org_id)
            ->where('property_id', $property)
            ->where('user_id', $staff->id)
            ->update(['is_active' => false]);

        app(PropertyAccessService::class)->clearUser($staff);

        return response()->json(['ok' => true]);
    }

    private function assertPropertyInOrg(string $orgId, string $propertyId): void
    {
        $exists = DB::table('properties')
            ->where('org_id', $orgId)
            ->where('id', $propertyId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) abort(404, 'Property not found');
    }
}
