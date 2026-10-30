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
            <div class="session-heading">
                <div>
                    <span class="eyebrow">THÔNG TIN PHIÊN THI</span>
                    <h2>Lần thi #{{ $lanThi->id }}</h2>
                </div>
                <span class="status-pill">
                    {{ match($lanThi->trangthai) {
                        'in_progress' => 'Đang diễn ra',
                        'completed' => 'Đã hoàn thành',
                        'abandoned' => 'Đã bỏ dở',
                        default => $lanThi->trangthai
                    } }}
                </span>
            </div>

            <div class="info-grid">
                <div>
                    <small>Đề thi</small>
                    <strong>{{ $deThi->ten ?? 'Không xác định' }}</strong>
                </div>

                <div>
                    <small>Hình thức</small>
                    <strong>
                        {{ $deThi?->kieu === 'adaptive'
                            ? 'Adaptive' : 'Fixed' }}
                    </strong>
                </div>

                <div>
                    <small>Thời gian quy định</small>
                    <strong>
                        {{ $deThi ? ceil($deThi->thoigian / 60) : '—' }}
                        phút
                    </strong>
                </div>

                <div>
                    <small>Thời điểm bắt đầu</small>
                    <strong>
                        {{ $lanThi->batdau?->format('d/m/Y H:i:s') }}
                    </strong>
                </div>

                <div>
                    <small>Độ khó hiện tại</small>
                    <strong>{{ $lanThi->dokho_hientai }}</strong>
                </div>

                <div>
                    <small>Trạng thái</small>
                    <strong>{{ $lanThi->trangthai }}</strong>
                </div>
            </div>

            <div class="session-notice">
                @if ($lanThi->trangthai === 'in_progress')
                    Phiên thi đã được khởi tạo thành công.
                    Chức năng nhận câu hỏi và trả lời sẽ
                    được phát triển trong MT02 và MT03.
                @else
                    Phiên thi này không còn ở trạng thái đang diễn ra.
                @endif
            </div>

            <div class="session-actions">
                <a class="btn btn-dark"
                   href="{{ route('mock-test.index') }}">
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
