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
                <span class="admin-brand__mark">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 12.5l3 3 7-7" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="admin-brand__text">
                    <strong>DET Admin</strong>
                    <small>Bảng điều khiển</small>
                </span>
            </a>

            <nav class="admin-nav" aria-label="Điều hướng quản trị">
                <p class="admin-nav__label">Tổng quan</p>
                <a class="admin-nav__item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
                   href="{{ route('admin.dashboard') }}">
                    @include('admin.partials.icon', ['icon' => 'dashboard'])
                    <span>Tổng quan</span>
                </a>

                <p class="admin-nav__label">Nội dung</p>
                <a class="admin-nav__item {{ request()->routeIs('admin.questions.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.questions.index') }}">
                    @include('admin.partials.icon', ['icon' => 'questions'])
                    <span>Ngân hàng câu hỏi</span>
                </a>
                <a class="admin-nav__item {{ request()->routeIs('admin.question-types.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.question-types.index') }}">
                    @include('admin.partials.icon', ['icon' => 'types'])
                    <span>Dạng bài</span>
                </a>
                <a class="admin-nav__item {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.categories.index') }}">
                    @include('admin.partials.icon', ['icon' => 'categories'])
                    <span>Chủ đề</span>
                </a>
                <a class="admin-nav__item {{ request()->routeIs('admin.exams.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.exams.index') }}">
                    @include('admin.partials.icon', ['icon' => 'exam'])
                    <span>Đề thi</span>
                </a>

                <p class="admin-nav__label">Hệ thống</p>
                <a class="admin-nav__item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.users.index') }}">
                    @include('admin.partials.icon', ['icon' => 'users'])
                    <span>Người dùng</span>
                </a>
                <a class="admin-nav__item {{ request()->routeIs('admin.ai-logs.*') ? 'is-active' : '' }}"
                   href="{{ route('admin.ai-logs.index') }}">
                    @include('admin.partials.icon', ['icon' => 'ai'])
                    <span>Nhật ký AI</span>
                </a>
            </nav>

            <div class="admin-sidebar__footer">
                <div class="admin-user">
                    <span class="admin-user__avatar">{{ mb_substr(auth()->user()->hoten ?? 'Q', 0, 1) }}</span>
                    <span class="admin-user__meta">
                        <strong>{{ auth()->user()->hoten ?? 'Quản trị viên' }}</strong>
                        <small>Quản trị viên</small>
                    </span>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="admin-topbar__eyebrow">Quản trị nội dung</p>
                    <h1 class="admin-topbar__title">@yield('heading', 'Quản trị')</h1>
                </div>
                <div class="admin-topbar__actions">
                    <a class="admin-topbar__link" href="{{ url('/') }}">← Về trang chủ</a>
                </div>
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
