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
                <input type="password" id="matkhau" name="matkhau" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)" required>
                <small class="password-hint">
                    🔒 Yêu cầu: tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt (VD: Abc@1234).
                </small>
                @error('matkhau')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 10px;">Đặt lại mật khẩu</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
