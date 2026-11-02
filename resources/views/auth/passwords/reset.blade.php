@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Đặt lại mật khẩu mới</h1>
        <p class="subtitle">Thiết lập mật khẩu mới đạt chuẩn bảo mật cho tài khoản của bạn.</p>

        @if ($errors->has('email'))
            <div class="alert-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" placeholder="example@domain.com" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu mới</label>
                <div class="password-field-wrapper">
                    <input type="password" id="matkhau" name="matkhau" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)" required>
                    <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('matkhau', this)" title="Hiện mật khẩu" tabindex="-1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>

                @error('matkhau')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới</label>
                <div class="password-field-wrapper">
                    <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu mới" required>
                    <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('matkhau_confirmation', this)" title="Hiện mật khẩu" tabindex="-1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 10px;">Đặt lại mật khẩu</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
