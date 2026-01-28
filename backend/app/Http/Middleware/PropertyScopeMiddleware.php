<?php

namespace App\Http\Middleware;

use App\Services\PropertyAccess\PropertyAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyScopeMiddleware
{
    /**
     * Handle an incoming request.
     * Auto-inject property scope vào request để các query có thể sử dụng
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Lấy danh sách property_ids mà user được phép truy cập
        $propertyIds = app(PropertyAccessService::class)->allowedPropertyIds($user);

        // Inject vào request để controller/model sử dụng
        $request->merge([
            '_scoped_property_ids' => $propertyIds,
        ]);

        // Lưu vào config runtime cho global scope
        config([
            'app.scoped_property_ids' => $propertyIds,
            'app.scoped_user_id' => $user->id,
            'app.scoped_org_id' => $user->org_id,
        ]);

        return $next($request);
    }
}
