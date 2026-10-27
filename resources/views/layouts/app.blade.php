<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DET Practice')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class', 'app-body')">
    <header class="app-header">
        <div class="app-header__inner">
            <a class="app-brand" href="{{ url('/') }}">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="2" y="2" width="20" height="20" rx="6" fill="#58cc02"/>
                    <path d="M7 12.5l3 3 7-7" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="app-brand__text">DET <strong>Practice</strong></span>
            </a>

            <nav class="app-nav" aria-label="Điều hướng chính">
                @auth
                    @if (auth()->user()->vaitro === 'admin')
                        <a href="{{ route('admin.dashboard') }}">Quản trị</a>
                    @endif
                    <a href="{{ route('practice.index') }}">Luyện tập</a>
                    <span class="app-user">{{ auth()->user()->hoten }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="app-logout">
                        @csrf
                        <button type="submit">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Đăng nhập</a>
                    <a class="app-nav__cta" href="{{ route('register') }}">Đăng ký</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="app-main @yield('main_class')">
        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <p>DET Practice &copy; {{ date('Y') }} · Luyện thi DET mỗi ngày</p>
    </footer>

    @stack('scripts')
</body>
</html>
