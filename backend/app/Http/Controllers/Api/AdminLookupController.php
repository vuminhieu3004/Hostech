<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLookupController extends Controller
{
    public function properties(Request $request)
    {
        $u = $request->user();

        return DB::table('properties')
            ->where('org_id', $u->org_id)
            ->whereNull('deleted_at')
            ->select('id', 'code', 'name', 'address')
            ->orderBy('name')
            ->get();
    }

    public function staff(Request $request)
    {
        $u = $request->user();

        return DB::table('users')
            ->where('org_id', $u->org_id)
            ->whereIn('role', ['STAFF', 'MANAGER'])
            ->select('id', 'full_name', 'email', 'phone', 'role', 'is_active')
            ->orderBy('role')
            ->orderBy('full_name')
            ->get();
    }
}
