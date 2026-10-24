<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - DET Practice</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); width: 100%; max-width: 400px; box-sizing: border-box; }
        h1 { margin-top: 0; font-size: 24px; color: #1e293b; text-align: center; }
        p { color: #64748b; font-size: 14px; text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 6px; }
        input[type="email"] { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        input:focus { border-color: #2cb67d; outline: none; }
        .btn-primary { width: 100%; background: #2cb67d; color: white; border: none; padding: 10px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: #249667; }
        .error-message { color: #dc2626; font-size: 13px; margin-top: 4px; }
        .status-message { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .auth-links { margin-top: 20px; font-size: 13px; text-align: center; color: #64748b; }
        .auth-links a { color: #2cb67d; text-decoration: none; font-weight: 600; }
        .auth-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Khôi phục mật khẩu</h1>
        <p>Nhập địa chỉ email của bạn để nhận liên kết đặt lại mật khẩu.</p>

        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">Gửi liên kết khôi phục</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>
