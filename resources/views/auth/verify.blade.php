<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh Email - DET Practice</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4fbf7; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #ffffff; padding: 36px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.06); width: 100%; max-width: 440px; box-sizing: border-box; text-align: center; }
        .icon { font-size: 48px; margin-bottom: 12px; }
        h1 { margin-top: 0; font-size: 22px; color: #1e293b; }
        p { color: #64748b; font-size: 14px; line-height: 1.6; margin: 12px 0 20px; }
        .btn-primary { width: 100%; background: #00b86b; color: white; border: none; padding: 11px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: #009e5b; }
        .status-message { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .auth-links { margin-top: 24px; font-size: 13px; color: #64748b; }
        .auth-links a, .auth-links button { color: #00b86b; background: none; border: none; font-weight: 600; cursor: pointer; text-decoration: none; padding: 0; }
        .auth-links a:hover, .auth-links button:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="icon">✉️</div>
        <h1>Xác minh địa chỉ Email</h1>

        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        <p>
            Cảm ơn bạn đã đăng ký tài khoản <strong>DET Practice</strong>! Trước khi bắt đầu luyện tập, vui lòng kiểm tra hộp thư email của bạn và nhấn vào liên kết xác nhận chúng tôi vừa gửi.
        </p>

        <p style="font-size: 13px; color: #94a3b8;">
            Nếu bạn không nhận được thư, hãy nhấn vào nút bên dưới để chúng tôi gửi lại.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Gửi lại email xác minh</button>
        </form>

        <div class="auth-links">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">Đăng xuất</button>
            </form>
            • <a href="/">Trở về trang chủ</a>
        </div>
    </div>
</body>
</html>

