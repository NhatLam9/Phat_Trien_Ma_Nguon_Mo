
@extends('layouts.app')

@section('title', 'Làm bài thi thử - DET Practice')
@section('body_class', 'app-body mock-test-body')
@section('main_class', 'app-main--mock-exam')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">

    @if ($lanThi->trangthai === 'in_progress')
        <meta http-equiv="refresh"
              content="{{ max(1, $soGiayConLai + 1) }}">
    @endif
@endpush

@section('content')
    @php
        $dangThi = $lanThi->trangthai === 'in_progress';
        $tongSoGiay = max(1, (int) $deThi->thoigian);
        $phanTram = max(
            0,
            min(100, $soGiayConLai / $tongSoGiay * 100)
        );
        $soCauDaTraLoi = $dsCauDaCap
            ->filter(fn ($cau) => (int) $cau->datraloi === 1)
            ->count();
    @endphp

    <div class="exam-page">

        {{-- Thanh điều hướng --}}
        <div class="exam-topbar">
            <a href="{{ route('mock-test.index') }}"
               class="back-link">
                ← Danh sách đề thi
            </a>

            <div class="exam-breadcrumb">
                <span>LUYỆN THI DET</span>
                <span>/</span>
                <span>BÀI THI THỬ</span>
                <span>/</span>
                <span class="pill">MÃ ĐỀ {{ $deThi->id }}</span>
            </div>

            <div class="exam-status-pill">
                <span class="dot"></span>
                {{ $dangThi ? 'Đang làm bài' : 'Đã kết thúc' }}
            </div>
        </div>

        {{-- Thông tin đề --}}
        <header class="exam-heading">
            <div class="exam-heading-meta">
                <span class="eyebrow">
                    LUYỆN THI TRỰC TUYẾN
                </span>

                <span class="dot-sep">•</span>

                <span class="meta-pill">
                    {{ $deThi->kieu === 'adaptive'
                        ? 'Cấu trúc thích ứng'
                        : 'Cấu trúc đề cố định' }}
                </span>
            </div>

            <h1>{{ $deThi->ten }}</h1>

            <div class="exam-heading-sub">
                <span>
                    Cấu trúc đề:
                    {{ $deThi->kieu === 'adaptive'
                        ? 'Thích ứng'
                        : 'Cố định' }}
                </span>

                <span>•</span>

                <span class="progress-pill">
                    Tiến độ: {{ $soCauDaCap }}/{{ $tongSoCau }} câu hỏi
                </span>
            </div>
        </header>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="exam-notification" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="exam-notification exam-notification--error"
                 role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="exam-layout">

            {{-- CỘT TRÁI --}}
            <div class="exam-left">

                <section class="glass-card question-card"
                         id="question-area">

                    {{-- Phiên thi đã bỏ dở --}}
                    @if ($lanThi->trangthai === 'abandoned')

                        <div class="ready-state-box">
                            <span class="eyebrow">
                                {{ $biHetGio
                                    ? 'HẾT THỜI GIAN'
                                    : 'ĐÃ BỎ DỞ' }}
                            </span>

                            <h2>
                                {{ $biHetGio
                                    ? 'Bài thi đã hết thời gian'
                                    : 'Bài thi đã bỏ dở' }}
                            </h2>

                            <p>
                                Phiên thi đã kết thúc.
                                Bạn không thể tiếp tục làm bài này.
                            </p>

                            <a href="{{ route('mock-test.index') }}"
                               class="nav-btn nav-btn-primary">
                                Chọn đề thi khác →
                            </a>
                        </div>

                    {{-- Phiên thi đã hoàn thành --}}
                    @elseif ($lanThi->trangthai === 'completed')

                        <div class="ready-state-box">
                            <span class="eyebrow">
                                HOÀN THÀNH BÀI THI
                            </span>

                            <h2>
                                {{ $biHetGio
                                    ? 'Bài thi đã tự động nộp vì hết giờ'
                                    : 'Bạn đã hoàn thành bài thi' }}
                            </h2>

                            <p>
                                Đã ghi nhận {{ $soCauDaTraLoi }}
                                câu trả lời.
                                Kết quả chấm điểm chi tiết sẽ
                                được cập nhật trong MT05 và MT06.
                            </p>

                            <a href="{{ route('mock-test.index') }}"
                               class="nav-btn nav-btn-primary">
                                Danh sách đề thi →
                            </a>
                        </div>

                    {{-- Câu hỏi đang làm --}}
                    @elseif ($cauDuocCap && $cauHoi)

                        <div class="question-top-meta">
                            <div class="question-code-group">

                                <span class="small-pill">
                                    CÂU
                                    {{ str_pad($cauDuocCap->thutu, 2, '0', STR_PAD_LEFT) }}
                                    /
                                    {{ $tongSoCau }}
                                </span>

                                <span class="small-pill light-pill">
                                    {{ $cauHoi->tenloai }}
                                </span>
                            </div>

                            {{-- Sẽ kích hoạt khi có backend đánh dấu --}}
                            <button type="button"
                                    class="bookmark-btn"
                                    disabled
                                    title="Chức năng đánh dấu xem lại đang được phát triển">
                                Đánh dấu xem lại
                            </button>
                        </div>

                        {{-- Nội dung câu hỏi --}}
                        <div class="question-block">
                            <p class="field-label">
                                NỘI DUNG CÂU HỎI
                            </p>

                            <div class="question-content-box">
                                {{ $cauHoi->noidung }}
                            </div>

                            @if ($cauHoi->anh)
                                <img
                                    src="{{ asset(ltrim($cauHoi->anh, '/')) }}"
                                    alt="Hình ảnh minh họa câu hỏi"
                                    class="question-image">
                            @endif

                            @if ($cauHoi->audio)
                                <audio controls
                                       preload="metadata"
                                       class="question-audio">
                                    <source src="{{ asset(ltrim($cauHoi->audio, '/')) }}">
                                    Trình duyệt không hỗ trợ âm thanh.
                                </audio>
                            @endif
                        </div>

                        {{-- MT03: Lưu câu trả lời --}}

                        {{-- MT02 / MT03 / MT05: Trả lời và chuyển câu --}}
                        @php
                            $laCauCuoi = $soCauHopLeConLai === 0
                                && $cauSau === null;
                        @endphp

                        <form method="POST"
                            action="{{ $laCauCuoi
                                ? route('mock-test.finish', $lanThi->id)
                                : route('mock-test.skip', $lanThi->id) }}"
                            class="answer-form">

                            @csrf

                            <input type="hidden"
                                name="lanthicauhoi_id"
                                value="{{ $cauDuocCap->id }}">

                            @if ($laCauCuoi)
                                <input type="hidden"
                                    name="xac_nhan_bo_trong"
                                    value="1">
                            @endif

                            {{-- Đáp án trắc nghiệm --}}
                            @if ($luaChon->isNotEmpty())
                                <div class="answer-list">
                                    <p class="field-label">CHỌN ĐÁP ÁN CỦA BẠN</p>

                                    @foreach ($luaChon as $item)
                                        <label class="answer-item">
                                            <input type="radio"
                                                name="luachon_id"
                                                value="{{ $item->id }}"
                                                @checked(old('luachon_id') == $item->id)>

                                            <span class="answer-item-inner">
                                                <span class="answer-letter">
                                                    {{ chr(65 + $loop->index) }}
                                                </span>

                                                <span class="answer-text">
                                                    {{ $item->noidung }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- Đáp án tự luận --}}
                            @else
                                <div class="text-answer-box">
                                    <label for="noidung" class="field-label">
                                        CÂU TRẢ LỜI CỦA BẠN
                                    </label>

                                    <textarea id="noidung"
                                            name="noidung"
                                            rows="6"
                                            maxlength="10000"
                                            placeholder="Nhập câu trả lời...">{{ old('noidung') }}</textarea>
                                </div>
                            @endif

                            {{-- Điều hướng và tự động lưu đáp án --}}
                            <div class="question-nav-actions">

                                <div class="nav-left">
                                    @if ($cauTruoc)
                                        <a href="{{ route('mock-test.show', [
                                            'id' => $lanThi->id,
                                            'cau' => $cauTruoc->id
                                        ]) }}"
                                        class="nav-btn nav-btn-secondary">
                                            ← Câu trước
                                        </a>
                                    @else
                                        <button type="button"
                                                class="nav-btn nav-btn-disabled"
                                                disabled>
                                            ← Câu trước
                                        </button>
                                    @endif
                                </div>

                                <div class="nav-right">
                                    <button type="submit"
                                            class="nav-btn nav-btn-primary">

                                        @if ($laCauCuoi)
                                            Nộp bài
                                        @else
                                            Câu sau →
                                        @endif

                                    </button>
                                </div>
                            </div>
                        </form>

                    {{-- Đã cấp hết câu hỏi --}}
                    @elseif ($soCauHopLeConLai === 0)

                        <div class="ready-state-box">
                            <span class="eyebrow">
                                TRẠNG THÁI BÀI THI
                            </span>

                            <h2>
                                {{ $soCauDaCap > 0
                                    ? 'Đã cấp hết câu hỏi'
                                    : 'Đề thi chưa có câu hỏi hợp lệ' }}
                            </h2>

                            <p>
                                {{ $soCauDaCap > 0
                                    ? 'Bạn đã đi hết ngân hàng câu hỏi của đề thi. Hãy kiểm tra Phiếu trả lời và xác nhận nộp bài.'
                                    : 'Không có câu hỏi hợp lệ để bắt đầu bài thi.' }}
                            </p>
                        </div>

                    {{-- Chưa bắt đầu nhận câu hỏi --}}
                    @else

                        <div class="ready-state-box">
                            <span class="eyebrow">
                                BẮT ĐẦU BÀI THI
                            </span>

                            <h2>Bạn đã sẵn sàng làm bài?</h2>

                            <p>
                                Hệ thống sẽ lựa chọn câu hỏi theo
                                hình thức Fixed hoặc Adaptive.
                            </p>

                            <form method="POST"
                                  action="{{ route('mock-test.question.next', $lanThi->id) }}">
                                @csrf

                                <button type="submit"
                                        class="start-btn">
                                    Bắt đầu làm bài →
                                </button>
                            </form>
                        </div>
                    @endif

                </section>

                {{-- Các thao tác bài thi --}}
                <section class="glass-card action-card">
                    <div>
                        <h3>THAO TÁC BÀI THI</h3>
                        <p>
                            Kiểm tra câu trả lời hoặc kết thúc bài thi.
                        </p>
                    </div>

                    <div class="action-buttons">
                        @if ($dangThi)
                            <a href="#confirm-finish"
                               class="nav-btn nav-btn-primary">
                                Xác nhận nộp bài
                            </a>

                            <details class="abandon-confirm">
                                <summary class="nav-btn nav-btn-danger-outline">
                                    Bỏ dở bài thi
                                </summary>

                                <div class="abandon-confirm-content">
                                    <strong>Xác nhận bỏ dở bài thi?</strong>

                                    <p>
                                        Phiên thi sẽ kết thúc và không
                                        được tính điểm.
                                    </p>

                                    <form method="POST"
                                          action="{{ route('mock-test.abandon', $lanThi->id) }}">
                                        @csrf

                                        <button type="submit"
                                                class="nav-btn nav-btn-danger-outline">
                                            Xác nhận bỏ dở
                                        </button>
                                    </form>
                                </div>
                            </details>
                        @else
                            <a href="{{ route('mock-test.index') }}"
                               class="nav-btn nav-btn-secondary">
                                Danh sách đề thi
                            </a>
                        @endif
                    </div>
                </section>

            </div>

            {{-- CỘT PHẢI --}}
            <aside class="exam-right">

                {{-- MT04: Đồng hồ --}}
                @if ($dangThi)
                    <section class="glass-card timer-card">
                        <div class="timer-title">
                            <span>THỜI GIAN CÒN LẠI</span>
                            <span class="dot"></span>
                        </div>

                        <div class="timer-number"
                             id="mock-countdown"
                             data-seconds="{{ $soGiayConLai }}">
                            {{ sprintf(
                                '%02d:%02d',
                                intdiv($soGiayConLai, 60),
                                $soGiayConLai % 60
                            ) }}
                        </div>

                        <p class="timer-note">
                            Thời gian làm bài còn lại
                        </p>

                        <div class="timer-bar">
                            <div id="mock-timer-progress"
                                 style="width: {{ $phanTram }}%">
                            </div>
                        </div>

                        <div class="timer-deadline">
                            <div>
                                <span>Hết hạn lúc:</span>
                                <strong>
                                    {{ $hanThi->format('H:i:s') }}
                                </strong>
                            </div>

                            <div>
                                <span>Ngày hết hạn:</span>
                                <strong>
                                    {{ $hanThi->format('d/m/Y') }}
                                </strong>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Phiếu trả lời --}}
                <section class="glass-card answer-sheet-card">
                    <div class="sheet-head">
                        <h3>PHIẾU TRẢ LỜI</h3>

                        <span class="sheet-progress">
                            {{ $soCauDaTraLoi }}/{{ $tongSoCau }}
                        </span>
                    </div>

                    <div class="sheet-grid">
                        @foreach ($phieuTraLoi as $item)
                            @php
                                $trangThai = $item['status'];
                            @endphp

                            @if ($dangThi && $item['lanthicauhoi_id'])
                                <a
                                    href="{{ route('mock-test.show', [
                                        'id' => $lanThi->id,
                                        'cau' => $item['lanthicauhoi_id']
                                    ]) }}"
                                    class="sheet-item {{ $trangThai }}"
                                    title="Câu {{ $item['thutu'] }}">
                                    {{ $item['thutu'] }}
                                </a>
                            @else
                                <span
                                    class="sheet-item {{ $trangThai }}"
                                    title="Câu {{ $item['thutu'] }}">
                                    {{ $item['thutu'] }}
                                </span>
                            @endif
                        @endforeach
                    </div>

                    <div class="sheet-legend">
                        <span>
                            <i class="legend-dot answered"></i>
                            Đã trả lời
                        </span>

                        <span>
                            <i class="legend-dot empty"></i>
                            Chưa làm
                        </span>

                        <span>
                            <i class="legend-dot seen"></i>
                            Đã cấp
                        </span>

                        <span>
                            <i class="legend-dot current"></i>
                            Đang xem
                        </span>
                    </div>
                </section>

                {{-- Hướng dẫn --}}
                <section class="glass-card note-card">
                    <h3>Lưu ý quan trọng khi làm bài</h3>

                    <p>
                        Chọn đáp án và nhấn Lưu đáp án để ghi nhận
                        câu trả lời.
                    </p>

                    <p>
                        Chuyển câu mà chưa lưu sẽ không tự động
                        lưu lựa chọn đang nhập.
                    </p>

                    <p>
                        Nhấn vào ô số trong Phiếu trả lời
                        để mở các câu chưa nộp đáp án.
                    </p>
                </section>

            </aside>
        </div>

        {{-- MT05: Xác nhận nộp bài --}}
        @if ($dangThi)
            <div id="confirm-finish" class="finish-overlay">
                <div class="finish-modal"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="finish-title">

                    <h2 id="finish-title">
                        Xác nhận nộp bài
                    </h2>

                    @if ($cauChuaTraLoi->isNotEmpty() ||
                         $soCauHopLeConLai > 0)

                        <p>
                            Bạn vẫn còn câu hỏi chưa hoàn thành.
                            Các câu bỏ trống sẽ không có điểm.
                        </p>

                        @if ($cauChuaTraLoi->isNotEmpty())
                            <p>
                                Câu chưa trả lời:
                                {{ $cauChuaTraLoi
                                    ->pluck('thutu')
                                    ->implode(', ') }}
                            </p>
                        @endif

                        @if ($soCauHopLeConLai > 0)
                            <p>
                                Còn {{ $soCauHopLeConLai }}
                                câu hỏi chưa được cấp.
                            </p>
                        @endif
                    @else
                        <p>
                            Bạn có chắc chắn muốn nộp bài thi?
                        </p>
                    @endif

                    <div class="finish-actions">
                        <a href="#question-area"
                           class="nav-btn nav-btn-secondary">
                            Quay lại
                        </a>

                        <form method="POST"
                              action="{{ route('mock-test.finish', $lanThi->id) }}">
                            @csrf

                            <input type="hidden"
                                   name="xac_nhan_bo_trong"
                                   value="1">

                            <button type="submit"
                                    class="nav-btn nav-btn-primary">
                                Nộp bài thi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    @if ($lanThi->trangthai === 'in_progress')
        <script>
            (() => {
                const timer = document.getElementById('mock-countdown');
                const progress = document.getElementById('mock-timer-progress');

                if (!timer) return;

                const total = {{ $tongSoGiay }};
                const initial = Number(timer.dataset.seconds);
                const deadline = performance.now() + initial * 1000;

                let interval;

                function updateTimer() {
                    const seconds = Math.max(
                        0,
                        Math.ceil((deadline - performance.now()) / 1000)
                    );

                    const minutes = Math.floor(seconds / 60);
                    const remainder = seconds % 60;

                    timer.textContent =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(remainder).padStart(2, '0');

                    if (progress) {
                        progress.style.width =
                            Math.min(100, seconds / total * 100) + '%';
                    }

                    if (seconds <= 0) {
                        clearInterval(interval);
                        window.location.reload();
                    }
                }

                interval = setInterval(updateTimer, 1000);
                updateTimer();
            })();
        </script>
    @endif
@endpush
