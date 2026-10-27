@extends('layouts.app')

@section('title', 'Quên mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Khôi phục mật khẩu</h1>
        <p>Nhập địa chỉ email của bạn để nhận liên kết đặt lại mật khẩu.</p>

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
            <a href="{{ route('login') }}">Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
