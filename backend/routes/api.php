<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AdminLookupController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\PropertyStaffController;
use App\Http\Controllers\Api\Auth\OtpVerifyController;
use App\Http\Controllers\Api\Auth\OtpRequestController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\LoginPasswordController;
use App\Http\Controllers\Api\Auth\RegisterOwnerController;

Route::prefix('auth')->group(function () {

    // Đăng ký owner + org (public)
    Route::post('register', RegisterOwnerController::class);

    // Đăng ký user mới (chỉ Owner/Manager)
    Route::post('register-user', RegisterUserController::class)
        ->middleware(['auth:sanctum', 'rbac:users.create']);

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

    //Trả về danh sách tất cả nhà/khu (properties) thuộc org của người đang đăng nhập (Owner/Manager).
    Route::get('/admin/properties', [AdminLookupController::class, 'properties'])
        ->middleware('rbac:properties.staff.view');
    //Trả về danh sách user trong org có role STAFF/MANAGER để chọn người cần phân công.
    Route::get('/admin/staff', [AdminLookupController::class, 'staff'])
        ->middleware('rbac:properties.staff.view');

    //Lấy danh sách ai đang được gán vào property này
    Route::get('/properties/{property}/staff', [PropertyStaffController::class, 'index'])
        ->middleware('rbac:properties.staff.view');
    //Gán (hoặc cập nhật) một staff/manager vào property.
    Route::post('/properties/{property}/staff/{staff}', [PropertyStaffController::class, 'assign'])
        ->middleware('rbac:properties.staff.assign');
    //Thu hồi quyền vào property của staff/manager đó.
    Route::delete('/properties/{property}/staff/{staff}', [PropertyStaffController::class, 'revoke'])
        ->middleware('rbac:properties.staff.revoke');


    //test thử phân chia
    // Route::get('/properties/{property}/rooms', [RoomController::class, 'index'])
    //     ->middleware('rbac:rooms.view,property');
});
