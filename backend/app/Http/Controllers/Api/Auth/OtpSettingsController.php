<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

use function Psy\debug;

class OtpSettingsController extends Controller
{
    /**
     * Lấy cài đặt OTP hiện tại của user
     */
    public function show(Request $request)
    {
        $user = $request->user();
        // debug($user);
        return response()->json([
            'otp_required' => $user->otp_required ?? true,
        ]);
    }

    /**
     * Cập nhật cài đặt OTP cho user
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'otp_required' => ['required', 'boolean'],
        ]);

        $oldValue = $user->otp_required ?? true;
        $user->otp_required = $data['otp_required'];
        $user->save();
        // debug($user);

        AuditLogger::log(
            $user->org_id,
            $user->id,
            'USER_OTP_SETTINGS_UPDATED',
            'users',
            $user->id,
            [
                'old_value' => $oldValue,
                'new_value' => $data['otp_required'],
            ],
            $request
        );

        return response()->json([
            'message' => $data['otp_required']
                ? 'OTP đã được bật cho tài khoản của bạn.'
                : 'OTP đã được tắt cho tài khoản của bạn.',
            'otp_required' => $user->otp_required,
        ]);
    }
}
