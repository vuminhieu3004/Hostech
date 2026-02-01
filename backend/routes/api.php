<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\PropertyController;
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
use App\Http\Controllers\Api\Auth\AcceptInviteController;
use App\Http\Controllers\Api\InviteController;

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

// Tính năng mời người dùng vào org
Route::prefix('invites')->group(function () {
    // Verify invite code (public)
    Route::get('verify/{code}', [AcceptInviteController::class, 'verify']);

    // Accept invite and create account
    Route::post('accept', [AcceptInviteController::class, 'accept']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('rbac:audit_logs.view');

    // Invite management (Owner/Manager only)
    Route::prefix('invites')->group(function () {
        Route::get('/', [InviteController::class, 'index']); // List invites
        Route::post('/', [InviteController::class, 'store']); // Create invite
        Route::get('/{invite}', [InviteController::class, 'show']); // Show invite
        Route::delete('/{invite}', [InviteController::class, 'destroy']); // Revoke invite
        Route::post('/{invite}/resend', [InviteController::class, 'resend']); // Resend invite
    });

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

    // Các route quản lý bất động sản với cơ chế tự động giới hạn phạm vi (scoping)
    Route::middleware('property-scope')->group(function () {
        // Danh sách bất động sản (tự động lọc theo các bất động sản được phép truy cập)
        Route::get('/properties', [PropertyController::class, 'index'])
            ->middleware('rbac:properties.view');

        // Xem chi tiết một bất động sản (tự động kiểm tra đối chiếu với danh sách được phép)
        Route::get('/properties/{property}', [PropertyController::class, 'show'])
            ->middleware('rbac:properties.view');

        // Tạo bất động sản mới (Chỉ dành cho Chủ sở hữu/quản lý)
        Route::post('/properties', [PropertyController::class, 'store'])
            ->middleware('rbac:properties.create');

        // Cập nhật thông tin bất động sản (Chỉ dành cho Chủ sở hữu/quản lý)
        Route::put('/properties/{property}', [PropertyController::class, 'update'])
            ->middleware('rbac:properties.update');

        // Xóa bất động sản (Chỉ dành cho Chủ sở hữu)
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])
            ->middleware('rbac:properties.delete');

        // Quản lý phòng (được giới hạn trong phạm vi các bất động sản được phép)
        Route::get('/properties/{property}/rooms', [RoomController::class, 'index'])
            ->middleware('rbac:rooms.view');

        Route::post('/properties/{property}/rooms', [RoomController::class, 'store'])
            ->middleware('rbac:rooms.create');

        Route::get('/rooms/{room}', [RoomController::class, 'show'])
            ->middleware('rbac:rooms.view');

        Route::put('/rooms/{room}', [RoomController::class, 'update'])
            ->middleware('rbac:rooms.update');

        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
            ->middleware('rbac:rooms.delete');
    });
});
