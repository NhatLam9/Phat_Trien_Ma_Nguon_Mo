<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị') - DET Practice</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="2" y="2" width="20" height="20" rx="6" fill="#58cc02"/>
                    <path d="M7 12.5l3 3 7-7" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>DET Admin</span>
            </a>

            <nav class="admin-nav">
                <a class="admin-nav__item is-active" href="{{ route('admin.dashboard') }}">Tổng quan</a>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Câu hỏi</span>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Dạng bài</span>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Chủ đề</span>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Đề thi</span>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Người dùng</span>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Nhật ký AI</span>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <h1 class="admin-topbar__title">@yield('heading', 'Quản trị')</h1>
                <span class="admin-topbar__user">{{ auth()->user()->hoten ?? 'Quản trị viên' }}</span>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
