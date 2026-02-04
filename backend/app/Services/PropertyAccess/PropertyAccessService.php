<?php

namespace App\Services\PropertyAccess;

use App\Models\PropertyUserRole;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PropertyAccessService
{
    public function allowedPropertyIds(User $user): array
    {
        // Owner: thấy toàn bộ property của org
        if ($user->hasRole('Owner')) {
            return Cache::remember($this->key($user), 600, function () use ($user) {
                return DB::table('properties')
                    ->where('org_id', $user->org_id)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->map(fn($id) => (string)$id)
                    ->all();
            });
        }

        // Manager/Staff: chỉ các property được assign
        return Cache::remember($this->key($user), 600, function () use ($user) {
            return PropertyUserRole::query()
                ->where('org_id', $user->org_id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->pluck('property_id')
                ->map(fn($id) => (string)$id)
                ->unique()->values()->all();
        });
    }

    public function clearUser(User|string $user): void
    {
        $userId = is_string($user) ? $user : $user->id;
        Cache::forget("props:allowed:{$userId}");
    }

    private function key(User $user): string
    {
        return "props:allowed:{$user->id}";
    }
}
