<?php

namespace App\Services;

use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generate(int $length = 6): string
    {
        return str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
    }

    public function ttlSeconds(): int
    {
        return (int) env('OTP_TTL_SECONDS', 300);
    }

    public function hash(string $otp): string
    {
        return Hash::make($otp);
    }

    public function check(string $otp, string $hash): bool
    {
        return Hash::check($otp, $hash);
    }

    // GỬI OTP QUA EMAIL
    public function send(string $email, string $otp): void
    {
        $ttl = $this->ttlSeconds();
        Mail::to($email)->send(new LoginOtpMail($otp, $ttl));
    }
}
