<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            $phone = (string) $request->input('phone');
            return Limit::perMinute(3)->by($request->ip() . '|' . $phone);
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            $phone = (string) $request->input('phone');
            return Limit::perMinute(10)->by($request->ip() . '|' . $phone);
        });
    }
}
