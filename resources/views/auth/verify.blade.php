@extends('layouts.app')

@section('title', 'Xác minh Email - DET Practice')
@section('body_class', 'auth-body')

@section('content')
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
                <button type="submit" style="background: none; border: none; color: var(--bento-orange); font-weight: 700; cursor: pointer; text-decoration: none; padding: 0;">Đăng xuất</button>
            </form>
            • <a href="/">Trở về trang chủ</a>
        </div>
    </div>
@endsection
