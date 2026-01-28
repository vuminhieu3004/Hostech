<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Đăng nhập</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Mã OTP đăng nhập Hostech</h2>
    <p>Mã OTP của bạn là:</p>
    <h1 style="color: #4CAF50; font-size: 36px; letter-spacing: 5px;">{{ $otp }}</h1>
    <p>Mã này có hiệu lực trong <strong>{{ ceil($ttlSeconds / 60) }} phút</strong>.</p>
    <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
    <hr>
    <p style="color: #999; font-size: 12px;">Email tự động từ hệ thống Hostech</p>
</body>
</html>
