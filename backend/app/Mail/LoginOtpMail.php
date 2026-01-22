<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public int $ttlSeconds
    ) {}

    public function build()
    {
        return $this->subject('Mã OTP đăng nhập Hostech')
            ->view('emails.login_otp');
    }
}
