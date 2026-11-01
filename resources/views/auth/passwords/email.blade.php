@extends('layouts.app')

@section('title', 'Quên mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Khôi phục mật khẩu</h1>
        <p style="color: #64748b; font-size: 14px; text-align: center; margin-bottom: 22px; line-height: 1.5;">
            Nhập địa chỉ email của bạn để nhận liên kết đặt lại mật khẩu.
        </p>

        @if (session('status'))
            <div class="status-message" style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; line-height: 1.4;">
                {{ session('status') }}
                @if (session('debug_reset_link'))
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #bbf7d0;">
                        <a href="{{ session('debug_reset_link') }}" style="color: #166534; font-weight: 700; text-decoration: underline; font-size: 13px;">
                            👉 Nhấn vào đây để đặt lại mật khẩu ngay (Thử nghiệm)
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required autofocus>
                @error('email')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">Gửi liên kết khôi phục</button>
        </form>

        <div class="auth-links" style="margin-top: 20px; font-size: 13px; text-align: center;">
            <a href="{{ route('login') }}" style="color: #2cb67d; text-decoration: none; font-weight: 600;">← Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
