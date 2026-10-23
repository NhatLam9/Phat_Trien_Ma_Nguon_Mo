<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quên mật khẩu</title></head>
<body>
    <h1>Khôi phục mật khẩu</h1>
    @if (session('status'))
        <div style="color: green;">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input type="email" name="email" placeholder="Nhập email của bạn" required>
        <button type="submit">Gửi liên kết khôi phục</button>
    </form>
    <br><a href="{{ route('login') }}">Trở về đăng nhập</a>
</body>
</html>