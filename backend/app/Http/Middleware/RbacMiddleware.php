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
        $raw = $request->route($propertyParam);

        if (is_object($raw) && method_exists($raw, 'getKey')) {
            $propertyId = (string) $raw->getKey();
        } elseif (is_string($raw)) {
            $propertyId = $raw;
        }

        if (!$propertyId && $request->has('property_id')) {
            $propertyId = (string) $request->input('property_id');
        }

        app(PolicyEngine::class)->authorize($user, $permission, $propertyId);

        return $next($request);
    }
}
