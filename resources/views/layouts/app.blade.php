<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DET Practice - Nền tảng luyện thi Duolingo English Test')</title>
    
    <!-- CSS nền hệ thống do TV1 cung cấp dùng chung cho toàn bộ dự án -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>

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
                <a href="{{ url('/mock-test') }}" class="{{ request()->is('mock-test*') ? 'active' : '' }}">Mock Test</a>
                <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a>

                @if(Auth::user()->isAdmin())
                    <!-- Menu dành riêng cho Quản trị viên (Admin) -->
                    <a href="{{ url('/admin') }}" class="{{ request()->is('admin*') ? 'active' : '' }}" style="color: #7e22ce; font-weight: 700;">
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
            <!-- Thanh điều hướng cho Khách (chưa đăng nhập) chỉ gồm: Trang chủ, Đăng nhập, Đăng ký -->
            <nav class="main-nav">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
                <a href="{{ route('login') }}" class="{{ request()->is('login') ? 'active' : '' }}">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="color: #ffffff; padding: 7px 18px;">Đăng ký</a>
            </nav>
        @endauth
    </header>

    <!-- MAIN CONTENT -->
    <main class="app-main">
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
            <p>&copy; 2026 DETPractice. Dự án học tập môn Phát Triển Mã Nguồn Mở (không liên kết với Duolingo).</p>
            <p class="footer-credits">Phát triển bởi: Trần Thế Hào (TV1) • Võ Nhật Lâm (TV2) • Đoàn Phi Phong (TV3)</p>
        </div>
    </footer>

</body>
</html>
