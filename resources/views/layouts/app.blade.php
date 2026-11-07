<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DET Practice - Nền tảng luyện thi Duolingo English Test')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/bento.css') }}?v={{ filemtime(public_path('css/bento.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/bento-pages.css') }}?v={{ filemtime(public_path('css/bento-pages.css')) }}">
</head>
@php
    $isFocusPage = request()->routeIs(
        'mock-test.show', 'practice.show', 'practice.session',
        'practice.set.show', 'practice.ai.question'
    );
    $isAccountPage = request()->routeIs(
        'login', 'register', 'password.*', 'verification.*'
    );
    $fullName = trim(auth()->user()?->hoten ?? '');
    $shortName = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::afterLast(preg_replace('/\s+/u', ' ', $fullName), ' '), 16);
    $useSidebar = auth()->check()
        && !request()->is('/') && !$isAccountPage && !$isFocusPage;
@endphp
<body class="@yield('body_class', 'app-body') {{ $useSidebar ? 'bento-sidebar-layout' : 'bento-topbar-layout' }} {{ $isFocusPage ? 'bento-focus-layout' : '' }}">
    <header class="app-header">
        <a href="{{ url('/') }}" class="logo-container" aria-label="Trang chủ AmberDET">
            <span class="bento-brand-mark" aria-hidden="true">A</span>
            <span class="bento-brand-name">Amber<strong>DET</strong></span>
        </a>
        @auth
            @if (!$isFocusPage)
                @if ($useSidebar)
                    <a href="{{ route('profile.edit') }}" class="bento-account" title="{{ $fullName }}">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="bento-account__avatar">
                        <span class="bento-account__info">
                            <strong>{{ $shortName }}</strong>
                            <small>{{ auth()->user()->vaitro === 'admin' || auth()->user()->isAdmin() ? 'Quản trị viên' : 'Người học' }}</small>
                        </span>
                    </a>
                @endif
                <nav class="main-nav" aria-label="Điều hướng chính">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
                    <details class="bento-nav-group" name="bento-main-nav" @if ($useSidebar && request()->routeIs('practice.*')) open @endif>
                        <summary class="bento-nav-toggle {{ request()->routeIs('practice.*') ? 'active' : '' }}">Luyện tập</summary>
                        <div class="bento-nav-children">
                            <a href="{{ route('practice.index') }}" class="{{ request()->routeIs('practice.*') && !request()->routeIs('practice.bookmarks*', 'practice.history') ? 'active' : '' }}">Vào luyện tập</a>
                            <a href="{{ route('practice.bookmarks') }}" class="{{ request()->routeIs('practice.bookmarks*') ? 'active' : '' }}">Câu hỏi đã lưu</a>
                            <a href="{{ route('practice.history') }}" class="{{ request()->routeIs('practice.history') ? 'active' : '' }}">Lịch sử luyện tập</a>
                        </div>
                    </details>
                    <details class="bento-nav-group" name="bento-main-nav" @if ($useSidebar && request()->routeIs('mock-test.*', 'learning.dashboard')) open @endif>
                        <summary class="bento-nav-toggle {{ request()->routeIs('mock-test.*', 'learning.dashboard') ? 'active' : '' }}">Thi thử</summary>
                        <div class="bento-nav-children">
                            <a href="{{ route('mock-test.index') }}" class="{{ request()->routeIs('mock-test.*') && !request()->routeIs('mock-test.history') ? 'active' : '' }}">Danh sách đề thi</a>
                            <a href="{{ route('mock-test.history') }}" class="{{ request()->routeIs('mock-test.history') ? 'active' : '' }}">Lịch sử thi</a>
                            <a href="{{ route('learning.dashboard') }}" class="{{ request()->routeIs('learning.dashboard') ? 'active' : '' }}">Thống kê</a>
                        </div>
                    </details>
                    <a href="{{ route('tutor.index') }}" class="{{ request()->is('tutor*') ? 'active' : '' }}">AI Tutor</a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Hồ sơ</a>
                    @if (auth()->user()->vaitro === 'admin' || auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Quản trị hệ thống</a>
                    @endif
                </nav>
                <div class="header-actions">
                    @if (!$useSidebar)
                        <a href="{{ route('profile.edit') }}" class="user-chip" title="{{ $fullName }}">
                            <img src="{{ auth()->user()->avatar_url }}" alt="">
                            <span>{{ $shortName }}</span>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Đăng xuất</button>
                    </form>
                </div>
            @else
                <span class="bento-focus-label">Không gian làm bài</span>
            @endif
        @else
            <nav class="main-nav" aria-label="Điều hướng chính">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
            </nav>
        @endauth
    </header>

    <!-- MAIN CONTENT -->
    <main class="app-main @yield('main_class')">
        <!-- Thông báo Flash Messages (Thuần HTML/CSS, không có JavaScript) -->
        @if (session('status') && !request()->is('forgot-password*') && !request()->is('reset-password*') && !request()->is('login*'))
            <div class="alert alert-success">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error') && !request()->is('forgot-password*') && !request()->is('reset-password*') && !request()->is('login*') && !request()->is('register*'))
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any() && !request()->is('forgot-password*') && !request()->is('reset-password*') && !request()->is('login*') && !request()->is('register*'))
            <div class="alert alert-danger">
                <div>
                    <strong>Có lỗi xảy ra:</strong>
                    <ul style="margin: 6px 0 0 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="app-footer">
        <div class="footer-inner">
            <p>&copy; {{ date('Y') }} DETPractice. Dự án môn Phát Triển Mã Nguồn Mở.</p>
            <p class="footer-credits">Nhóm phát triển: Trần Thế Hào (TV1) • Võ Nhật Lâm (TV2) • Đoàn Phi Phong (TV3)</p>
        </div>
    </footer>

    <!-- Tiện ích xem/ẩn mật khẩu an toàn trên mọi trình duyệt -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            var input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.title = 'Ẩn mật khẩu';
                btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00b86b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                input.type = 'password';
                btn.title = 'Hiện mật khẩu';
                btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
