@extends('layouts.app')

@section('title', 'Làm bài thi thử - DET Practice')
@section('body_class', 'app-body mock-test-body')
@section('main_class', 'app-main--mock-exam')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">

    @if ($lanThi->trangthai === 'in_progress')
        <meta http-equiv="refresh" content="{{ max(1, $soGiayConLai + 1) }}">
    @endif

    <style>
        .interactive-child {
            min-width: 0;
            margin: 0 0 20px;
            padding: 18px;
            border: 1px solid #d9e9df;
            border-radius: 14px;
        }

        .interactive-child legend {
            padding: 0 8px;
            font-weight: 700;
        }

        .interactive-child textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5d1;
            border-radius: 10px;
            font: inherit;
            resize: vertical;
        }

        .interactive-child .answer-list {
            margin-top: 12px;
        }
    </style>
@endpush

@section('content')
    @php
        $dangThi = $lanThi->trangthai === 'in_progress';
        $tongSoGiay = max(1, (int) $deThi->thoigian);
        $phanTram = max(0, min(100, $soGiayConLai / $tongSoGiay * 100));
        $soCauDaTraLoi = $dsCauDaCap->where('datraloi', 1)->count();

        $laInteractive = $cauHoi && in_array($cauHoi->maloai, [
            'interactive_reading',
            'interactive_listening',
        ], true);

        $laCauCuoi = $soCauHopLeConLai === 0 && $cauSau === null;

        $payloadCu = json_decode(old('noidung', $dapAnDaNop->noidung ?? ''), true);
        $dapAnCu = is_array($payloadCu)
            && is_array($payloadCu['answers'] ?? null)
                ? $payloadCu['answers']
                : [];
    @endphp

    <div class="exam-page">
        <div class="exam-topbar">
            <a href="{{ route('mock-test.index') }}" class="back-link">
                Danh sách đề thi
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

        <header class="exam-heading">
            <div class="exam-heading-meta">
                <span class="eyebrow">LUYỆN THI TRỰC TUYẾN</span>
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
                    {{ $deThi->kieu === 'adaptive'
                        ? 'Đề thích ứng'
                        : 'Đề cố định' }}
                </span>
                <span>•</span>
                <span class="progress-pill">
                    Đã trả lời: {{ $soCauDaTraLoi }}/{{ $tongSoCau }} câu
                </span>
            </div>
        </header>

        @if (session('success'))
            <div class="exam-notification" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="exam-notification exam-notification--error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="exam-layout">
            <div class="exam-left">
                <section class="glass-card question-card" id="question-area">
                    @if (!$dangThi)
                        <div class="ready-state-box">
                            <span class="eyebrow">BÀI THI ĐÃ KẾT THÚC</span>
                            <h2>
                                {{ $lanThi->trangthai === 'abandoned'
                                    ? 'Bạn đã bỏ dở bài thi'
                                    : 'Bạn đã hoàn thành bài thi' }}
                            </h2>
                            <p>Bạn không thể tiếp tục trả lời trong lần thi này.</p>

                            @if ($lanThi->trangthai === 'completed')
                                <a href="{{ route('mock-test.result', ['id' => $lanThi->id]) }}"
                                   class="nav-btn nav-btn-primary">
                                    Xem kết quả
                                </a>
                            @else
                                <a href="{{ route('mock-test.index') }}"
                                   class="nav-btn nav-btn-primary">
                                    Chọn đề thi khác
                                </a>
                            @endif
                        </div>

                    @elseif ($cauDuocCap && $cauHoi)
                        <div class="question-top-meta">
                            <div class="question-code-group">
                                <span class="small-pill">
                                    Question {{ $cauDuocCap->thutu }}
                                </span>
                                <span class="small-pill">{{ $cauHoi->tenloai }}</span>
                            </div>
                        </div>

                        <div class="question-content">
                            <div class="question-content-box">{{ trim($cauHoi->noidung) }}</div>

                            @if ($cauHoi->anh)
                                <img src="{{ asset(ltrim($cauHoi->anh, '/')) }}"
                                     alt="Hình minh họa câu hỏi"
                                     class="question-image">
                            @endif

                            @if ($cauHoi->audio)
                                <audio controls preload="metadata" class="question-audio">
                                    <source src="{{ asset(ltrim($cauHoi->audio, '/')) }}">
                                    Trình duyệt không hỗ trợ âm thanh.
                                </audio>
                            @endif
                        </div>

                        <form method="POST" enctype="multipart/form-data"
                              action="{{ $laCauCuoi
                                  ? route('mock-test.finish', $lanThi->id)
                                  : route('mock-test.skip', $lanThi->id) }}"
                              class="answer-form"
                              id="mock-answer-form">
                            @csrf

                            <input type="hidden"
                                   name="lanthicauhoi_id"
                                   value="{{ $cauDuocCap->id }}">

                            @if ($cauDuocCap->datraloi)
                                <p class="exam-notification">Câu này đã được nộp. Bạn có thể xem lại đáp án, không thể nộp lại.</p>
                            @endif
                            <fieldset class="answer-fields" @disabled($cauDuocCap->datraloi)>
                            @if ($laInteractive)
                                <noscript>
                                    <p role="alert">
                                        Cần bật JavaScript để gửi đáp án câu hỏi con.
                                    </p>
                                </noscript>

                                <input type="hidden"
                                       name="noidung"
                                       class="interactive-payload"
                                       value="{{ old('noidung', $dapAnDaNop->noidung ?? '') }}">

                                @forelse ($cauHoiCon as $con)
                                    @php
                                        $cacLuaChon = $luaChonCon->get($con->id, collect());
                                        $giaTriCu = $dapAnCu[$con->id] ?? '';
                                    @endphp

                                    <fieldset class="interactive-child"
                                              data-interactive-child="{{ $con->id }}">
                                        <legend>Subquestion {{ $loop->iteration }}</legend>
                                        <p>{{ $con->noidung }}</p>

                                        @if ($cacLuaChon->isNotEmpty())
                                            <div class="answer-list">
                                                @foreach ($cacLuaChon as $lua)
                                                    <label class="answer-item">
                                                        <input type="radio"
                                                               name="interactive_choice_{{ $con->id }}"
                                                               value="{{ $lua->id }}"
                                                               data-interactive-answer
                                                               @checked((string) $giaTriCu === (string) $lua->id)>

                                                        <span class="answer-item-inner">
                                                            <span class="answer-letter">
                                                                {{ chr(65 + $loop->index) }}
                                                            </span>
                                                            <span class="answer-text">
                                                                {{ $lua->noidung }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <textarea rows="4"
                                                      maxlength="2000"
                                                      data-interactive-answer
                                                      aria-label="Trả lời câu con {{ $loop->iteration }}"
                                                      placeholder="Nhập câu trả lời...">{{ $giaTriCu }}</textarea>
                                        @endif
                                    </fieldset>
                                @empty
                                    <p role="alert">
                                        Câu hỏi chưa có dữ liệu câu con.
                                        Bạn có thể bỏ qua và báo người quản lý đề.
                                    </p>
                                @endforelse

                            @elseif ($luaChon->isNotEmpty())
                                <div class="answer-list">
                                    <p class="field-label">CHỌN ĐÁP ÁN CỦA BẠN</p>

                                    @foreach ($luaChon as $item)
                                        <label class="answer-item">
                                            <input type="radio"
                                                   name="luachon_id"
                                                   value="{{ $item->id }}"
                                                   @checked(old('luachon_id', $dapAnDaNop->luachon_id ?? null) == $item->id)>

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

                            @elseif ($cauHoi->kynang === 'speaking')
                                <div class="text-answer-box">
                                    <label for="audio" class="field-label">FILE GHI ÂM CÂU TRẢ LỜI</label>
                                    @if (!empty($dapAnDaNop->audio))
                                        <audio controls preload="metadata" src="{{ route('practice.ai.audio', $dapAnDaNop->id) }}"></audio>
                                    @endif
                                    <input type="file" id="audio" name="audio" class="mock-audio-upload"
                                           aria-describedby="mock-audio-upload-help"
                                           accept=".mp3,.wav,.m4a,.ogg,.webm,audio/*">
                                    <p id="mock-audio-upload-help">MP3, WAV, M4A, OGG hoặc WebM, tối đa 10 MB.</p>
                                </div>
                            @else
                                <div class="text-answer-box">
                                    <label for="noidung" class="field-label">
                                        CÂU TRẢ LỜI CỦA BẠN
                                    </label>
                                    <textarea id="noidung"
                                              name="noidung"
                                              rows="6"
                                              maxlength="10000"
                                              placeholder="Nhập câu trả lời...">{{ old('noidung', $dapAnDaNop->noidung ?? '') }}</textarea>
                                </div>
                            @endif

                            <div class="question-nav-actions">
                                <div class="nav-left">
                                    @if ($cauTruoc)
                                        <a href="{{ route('mock-test.show', [
                                                'id' => $lanThi->id,
                                                'cau' => $cauTruoc->id,
                                            ]) }}"
                                           class="nav-btn nav-btn-secondary">
                                            Câu trước
                                        </a>
                                    @else
                                        <button type="button"
                                                class="nav-btn nav-btn-disabled"
                                                disabled>
                                            Câu trước
                                        </button>
                                    @endif
                                </div>

                                <div class="nav-right">
                                    @if ($laCauCuoi)
                                        <a href="#confirm-finish"
                                           class="nav-btn nav-btn-primary">
                                            Nộp bài
                                        </a>
                                    @else
                                        <button type="submit"
                                                class="nav-btn nav-btn-primary">
                                            Câu sau
                                        </button>
                                    @endif
                                </div>
                            </div>
                            </fieldset>
                        </form>
                        @if ($cauDuocCap->datraloi)
                            <a class="nav-btn" href="{{ route('mock-test.show', $lanThi->id) }}">Tiếp tục làm bài</a>
                        @endif

                    @elseif ($soCauHopLeConLai === 0)
                        <div class="ready-state-box">
                            <span class="eyebrow">TRẠNG THÁI BÀI THI</span>
                            <h2>
                                {{ $soCauDaCap > 0
                                    ? 'Đã cấp hết câu hỏi'
                                    : 'Đề thi chưa có câu hỏi hợp lệ' }}
                            </h2>
                            <p>Kiểm tra phiếu trả lời trước khi xác nhận nộp bài.</p>
                        </div>

                    @else
                        <div class="ready-state-box">
                            <span class="eyebrow">BẮT ĐẦU BÀI THI</span>
                            <h2>Bạn đã sẵn sàng làm bài?</h2>
                            <p>Thời gian làm bài đã bắt đầu được tính.</p>

                            <form method="POST"
                                  action="{{ route('mock-test.question.next', $lanThi->id) }}">
                                @csrf
                                <button type="submit" class="start-btn">
                                    Bắt đầu làm bài
                                </button>
                            </form>
                        </div>
                    @endif
                </section>

                <section class="glass-card action-card">
                    <div>
                        <h3>THAO TÁC BÀI THI</h3>
                        <p>Kiểm tra câu trả lời hoặc kết thúc bài thi.</p>
                    </div>

                    <div class="action-buttons">
                        @if ($dangThi)
                            <a href="#confirm-finish"
                               class="nav-btn nav-btn-primary">
                                Xác nhận nộp bài
                            </a>

                            <button type="button" class="nav-btn nav-btn-danger-outline"
                                    aria-haspopup="dialog"
                                    onclick="document.getElementById('abandon-dialog').showModal()">
                                Bỏ dở bài thi
                            </button>

                            <dialog id="abandon-dialog" class="abandon-dialog"
                                    aria-labelledby="abandon-title" aria-describedby="abandon-message">
                                <h2 id="abandon-title">Bỏ dở bài thi?</h2>
                                <p id="abandon-message">Lần thi sẽ kết thúc và không được tính điểm.</p>

                                <div class="abandon-dialog-actions">
                                    <form method="dialog">
                                        <button type="submit" class="btn btn-outline" autofocus>Quay lại</button>
                                    </form>

                                    <form method="POST" action="{{ route('mock-test.abandon', $lanThi->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Xác nhận bỏ dở</button>
                                    </form>
                                </div>
                            </dialog>
                        @else
                            <a href="{{ route('mock-test.index') }}"
                               class="nav-btn nav-btn-secondary">
                                Danh sách đề thi
                            </a>
                        @endif
                    </div>
                </section>
            </div>

            <aside class="exam-right">
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

                        <p class="timer-note">Thời gian làm bài còn lại</p>

                        <div class="timer-bar">
                            <div id="mock-timer-progress"
                                 style="width: {{ $phanTram }}%"></div>
                        </div>

                        <div class="timer-deadline">
                            <div>
                                <span>Hết hạn lúc:</span>
                                <strong>{{ $hanThi->format('H:i:s') }}</strong>
                            </div>
                            <div>
                                <span>Ngày hết hạn:</span>
                                <strong>{{ $hanThi->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="glass-card answer-sheet-card">
                    <div class="sheet-head">
                        <h3>PHIẾU TRẢ LỜI</h3>
                        <span class="sheet-progress">
                            {{ $soCauDaTraLoi }}/{{ $tongSoCau }}
                        </span>
                    </div>

                    <div class="sheet-grid">
                        @foreach ($phieuTraLoi as $item)
                            @if ($dangThi && ($item['lanthicauhoi_id'] || $deThi->kieu === 'fixed'))
                                <a href="{{ route('mock-test.show', [
                                        'id' => $lanThi->id,
                                        'cau' => $item['lanthicauhoi_id'],
                                        'so' => $item['lanthicauhoi_id'] ? null : $item['thutu'],
                                    ]) }}"
                                   class="sheet-item {{ $item['status'] }}"
                                   @if ($cauDuocCap && (int) $cauDuocCap->thutu === $item['thutu']) aria-current="page" @endif
                                   title="Question {{ $item['thutu'] }}">
                                    {{ $item['thutu'] }}
                                </a>
                            @else
                                <span class="sheet-item {{ $item['status'] }}"
                                      title="Question {{ $item['thutu'] }}">
                                    {{ $item['thutu'] }}
                                </span>
                            @endif
                        @endforeach
                    </div>

                    <div class="sheet-legend">
                        <span><i class="legend-dot answered"></i> Đã trả lời</span>
                        <span><i class="legend-dot empty"></i> Chưa làm</span>
                        <span><i class="legend-dot seen"></i> Đã cấp</span>
                        <span><i class="legend-dot current"></i> Đang xem</span>
                    </div>
                </section>

                <section class="glass-card note-card">
                    <h3>Lưu ý khi làm bài</h3>
                    <p>Nhấn Câu sau để lưu đáp án đang nhập và chuyển tiếp.</p>
                    <p>
                        Nhấn Câu trước hoặc ô số chỉ chuyển trang,
                        không lưu nội dung chưa gửi.
                    </p>
                    <p>
                        Nút xác nhận nộp bài sẽ gửi cả đáp án đang nhập.
                        Nội dung chưa gửi khi hết giờ không được ghi nhận.
                    </p>
                </section>
            </aside>
        </div>

        @if ($dangThi)
            <div id="confirm-finish" class="finish-overlay">
                <div class="finish-modal"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="finish-title">
                    <h2 id="finish-title">Xác nhận nộp bài</h2>

                    <p>
                        Đáp án đang nhập sẽ được gửi cùng bài thi.
                        Các câu chưa trả lời được tính là bỏ trống.
                    </p>

                    @if ($cauChuaTraLoi->isNotEmpty())
                        <p>
                            Câu chưa ghi nhận đáp án:
                            {{ $cauChuaTraLoi->pluck('thutu')->implode(', ') }}.
                        </p>
                    @endif

                    @if ($soCauHopLeConLai > 0)
                        <p>Còn {{ $soCauHopLeConLai }} câu chưa được cấp.</p>
                    @endif

                    <div class="finish-actions">
                        <a href="#question-area" class="nav-btn nav-btn-secondary">
                            Quay lại
                        </a>

                        @if ($cauDuocCap && $cauHoi)
                            <button type="submit"
                                    form="mock-answer-form"
                                    formaction="{{ route('mock-test.finish', $lanThi->id) }}"
                                    name="xac_nhan_bo_trong"
                                    value="1"
                                    class="nav-btn nav-btn-primary">
                                Nộp bài thi
                            </button>
                        @else
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
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if ($dangThi)
        <script>
            (() => {
                const form = document.getElementById('mock-answer-form');
                const payload = form?.querySelector('.interactive-payload');

                if (form && payload) {
                    form.addEventListener('submit', (event) => {
                        const answers = {};
                        let hasAnswer = false;

                        form.querySelectorAll('[data-interactive-child]')
                            .forEach((child) => {
                                const input = child.querySelector(
                                    'input[data-interactive-answer]:checked, ' +
                                    'textarea[data-interactive-answer]'
                                );

                                const value = input?.value.trim() ?? '';

                                answers[child.dataset.interactiveChild] = value;

                                if (value !== '') {
                                    hasAnswer = true;
                                }
                            });

                        payload.value = hasAnswer
                            ? JSON.stringify({ version: 1, answers })
                            : '';

                        if (payload.value.length > 10000) {
                            event.preventDefault();
                            alert('Tổng nội dung trả lời quá dài. Vui lòng rút gọn.');
                        }
                    });
                }

                const timer = document.getElementById('mock-countdown');
                const progress = document.getElementById('mock-timer-progress');

                if (!timer) {
                    return;
                }

                const total = {{ $tongSoGiay }};
                const deadline = performance.now()
                    + Number(timer.dataset.seconds) * 1000;

                let interval;

                function updateTimer() {
                    const seconds = Math.max(
                        0,
                        Math.ceil((deadline - performance.now()) / 1000)
                    );

                    timer.textContent =
                        String(Math.floor(seconds / 60)).padStart(2, '0')
                        + ':'
                        + String(seconds % 60).padStart(2, '0');

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