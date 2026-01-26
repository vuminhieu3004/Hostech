<?php

namespace App\Services\Rbac;

use App\Models\User;
use App\Models\PropertyUserRole;
use Illuminate\Auth\Access\AuthorizationException;

class PolicyEngine
{
    public function authorize(User $user, string $permission, ?string $propertyId = null): void
    {
        if (!$this->can($user, $permission, $propertyId)) {
            throw new AuthorizationException('FORBIDDEN');
        }
    }

    public function can(User $user, string $permission, ?string $propertyId = null): bool
    {
        // 0) permission phải có trong config (chống typo)
        if (!in_array($permission, config('rbac.permissions', []), true)) {
            return false;
        }

        // 1) check permission theo Spatie
        if (!$user->can($permission)) {
            return false;
        }

        // 2) resolve scope
        $scope = $this->resolveScope($permission);

        // org-scope: chỉ cần RBAC
        if ($scope === 'org') {
            return true;
        }

        // property-scope: bắt buộc có propertyId
        if (!$propertyId) {
            return false;
        }

        // Owner: full scope trong org
        if ($user->hasRole('Owner')) {
            return true;
        }

        // còn lại: phải có assignment trong property_user_roles
        return PropertyUserRole::query()
            ->where('org_id', $user->org_id)
            ->where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->where('is_active', true)
            ->exists();
    }

    private function resolveScope(string $permission): string
    {
        $rules = config('rbac.scopes', []);

        if (isset($rules[$permission])) return $rules[$permission];

        [$module] = explode('.', $permission, 2);
        $wild = $module . '.*';
        if (isset($rules[$wild])) return $rules[$wild];

        // an toàn: mặc định property
        return 'property';
    }
}
