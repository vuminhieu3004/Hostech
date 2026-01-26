<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Multi-tenant: bắt buộc lọc org_id
        $logs = AuditLog::query()
            ->where('org_id', $user->org_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($logs);
    }
}
