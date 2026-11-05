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
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form" onsubmit="handleForgotPasswordSubmit(this)">
            @csrf

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" id="btn-submit-reset" class="btn-primary" style="margin-top: 10px; width: 100%; display: flex; align-items: center; justify-content: center; min-height: 44px;">
                <span id="btn-text">Gửi liên kết khôi phục</span>
                <span id="btn-loading" style="display: none; align-items: center; justify-content: center; gap: 8px;">
                    <span class="spinner-icon"></span>
                    <span>Đang gửi liên kết...</span>
                </span>
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">← Quay lại đăng nhập</a>
        </div>
    </div>

<script>
    function handleForgotPasswordSubmit(form) {
        if (form.checkValidity()) {
            const btn = document.getElementById('btn-submit-reset');
            const text = document.getElementById('btn-text');
            const loading = document.getElementById('btn-loading');

            if (btn && text && loading) {
                text.style.display = 'none';
                loading.style.display = 'inline-flex';
                btn.classList.add('btn-loading');
                btn.style.pointerEvents = 'none';
            }
        }
    }
</script>
@endsection
