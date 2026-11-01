@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Đặt lại mật khẩu mới</h1>
        <p style="color: #64748b; font-size: 14px; text-align: center; margin-bottom: 22px; line-height: 1.5;">
            Thiết lập mật khẩu mới đạt chuẩn bảo mật cho tài khoản của bạn.
        </p>

        @if ($errors->has('email'))
            <div class="alert-error" style="color: #dc2626; margin-bottom: 15px; font-size: 14px;">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" placeholder="example@domain.com" required autofocus>
                @error('email')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu mới</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)" required>
                <small class="password-hint" style="display: block; font-size: 12px; color: #64748b; margin-top: 5px; line-height: 1.4;">
                    🔒 Yêu cầu: tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt (VD: Abc@1234).
                </small>
                @error('matkhau')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn-primary">Đặt lại mật khẩu</button>
        </form>

        <div class="auth-links" style="margin-top: 20px; font-size: 13px; text-align: center;">
            <a href="{{ route('login') }}" style="color: #2cb67d; text-decoration: none; font-weight: 600;">← Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
