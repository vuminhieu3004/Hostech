<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\InviteController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\AdminLookupController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\PropertyStaffController;
use App\Http\Controllers\Api\Auth\OtpVerifyController;
use App\Http\Controllers\Api\Auth\OtpRequestController;
use App\Http\Controllers\Api\Auth\OtpSettingsController;
use App\Http\Controllers\Api\Auth\AcceptInviteController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\LoginPasswordController;
use App\Http\Controllers\Api\Auth\RegisterOwnerController;

Route::prefix('auth')->group(function () {

    // Đăng ký owner + org (public)
    Route::post('register', RegisterOwnerController::class);

    // Đăng ký user mới (chỉ Owner/Manager)
    Route::post('register-user', RegisterUserController::class)
        ->middleware(['jwt.auth', 'rbac:users.create']);

    // Step 1: login bằng password (tất cả role) -> tạo session PENDING_OTP + gửi OTP
    Route::post('login', LoginPasswordController::class);

    // Step 1.1 (optional): gửi lại OTP theo session_id (resend)
    Route::post('otp/request', OtpRequestController::class)->middleware('throttle:otp');

    // Step 2: verify OTP -> cấp access_token + refresh_token
    Route::post('otp/verify', OtpVerifyController::class)->middleware('throttle:otp-verify');

    // refresh token (chỉ cho session ACTIVE)
    Route::post('refresh', RefreshController::class);

    // Routes yêu cầu xác thực JWT
    Route::middleware('jwt.auth')->group(function () {
        Route::get('me', MeController::class);
        Route::post('logout', LogoutController::class);

        // OTP Settings - User tự quản lý cài đặt OTP của mình
        Route::get('otp-settings', [OtpSettingsController::class, 'show']);
        Route::put('otp-settings', [OtpSettingsController::class, 'update']);
    });
});



// Tính năng mời người dùng vào org
Route::prefix('invites')->group(function () {
    // Xác thực mã mời (public)
    Route::get('verify/{code}', [AcceptInviteController::class, 'verify']);

    // Chấp nhận lời mời và tạo tài khoản
    Route::post('accept', [AcceptInviteController::class, 'accept']);
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('rbac:audit_logs.view');

    // Quản lý lời mời (chỉ owner/manager)
    Route::prefix('invites')->group(function () {
        Route::get('/', [InviteController::class, 'index']); // Danh sách lời mời
        Route::post('/', [InviteController::class, 'store']); // Tạo lời mời
        Route::get('/{invite}', [InviteController::class, 'show']); // Xem chi tiết lời mời
        Route::delete('/{invite}', [InviteController::class, 'destroy']); // Hủy lời mời
        Route::post('/{invite}/resend', [InviteController::class, 'resend']); // Gửi lại lời mời
    });


    //Trả về danh sách tất cả nhà/khu (properties) thuộc org của người đang đăng nhập (owner/manager).
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


    // Các route quản lý bất động sản
    Route::middleware('property-scope')->group(function () {
        // Danh sách bất động sản thuộc org của user đăng nhập
        Route::get('/properties', [PropertyController::class, 'index'])
            ->middleware('rbac:properties.view');
        // Xem chi tiết một bất động sản
        Route::get('/properties/{property}', [PropertyController::class, 'show'])
            ->middleware('rbac:properties.view');
        // Tạo bất động sản mới (Chỉ dành cho chủ sở hữu/quản lý)
        Route::post('/properties', [PropertyController::class, 'store'])
            ->middleware('rbac:properties.create');
        // Cập nhật thông tin bất động sản (Chỉ dành cho chủ sở hữu/quản lý)
        Route::put('/properties/{property}', [PropertyController::class, 'update'])
            ->middleware('rbac:properties.update');
        // Xóa bất động sản (Chỉ dành cho chủ sở hữu)
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])
            ->middleware('rbac:properties.delete');



        // Quản lý phòng
        //danh sách phòng
        Route::get('/properties/{property}/rooms', [RoomController::class, 'index'])
            ->middleware('rbac:rooms.view');
        //tạo phòng
        Route::post('/properties/{property}/rooms', [RoomController::class, 'store'])
            ->middleware('rbac:rooms.create');
        //xem chi tiết phòng
        Route::get('/rooms/{room}', [RoomController::class, 'show'])
            ->middleware('rbac:rooms.view');
        //cập nhật phòng
        Route::put('/rooms/{room}', [RoomController::class, 'update'])
            ->middleware('rbac:rooms.update');
        //xóa phòng
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
            ->middleware('rbac:rooms.delete');
    });
});
