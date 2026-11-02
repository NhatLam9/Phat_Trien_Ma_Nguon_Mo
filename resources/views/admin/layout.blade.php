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
                <a class="admin-nav__item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Tổng quan</a>
                <a class="admin-nav__item {{ request()->routeIs('admin.questions.index') || request()->routeIs('admin.questions.show') ? 'is-active' : '' }}" href="{{ route('admin.questions.index') }}">Ngân hàng câu hỏi</a>
                <a class="admin-nav__item {{ request()->routeIs('admin.questions.import.*') ? 'is-active' : '' }}" href="{{ route('admin.questions.import.create') }}">Import JSON</a>
                <a class="admin-nav__item {{ request()->routeIs('admin.question-types.*') ? 'is-active' : '' }}" href="{{ route('admin.question-types.index') }}">Dạng bài</a>
                <a class="admin-nav__item {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}">Chủ đề</a>
                <span class="admin-nav__item is-disabled" title="Sẽ làm ở bước tiếp theo">Đề thi</span>
                <a class="admin-nav__item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">Người dùng</a>
                <a class="admin-nav__item {{ request()->routeIs('admin.ai-logs.*') ? 'is-active' : '' }}" href="{{ route('admin.ai-logs.index') }}">Nhật ký AI</a>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <h1 class="admin-topbar__title">@yield('heading', 'Quản trị')</h1>
                <span class="admin-topbar__user">{{ auth()->user()->hoten ?? 'Quản trị viên' }}</span>
            </header>

            <main class="admin-content">
                @if (session('success'))
                    <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="admin-alert admin-alert--error">{{ session('error') }}</div>
                @endif

                @php($bagLoi = session('errors'))
                @if ($bagLoi && $bagLoi->any())
                    <div class="admin-alert admin-alert--error">
                        <strong>Vui lòng kiểm tra lại:</strong>
                        <ul>
                            @foreach ($bagLoi->all() as $loi)
                                <li>{{ $loi }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('bulk_skipped'))
                    <div class="admin-alert admin-alert--warning">
                        <strong>Một số câu hỏi bị bỏ qua:</strong>
                        <ul>
                            @foreach (session('bulk_skipped') as $boQua)
                                <li><code>{{ $boQua['ma'] }}</code>: {{ $boQua['ly_do'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
