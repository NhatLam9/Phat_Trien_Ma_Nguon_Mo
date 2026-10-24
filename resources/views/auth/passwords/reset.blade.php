<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - DET Practice</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); width: 100%; max-width: 420px; box-sizing: border-box; }
        h1 { margin-top: 0; font-size: 24px; color: #1e293b; text-align: center; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 6px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        input:focus { border-color: #2cb67d; outline: none; }
        .btn-primary { width: 100%; background: #2cb67d; color: white; border: none; padding: 10px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 8px; }
        .btn-primary:hover { background: #249667; }
        .error-message { color: #dc2626; font-size: 13px; margin-top: 4px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .auth-links { margin-top: 20px; font-size: 13px; text-align: center; color: #64748b; }
        .auth-links a { color: #2cb67d; text-decoration: none; font-weight: 600; }
        .auth-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Đặt lại mật khẩu</h1>

        @if ($errors->has('email'))
            <div class="alert-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu mới</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Ít nhất 6 ký tự" required>
                @error('matkhau')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn-primary">Đặt lại mật khẩu</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>

