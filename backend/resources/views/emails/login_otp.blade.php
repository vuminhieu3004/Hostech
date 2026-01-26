<!doctype html>
<html>
<head><meta charset="utf-8"></head>
<body>
  <h3>Mã OTP đăng nhập Hostech</h3>
  <p>Mã OTP của bạn là: <b style="font-size:20px;">{{ $otp }}</b></p>
  <p>Mã có hiệu lực trong {{ intval($ttlSeconds/60) }} phút ({{ $ttlSeconds }} giây).</p>
  <p>Nếu bạn không yêu cầu đăng nhập, hãy bỏ qua email này.</p>
</body>
</html>
