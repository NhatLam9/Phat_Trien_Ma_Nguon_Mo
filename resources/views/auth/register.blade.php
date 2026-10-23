<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng ký</title></head>
<body>
    <h1>Đăng ký</h1>
    <form method="POST" action="{{ route('register.submit') }}">
        @csrf
        <input type="text" name="hoten" placeholder="Họ tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="matkhau" placeholder="Mật khẩu" required>
        <input type="password" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
</body>
</html>