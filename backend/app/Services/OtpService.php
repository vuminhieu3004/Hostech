<?php

namespace App\Services;

use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

    /**
     * Gửi OTP qua Email
     */
    public function sendEmail(string $email, string $otp): void
    {
        $ttl = $this->ttlSeconds();
        Mail::to($email)->send(new LoginOtpMail($otp, $ttl));
    }

    /**
     * Gửi OTP qua SMS
     * TODO: Tích hợp SMS Gateway (Twilio, AWS SNS, Esms.vn, etc.)
     */
    public function sendSms(string $phone, string $otp): void
    {
        $ttl = $this->ttlSeconds();
        $minutes = ceil($ttl / 60);

        // Trong môi trường local/dev, chỉ log
        if (app()->environment('local', 'development')) {
            Log::info("SMS OTP", [
                'phone' => $phone,
                'otp' => $otp,
                'message' => "Ma OTP cua ban la: {$otp}. Ma co hieu luc trong {$minutes} phut.",
            ]);
            return;
        }

        // TODO: Tích hợp SMS Gateway thực tế
        // Ví dụ với Twilio:
        // $twilio = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        // $twilio->messages->create($phone, [
        //     'from' => env('TWILIO_PHONE'),
        //     'body' => "Ma OTP cua ban la: {$otp}. Ma co hieu luc trong {$minutes} phut."
        // ]);

        // Ví dụ với Esms.vn (Vietnam):
        // Http::get('http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get', [
        //     'ApiKey' => env('ESMS_API_KEY'),
        //     'SecretKey' => env('ESMS_SECRET_KEY'),
        //     'Phone' => $phone,
        //     'Content' => "Ma OTP cua ban la: {$otp}. Ma co hieu luc trong {$minutes} phut.",
        //     'SmsType' => 2,
        // ]);

        Log::warning("SMS Gateway chưa được cấu hình. OTP không được gửi.", [
            'phone' => $phone,
        ]);
    }

    /**
     * Tự động chọn phương thức gửi OTP
     * Ưu tiên: Email > Phone (vì SMS chưa tích hợp)
     */
    public function send(?string $email, ?string $phone, string $otp): string
    {
        if ($email) {
            $this->sendEmail($email, $otp);
            return 'email';
        }

        if ($phone) {
            $this->sendSms($phone, $otp);
            return 'sms';
        }

        throw new \RuntimeException('USER_HAS_NO_CONTACT_METHOD');
    }
}
