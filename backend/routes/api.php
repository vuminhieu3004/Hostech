<?php

use App\Http\Controllers\Api\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterOwnerController;
use App\Http\Controllers\Api\Auth\LoginPasswordController;
use App\Http\Controllers\Api\Auth\OtpRequestController;
use App\Http\Controllers\Api\Auth\OtpVerifyController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\Auth\LogoutController;

Route::prefix('auth')->group(function () {

    // Đăng ký owner + org (sau register sẽ gửi OTP để xác nhận đăng nhập)
    Route::post('register', RegisterOwnerController::class);

    // Step 1: login bằng password (tất cả role) -> tạo session PENDING_OTP + gửi OTP
    Route::post('login', LoginPasswordController::class);

    // Step 1.1 (optional): gửi lại OTP theo session_id (resend)
    Route::post('otp/request', OtpRequestController::class)->middleware('throttle:otp');

    // Step 2: verify OTP -> cấp access_token + refresh_token
    Route::post('otp/verify', OtpVerifyController::class)->middleware('throttle:otp-verify');

    // refresh token (chỉ cho session ACTIVE)
    Route::post('refresh', RefreshController::class);

    // các route cần access_token
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', fn($r) => $r->user());
        Route::post('logout', LogoutController::class);
    });
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('rbac:audit_logs.view');
});
