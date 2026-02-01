<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Lấy thông tin user từ JWT token
     */
    public function __invoke(Request $request)
    {
        // Lấy user từ request
        $user = $request->input('auth_user');

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy thông tin user trong token.'], 401);
        }

        return response()->json([
            'user' => $user,
            'session_id' => $request->input('auth_session_id'),
        ]);
    }
}
