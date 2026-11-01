@extends('layouts.app')

@section('title', 'Đăng ký - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Tạo tài khoản DET Practice</h1>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="form-group">
                <label for="hoten">Họ và tên</label>
                <input type="text" id="hoten" name="hoten" value="{{ old('hoten') }}" placeholder="Nguyễn Văn A" required autofocus>
                @error('hoten')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required>
                @error('email')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Tối thiểu 8 ký tự (hoa, thường, số, ký tự đặc biệt)" required>
                <small class="password-hint" style="display: block; font-size: 12px; color: #64748b; margin-top: 5px; line-height: 1.4;">
                    🔒 Yêu cầu: tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt (VD: Abc@1234).
                </small>
                @error('matkhau')
                    <div class="error-message" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn-primary">Đăng ký tài khoản</button>
        </form>

        <div class="auth-links" style="margin-top: 20px; font-size: 13px; text-align: center;">
            Đã có tài khoản? <a href="{{ route('login') }}" style="color: #2cb67d; text-decoration: none; font-weight: 600;">Đăng nhập ngay</a>
        </div>
    </div>
@endsection
