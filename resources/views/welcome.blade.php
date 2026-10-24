
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DET Practice - Luyện thi tiếng Anh</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<div class="site-shell">

    <header class="glass navbar">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/fox-logo.jpg') }}"
                 alt="Linh vật cáo DET Practice">
            <span><b>DET</b> Practice</span>
        </a>

        <nav class="nav-links" aria-label="Điều hướng chính">
            <a class="active" href="{{ url('/') }}">Trang chủ</a>
            <a href="{{ route('mock-test.index') }}">Thi thử</a>
            <span class="nav-unavailable">Luyện tập</span>
            <span class="nav-unavailable">Dashboard</span>
        </nav>

        <div class="nav-account">
            @auth
                <span>Xin chào, {{ auth()->user()->hoten ?? 'Học viên' }}</span>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}">Đăng nhập</a>
                @else
                    <span>Tài khoản</span>
                @endif
            @endauth
        </div>
    </header>

    <main>
        <section class="glass hero">
            <div class="hero-content">
                <span class="eyebrow">WELCOME TO DET PRACTICE</span>

                <h1>
                    Luyện thi DET
                    <span>theo cách của bạn.</span>
                </h1>

                <p>
                    Bắt đầu từ một bài thi thử.
                    Tiến bộ qua từng ngày.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('mock-test.index') }}"
                       class="btn btn-primary">
                        Khám phá đề thi <span aria-hidden="true">↗</span>
                    </a>

                    <a href="#kham-pha" class="btn btn-glass">
                        Tìm hiểu thêm
                    </a>
                </div>
            </div>

            <div class="hero-mascot">
                <div class="mascot-halo"></div>
                <img src="{{ asset('images/fox-logo.jpg') }}"
                     alt="Cáo nhỏ đồng hành cùng bạn luyện thi DET">
                <span class="mascot-caption">Small steps. Big progress.</span>
            </div>
        </section>

        <section id="kham-pha" class="discover">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">BẮT ĐẦU TỪ ĐÂY</span>
                    <h2>Hôm nay bạn muốn làm gì?</h2>
                </div>
            </div>

            <div class="feature-grid">
                <a class="glass feature-card feature-primary"
                   href="{{ route('mock-test.index') }}">
                    <div class="feature-icon" aria-hidden="true">✦</div>
                    <div class="feature-content">
                        <h3>Thi thử DET</h3>
                        <p>Khám phá đề thi và bắt đầu thử sức.</p>
                    </div>
                    <span class="feature-arrow" aria-hidden="true">↗</span>
                </a>

                <article class="glass feature-card">
                    <div class="feature-icon" aria-hidden="true">✎</div>
                    <div class="feature-content">
                        <h3>Luyện tập kỹ năng</h3>
                        <p>Cải thiện từng kỹ năng tiếng Anh.</p>
                        <small>Đang phát triển</small>
                    </div>
                </article>

                <article class="glass feature-card">
                    <div class="feature-icon" aria-hidden="true">◉</div>
                    <div class="feature-content">
                        <h3>Tiến độ học tập</h3>
                        <p>Theo dõi hành trình và kết quả của bạn.</p>
                        <small>Đang phát triển</small>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <span>© {{ date('Y') }} DET Practice</span>
        <span>Learn at your own pace.</span>
    </footer>

</div>

</body>
</html>
