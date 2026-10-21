<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiên thi #{{ $lanThi->id }} | DET Practice</title>
    <link rel="stylesheet"
          href="{{ asset('css/mock-test.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ url('/') }}" class="logo">
                DET <span>Practice</span>
            </a>

            <span class="header-status">
                Phiên thi #{{ $lanThi->id }}
            </span>
        </div>
    </header>

    <main class="container page">
        <div class="page-intro">
            <span class="eyebrow">DET PRACTICE · MOCK TEST</span>

            <h1>{{ $deThi->ten ?? 'Phiên thi thử' }}</h1>

            <p>
                Thông tin lần thi của bạn.
                Phần cấp câu hỏi sẽ được bổ sung trong MT02.
            </p>
        </div>


        <section class="session-card">

            @if ($errors->any())
                <div class="alert" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($lanThi->trangthai !== 'in_progress')

                <h2>Phiên thi không còn hoạt động</h2>
                <p>Trạng thái: {{ $lanThi->trangthai }}</p>

            @elseif ($cauDuocCap && $cauHoi)

                <div class="session-heading">
                    <div>
                        <span class="eyebrow">
                            CÂU HỎI {{ $cauDuocCap->thutu }}
                        </span>
                        <h2>{{ $cauHoi->tenloai }}</h2>
                    </div>

                    <span class="status-pill">
                        {{ ucfirst($cauHoi->kynang ?? 'Luyện tập') }}
                    </span>
                </div>

                <div class="question-content">
                    <p class="question-label">Nội dung câu hỏi</p>

                    <div class="question-text">
                        {{ $cauHoi->noidung }}
                    </div>
                </div>

                @if ($cauHoi->anh)
                    <p class="media-note">
                        Câu hỏi có hình ảnh; đang chờ tích hợp
                        đường dẫn media của TV3.
                    </p>
                @endif

                @if ($cauHoi->audio)
                    <p class="media-note">
                        Câu hỏi có âm thanh; đang chờ tích hợp
                        đường dẫn audio của TV3.
                    </p>
                @endif

                @if ($luaChon->isNotEmpty())
                    <div class="answer-options">
                        <h3>Danh sách lựa chọn</h3>

                        @foreach ($luaChon as $item)
                            <div class="answer-option">
                                <span class="option-letter">
                                    {{ $loop->iteration }}
                                </span>
                                <span>{{ $item->noidung }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="session-notice">
                    Câu hỏi đã được cấp thành công.
                    Chức năng trả lời sẽ được phát triển ở MT03.
                </div>

            @elseif ($soCauHopLeConLai === 0)

                <h2>Không còn câu hỏi có thể cấp</h2>
                <p>
                    Đề thi chưa có câu hỏi được xuất bản
                    hoặc tất cả câu hỏi hợp lệ đã được cấp.
                </p>

            @else

                <h2>Sẵn sàng nhận câu hỏi?</h2>
                <p>
                    Hệ thống sẽ lựa chọn câu hỏi tiếp theo
                    theo hình thức đề thi.
                </p>

                <form method="POST"
                    action="{{ route('mock-test.question.next', $lanThi->id) }}">
                    @csrf

                    <button type="submit" class="btn btn-primary">
                        Nhận câu hỏi →
                    </button>
                </form>

            @endif

            <div class="session-actions">
                <a href="{{ route('mock-test.index') }}"
                class="btn btn-dark">
                    ← Danh sách đề thi
                </a>
            </div>

        </section>

    </main>

    <footer class="site-footer">
        DET Practice · Mock Test
    </footer>
</body>
</html>
