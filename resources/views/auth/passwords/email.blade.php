@extends('layouts.app')

@section('title', 'Quên mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Khôi phục mật khẩu</h1>
        <p class="subtitle">Nhập địa chỉ email của bạn để nhận liên kết đặt lại mật khẩu.</p>

        @if (session('status'))
            <div class="status-message">
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
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 10px;">Gửi liên kết khôi phục</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
