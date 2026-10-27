@extends('layouts.app')

@section('title', 'DET Practice - Luyện thi DET mỗi ngày')
@section('main_class', 'app-main--home')

@section('content')
    <section class="det-hero">
        <svg class="det-art" viewBox="0 0 420 300" role="img" aria-label="Minh họa luyện thi DET">
            <path d="M40 224c-22-44-6-102 37-126 32-18 70-16 100-2 28-41 89-54 132-23 33 24 45 69 29 105 32 11 54 44 45 77-9 35-45 55-81 49-26 34-78 43-114 16-36 19-85 3-99-35-18 2-37-8-49-21z" fill="#e9ffd9"/>
            <circle cx="344" cy="66" r="24" fill="#ffc800"/>
            <circle cx="72" cy="74" r="10" fill="#1cb0f6"/>
            <path d="M62 120l6 14 14 6-14 6-6 14-6-14-14-6 14-6 6-14z" fill="#ffc800"/>
            <path d="M352 154l5 11 11 5-11 5-5 11-5-11-11-5 11-5 5-11z" fill="#1cb0f6"/>

            <rect x="78" y="62" width="264" height="176" rx="24" fill="#fff" stroke="#e5e5e5" stroke-width="4"/>
            <path d="M78 86c0-13 11-24 24-24h216c13 0 24 11 24 24v22H78V86z" fill="#58cc02"/>
            <circle cx="101" cy="85" r="5" fill="#fff" opacity=".9"/>
            <circle cx="119" cy="85" r="5" fill="#fff" opacity=".7"/>
            <circle cx="137" cy="85" r="5" fill="#fff" opacity=".5"/>

            <rect x="108" y="130" width="126" height="14" rx="7" fill="#e5e5e5"/>
            <rect x="108" y="156" width="194" height="10" rx="5" fill="#f0f0f0"/>
            <rect x="108" y="176" width="164" height="10" rx="5" fill="#f0f0f0"/>
            <rect x="108" y="201" width="98" height="18" rx="9" fill="#d7ffb8"/>

            <circle cx="299" cy="196" r="34" fill="#d7ffb8"/>
            <path d="M284 196l10 10 20-21" stroke="#58cc02" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>

            <rect x="102" y="246" width="76" height="10" rx="5" fill="#ffc800" opacity=".9"/>
            <rect x="188" y="246" width="108" height="10" rx="5" fill="#d7ffb8"/>
        </svg>

        <p class="det-eyebrow">Duolingo English Test</p>
        <h1 class="det-title">Luyện thi DET<br><span>mọi lúc, mọi nơi</span></h1>
        <p class="det-lead">
            Làm quen với các dạng bài, luyện đề mock test và theo dõi tiến độ của bạn.
        </p>

        <div class="det-actions">
            @auth
                <a class="det-btn det-btn--primary" href="{{ route('practice.index') }}">Vào luyện tập</a>
                @if (auth()->user()->vaitro === 'admin')
                    <a class="det-btn det-btn--ghost" href="{{ route('admin.dashboard') }}">Trang quản trị</a>
                @endif
            @else
                <a class="det-btn det-btn--primary" href="{{ route('register') }}">Bắt đầu miễn phí</a>
                <a class="det-btn det-btn--ghost" href="{{ route('login') }}">Đăng nhập</a>
            @endauth
        </div>

        <ul class="det-stats">
            <li><strong>8</strong><span>dạng bài DET</span></li>
            <li><strong>Mock</strong><span>test fixed &amp; adaptive</span></li>
            <li><strong>Miễn phí</strong><span>không cần cài đặt</span></li>
        </ul>
    </section>

    <section class="det-section">
        <h2 class="det-section__title">Luyện đúng phần bạn cần</h2>
        <p class="det-section__lead">
            Tập trung vào từng kỹ năng và từng dạng bài để tiến bộ nhanh hơn.
        </p>

        <div class="det-features">
            <article class="det-card">
                <div class="det-card__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <path d="M5 5.5A2.5 2.5 0 017.5 3H19v16H7.5A2.5 2.5 0 005 21.5V5.5z" stroke="#3f9e00" stroke-width="1.8"/>
                        <path d="M8 7h7M8 10.5h5" stroke="#3f9e00" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>Luyện theo dạng bài</h3>
                <p>Tám dạng bài DET được chia nhỏ theo kỹ năng đọc, nghe, viết và nói.</p>
            </article>

            <article class="det-card">
                <div class="det-card__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="13" r="8" stroke="#3f9e00" stroke-width="1.8"/>
                        <path d="M12 9v4l2.5 1.5M9 2h6" stroke="#3f9e00" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>Mock test như thi thật</h3>
                <p>Làm đề fixed hoặc adaptive để làm quen cấu trúc và áp lực thời gian.</p>
            </article>

            <article class="det-card">
                <div class="det-card__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <path d="M4 19V5M4 19h16" stroke="#3f9e00" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7 15l4-4 3 2 5-6" stroke="#3f9e00" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Theo dõi tiến độ</h3>
                <p>Lưu lịch sử luyện tập, câu hỏi đã đánh dấu và kết quả từng lần làm.</p>
            </article>
        </div>
    </section>

    <section class="det-section det-section--steps">
        <h2 class="det-section__title">Bắt đầu trong 3 bước</h2>

        <div class="det-steps">
            <article class="det-step">
                <span class="det-step__number">1</span>
                <h3>Tạo tài khoản</h3>
                <p>Đăng ký bằng email hoặc Google chỉ trong vài giây.</p>
            </article>
            <article class="det-step">
                <span class="det-step__number">2</span>
                <h3>Chọn dạng bài</h3>
                <p>Luyện từng kỹ năng hoặc làm một đề mock test hoàn chỉnh.</p>
            </article>
            <article class="det-step">
                <span class="det-step__number">3</span>
                <h3>Luyện và xem kết quả</h3>
                <p>Làm bài, nhận phản hồi và tiếp tục với phần cần cải thiện.</p>
            </article>
        </div>
    </section>
@endsection
