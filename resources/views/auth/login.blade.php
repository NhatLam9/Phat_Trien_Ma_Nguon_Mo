<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - DET Practice</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); width: 100%; max-width: 400px; box-sizing: border-box; }
        h1 { margin-top: 0; font-size: 24px; color: #1e293b; text-align: center; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 6px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        input[type="email"]:focus, input[type="password"]:focus { border-color: #2cb67d; outline: none; }
        .btn-primary { width: 100%; background: #2cb67d; color: white; border: none; padding: 10px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: #249667; }
        .btn-google { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #ffffff; color: #334155; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; box-sizing: border-box; margin-top: 12px; }
        .btn-google:hover { background: #f8fafc; }
        .error-message { color: #dc2626; font-size: 13px; margin-top: 4px; }
        .status-message { background: #dcfce7; color: #166534; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; text-align: center; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .auth-links { margin-top: 20px; font-size: 13px; text-align: center; color: #64748b; }
        .auth-links a { color: #2cb67d; text-decoration: none; font-weight: 600; }
        .auth-links a:hover { text-decoration: underline; }
        .divider { display: flex; align-items: center; text-align: center; margin: 16px 0; color: #94a3b8; font-size: 12px; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e2e8f0; }
        .divider:not(:empty)::before { margin-right: .5em; }
        .divider:not(:empty)::after { margin-left: .5em; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Đăng nhập DET Practice</h1>

        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        @if ($errors->has('email'))
            <div class="alert-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="••••••••" required>
                @error('matkhau')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 13px;">
                <label style="margin: 0; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                </label>
                <a href="{{ route('password.request') }}" style="color: #2cb67d; text-decoration: none;">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-primary">Đăng nhập</button>
        </form>

        <div class="divider">HOẶC</div>

        <a href="{{ route('auth.google') }}" class="btn-google">
            <span>Tiếp tục với Google</span>
        </a>

        <div class="auth-links">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>
