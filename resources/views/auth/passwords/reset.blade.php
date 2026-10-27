@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - DET Practice')
@section('body_class', 'auth-body')

@section('content')
    <div class="auth-card">
        <h1>Đặt lại mật khẩu</h1>

        @if ($errors->has('email'))
            <div class="alert-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu mới</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Ít nhất 6 ký tự" required>
                @error('matkhau')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="matkhau_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="matkhau_confirmation" name="matkhau_confirmation" placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn-primary">Đặt lại mật khẩu</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Quay lại đăng nhập</a>
        </div>
    </div>
@endsection
