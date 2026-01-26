<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // resend OTP
        RateLimiter::for('otp', function (Request $request) {
            // nếu bạn dùng session_id gửi ngầm:
            $sessionId = (string) $request->input('session_id', '');

            // nếu bạn verify theo login:
            $login = (string) $request->input('login', '');

            // ưu tiên session_id, fallback login
            $key = $sessionId !== '' ? $sessionId : $login;

            return Limit::perMinute(3)->by($request->ip() . '|' . $key);
        });

        // verify OTP
        RateLimiter::for('otp-verify', function (Request $request) {
            $sessionId = (string) $request->input('session_id', '');
            $login = (string) $request->input('login', '');

            $key = $sessionId !== '' ? $sessionId : $login;

            return Limit::perMinute(10)->by($request->ip() . '|' . $key);
        });
    }
}
