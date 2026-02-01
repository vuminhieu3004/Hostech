<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class LoginOtpMail extends Mailable
{

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
