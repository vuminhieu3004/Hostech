<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogger
{
    public static function log(?string $orgId, ?string $actorUserId, string $action, string $entityType, string $entityId, array $meta = [], ?Request $request = null): void
    {
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'org_id' => $orgId,
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip' => $request?->ip(),
            'device_id' => $meta['device_id'] ?? null,
            'user_agent' => $request?->userAgent(),
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
