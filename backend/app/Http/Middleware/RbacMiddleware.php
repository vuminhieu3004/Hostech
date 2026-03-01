<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Rbac\PolicyEngine;

class RbacMiddleware
{
    // Usage: ->middleware('rbac:rooms.update,property')
    public function handle(Request $request, Closure $next, string $permission, string $propertyParam = 'property'): mixed
    {
        $user = $request->user();

        $propertyId = null;

        // Try to get property from route parameter
        $raw = $request->route($propertyParam);

        if (is_object($raw) && method_exists($raw, 'getKey')) {
            $propertyId = (string) $raw->getKey();
        } elseif (is_string($raw)) {
            $propertyId = $raw;
        }

        // If not found, try to get from request body
        if (!$propertyId && $request->has('property_id')) {
            $propertyId = (string) $request->input('property_id');
        }

        // If still not found, try to infer from room model (for room-specific routes)
        if (!$propertyId) {
            $room = $request->route('room');
            if (is_object($room) && isset($room->property_id)) {
                $propertyId = (string) $room->property_id;
            }
        }

        app(PolicyEngine::class)->authorize($user, $permission, $propertyId);

        return $next($request);
    }
}
