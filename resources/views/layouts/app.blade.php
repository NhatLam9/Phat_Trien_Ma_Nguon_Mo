<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DET Practice - Nền tảng luyện thi Duolingo English Test')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    @stack('styles')
</head>
<body class="@yield('body_class', 'app-body')">

    <!-- HEADER / NAVIGATION THEO VAI TRÒ (Đúng ngữ cảnh: Khách chỉ gồm Trang chủ, Đăng nhập, Đăng ký) -->
    <header class="app-header">
        <a href="{{ url('/') }}" class="logo-container">
            <div class="logo">DET<span>Practice</span></div>
        </a>

        @auth
            <!-- Menu cho Người học và Admin đã đăng nhập -->
            <nav class="main-nav">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
                <a href="{{ route('practice.index') }}" class="{{ request()->is('practice*') && !request()->is('practice/bookmarks*') && !request()->is('practice/history*') ? 'active' : '' }}">Luyện tập</a>
                <a href="{{ route('practice.bookmarks') }}" class="{{ request()->is('practice/bookmarks*') ? 'active' : '' }}">Câu hỏi đã lưu</a>
                <a href="{{ route('practice.history') }}" class="{{ request()->is('practice/history*') ? 'active' : '' }}">Lịch sử</a>
                <a href="{{ route('mock-test.index') }}" class="{{ request()->is('mock-test*') ? 'active' : '' }}">Thi thử</a>

                @if(Auth::user()->vaitro === 'admin' || Auth::user()->isAdmin())
                    <!-- Menu dành riêng cho Quản trị viên (Admin) -->
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin*') ? 'active' : '' }}" style="color: #7e22ce; font-weight: 700;">
                        Quản trị <span class="badge badge-admin">Admin</span>
                    </a>
                @endif
            </nav>

            <div class="header-actions">
                <!-- Hồ sơ cá nhân & Đăng xuất -->
                <a href="{{ route('profile.edit') }}" class="user-chip" title="Xem và sửa hồ sơ cá nhân">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->hoten }}">
                    <span>{{ Auth::user()->hoten }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Đăng xuất</button>
                </form>
            </div>
        @else
            <!-- Thanh điều hướng cho Khách (chưa đăng nhập) đúng ngữ cảnh: Trang chủ, Đăng nhập, Đăng ký -->
            <nav class="main-nav">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
                <a href="{{ route('login') }}" class="{{ request()->is('login') ? 'active' : '' }}">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="color: #ffffff; padding: 7px 18px;">Đăng ký</a>
            </nav>
        @endauth
    </header>

    <!-- MAIN CONTENT -->
    <main class="app-main @yield('main_class')">
        <!-- Thông báo Flash Messages (Thuần HTML/CSS, không có JavaScript) -->
        @if (session('status'))
            <div class="alert alert-success">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
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
