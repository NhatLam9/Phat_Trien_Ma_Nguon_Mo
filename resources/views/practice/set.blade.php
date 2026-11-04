@extends('layouts.app')

@section('title', 'Làm bài thi ' . $set->code . ' - DET Practice')
@section('body_class', 'app-body mock-test-body')
@section('main_class', 'app-main--mock-exam')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
    <link rel="stylesheet" href="{{ asset('css/practice.css') }}">
    <style>
        .sheet-btn.has-answer {
            background-color: #00b86b !important;
            border-color: #00b86b !important;
            color: #ffffff !important;
            font-weight: 700;
        }
        .sheet-btn.is-active {
            outline: 3px solid #38bdf8;
            outline-offset: 2px;
            font-weight: 800;
        }
        .sheet-btn.is-flagged {
            position: relative;
        }
        .sheet-btn.is-flagged::after {
            content: "★";
            position: absolute;
            top: -6px;
            right: -4px;
            color: #f59e0b;
            font-size: 11px;
        }
        .option-label {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            background: #ffffff;
            margin-bottom: 12px;
        }
        .option-label:hover {
            border-color: #00b86b;
            background: #f8fafc;
        }
        .option-label input[type="radio"]:checked + .opt-badge + .opt-text {
            color: #00b86b;
            font-weight: 700;
        }
        .option-label.selected {
            border-color: #00b86b;
            background: #f0fdf4;
        }
    </style>
@endpush

@section('content')
<div class="exam-page">

    {{-- THANH ĐIỀU HƯỚNG TRÊN CÙNG --}}
    <div class="exam-topbar">
        <a href="{{ route('practice.select') }}" class="back-link" onclick="return confirmExit()">
            ← Danh sách mã đề
        </a>

        <div class="exam-breadcrumb">
            <span>LUYỆN THI DET</span>
            <span>/</span>
            <span>BÀI LUYỆN TẬP</span>
            <span>/</span>
            <span class="pill">MÃ ĐỀ {{ $set->code }}</span>
        </div>

        <div class="exam-status-pill">
            <span class="dot"></span>
            Đang làm bài
        </div>
    </div>

    {{-- THÔNG TIN MÃ ĐỀ THI --}}
    <header class="exam-heading">
        <div class="exam-heading-meta">
            <span class="eyebrow">
                LUYỆN THI TRỰC TUYẾN • CẤU TRÚC ĐỀ THEO ĐỘ KHÓ
            </span>
            <span class="dot-sep">•</span>
            <span class="meta-tag">Cấp độ {{ $set->dokho }}/10 ({{ $set->mucdo }})</span>
            <span class="dot-sep">•</span>
            <span class="meta-tag">Kỹ năng {{ ucfirst($set->kynang) }}</span>
        </div>

        <h1 class="exam-title">{{ $set->ten }}</h1>

        <div class="exam-sub">
            <span>Mã đề: <strong>{{ $set->code }}</strong></span>
            <span class="sep">•</span>
            <span>Tiến độ: <strong id="progress-indicator">1/{{ count($questions) }} câu hỏi</strong></span>
            <span class="sep">•</span>
            <span>Điểm tối đa: <strong>{{ $set->tongdiem }} điểm</strong></span>
        </div>
    </header>

    {{-- BỐ CỤC 2 CỘT CHÍNH (GIỐNG HỆT FORMAT ĐỀ THI DET) --}}
    <div class="exam-layout">

        {{-- CỘT TRÁI: KHU VỰC CÂU HỎI VÀ ĐIỀU HƯỚNG LÀM BÀI --}}
        <main class="exam-main">
            <form id="practice-set-form" method="POST" action="{{ route('practice.set.submit', $set->code) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="time_spent" id="time_spent" value="0">
                <input type="hidden" name="is_timeout" id="is_timeout" value="0">

                @foreach($questions as $index => $q)
                    <div class="question-pane" id="pane-{{ $index }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
                        <div class="glass-card" style="padding: 26px; border-radius: 16px; margin-bottom: 20px;">
                            {{-- Header câu hỏi: Số câu, Phân loại, Đánh dấu cờ --}}
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-weight: 800; color: #00b86b; background: #e6f9f0; padding: 4px 10px; border-radius: 6px; font-size: 13px;">
                                        CÂU {{ sprintf('%02d', $index + 1) }}/{{ sprintf('%02d', count($questions)) }}
                                    </span>
                                    <span class="badge {{ !empty($q->loai_kynang) ? 'badge-' . $q->loai_kynang : 'badge-user' }}">
                                        {{ $q->loai_ten ?? 'Duolingo English Test' }}
                                    </span>
                                    <span style="font-size: 13px; color: #64748b;">
                                        Độ khó: {{ $q->dokho ?? $set->dokho }}/10
                                    </span>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline" onclick="toggleFlag({{ $index }}, this)" style="font-size: 12px; padding: 4px 10px;">
                                    ☆ Đánh dấu xem lại
                                </button>
                            </div>

                            {{-- Nội dung đề bài --}}
                            <div style="margin-bottom: 24px;">
                                <div style="font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase;">
                                    NỘI DUNG CÂU HỎI:
                                </div>
                                <div style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.6; background: #f8fafc; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                    {{ $q->noidung }}
                                </div>

                                {{-- Xử lý thông minh hình ảnh đính kèm nếu có --}}
                                @if(!empty($q->anh))
                                    @php
                                        $cleanImg = ltrim(str_replace('\\', '/', $q->anh), '/');
                                        $imgUrl = file_exists(public_path($cleanImg)) ? asset($cleanImg) : (file_exists(public_path('images/questions/' . basename($cleanImg))) ? asset('images/questions/' . basename($cleanImg)) : asset($cleanImg));
                                    @endphp
                                    <div style="text-align: center; margin: 20px 0;">
                                        <img src="{{ $imgUrl }}" alt="Question Image" style="max-width: 100%; max-height: 360px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    </div>
                                @endif

                                {{-- Xử lý thông minh âm thanh audio đính kèm nếu có --}}
                                @if(!empty($q->audio))
                                    @php
                                        $cleanAudio = ltrim(str_replace('\\', '/', $q->audio), '/');
                                        $audUrl = file_exists(public_path($cleanAudio)) ? asset($cleanAudio) : (file_exists(public_path('audio/questions/' . basename($cleanAudio))) ? asset('audio/questions/' . basename($cleanAudio)) : asset($cleanAudio));
                                        $ext = strtolower(pathinfo(parse_url($audUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                                        $mime = $ext === 'wav' ? 'audio/wav' : ($ext === 'ogg' ? 'audio/ogg' : 'audio/mpeg');
                                    @endphp
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 12px; margin: 18px 0; text-align: center;">
                                        <p style="font-size: 13px; color: #475569; margin-bottom: 8px; font-weight: 700;">🎧 Nghe đoạn âm thanh câu hỏi:</p>
                                        <audio controls preload="auto" style="width: 100%; max-width: 480px;">
                                            <source src="{{ $audUrl }}" type="{{ $mime }}">
                                            <source src="{{ $audUrl }}">
                                            Trình duyệt của bạn không hỗ trợ phát âm thanh trực tiếp.
                                        </audio>
                                    </div>
                                @endif
                            </div>

                            {{-- KHU VỰC CHỌN HOẶC NHẬP ĐÁP ÁN --}}
                            <div style="margin-bottom: 10px;">
                                <div style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 12px; text-transform: uppercase;">
                                    CHỌN ĐÁP ÁN CỦA BẠN:
                                </div>

                                @if(isset($q->luachon) && count($q->luachon) > 0)
                                    <div style="display: flex; flex-direction: column;">
                                        @foreach($q->luachon as $optIdx => $opt)
                                            <label class="option-label" id="opt-label-{{ $q->id }}-{{ $opt->id }}">
                                                <input type="radio" 
                                                       name="answers[{{ $q->id }}]" 
                                                       value="{{ $opt->id }}" 
                                                       style="accent-color: #00b86b; width: 18px; height: 18px;" 
                                                       onchange="onOptionSelected({{ $index }}, {{ $q->id }}, {{ $opt->id }})">
                                                <span class="opt-badge" style="font-weight: 800; color: #00b86b; width: 22px; font-size: 15px;">
                                                    {{ chr(65 + $optIdx) }}.
                                                </span>
                                                <span class="opt-text" style="font-size: 15px; color: #1e293b; line-height: 1.4;">
                                                    {{ $opt->noidung }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif(!empty($q->loai_kynang) && $q->loai_kynang === 'speaking')
                                    <div class="form-group">
                                        <input type="text" 
                                               name="answers[{{ $q->id }}]" 
                                               class="form-control" 
                                               placeholder="Gõ phiên âm bài nói hoặc ghi chú câu trả lời..." 
                                               oninput="onInputTextAnswer({{ $index }}, this.value)"
                                               style="font-size: 15px; padding: 12px 14px;">
                                    </div>
                                @else
                                    <div class="form-group">
                                        <textarea name="answers[{{ $q->id }}]" 
                                                  rows="5" 
                                                  class="form-control" 
                                                  placeholder="Nhập câu trả lời bằng tiếng Anh tại đây..." 
                                                  oninput="onInputTextAnswer({{ $index }}, this.value)"
                                                  style="font-family: inherit; font-size: 15px; line-height: 1.6;"></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- CÁC NÚT ĐIỀU HƯỚNG CÂU HỎI --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
                            @if($index > 0)
                                <button type="button" class="btn btn-outline" onclick="goToQuestion({{ $index - 1 }})" style="padding: 10px 22px;">
                                    ← Câu trước
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div style="display: flex; gap: 10px;">
                                @if($index < count($questions) - 1)
                                    <button type="button" class="btn btn-primary" onclick="goToQuestion({{ $index + 1 }})" style="padding: 10px 26px;">
                                        Câu sau →
                                    </button>
                                @endif

                                <button type="submit" class="btn btn-success" onclick="return confirmSubmit()" style="padding: 10px 24px; font-weight: 700; background: #00b86b; border-color: #00b86b; color: #fff;">
                                    Nộp bài làm ✓
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </main>

        {{-- CỘT PHẢI: BỘ ĐẾM NGƯỢC THỜI GIAN & PHIẾU TRẢ LỜI (GIỐNG HỆT ẢNH CHỤP MÀN HÌNH) --}}
        <aside class="exam-right">

            {{-- 1. BỘ ĐẾM NGƯỢC THỜI GIAN (COUNTDOWN TIMER) --}}
            <section class="glass-card timer-card">
                <div class="timer-title">
                    <span>THỜI GIAN CÒN LẠI</span>
                    <span class="dot"></span>
                </div>

                <div class="timer-number" id="practice-countdown" data-seconds="{{ $set->thoigian }}">
                    {{ sprintf('%02d:%02d', intdiv($set->thoigian, 60), $set->thoigian % 60) }}
                </div>

                <p class="timer-note">
                    Thời gian làm bài còn lại
                </p>

                <div class="timer-bar">
                    <div id="practice-timer-progress" style="width: 100%;"></div>
                </div>

                <div class="timer-deadline">
                    <div>
                        <span>Hết hạn lúc:</span>
                        <strong id="deadline-time">
                            {{ now()->addSeconds($set->thoigian)->format('H:i:s') }}
                        </strong>
                    </div>

                    <div>
                        <span>Ngày làm bài:</span>
                        <strong>
                            {{ now()->format('d/m/Y') }}
                        </strong>
                    </div>
                </div>
            </section>

            {{-- 2. PHIẾU TRẢ LỜI CÂU HỎI --}}
            <section class="glass-card sheet-card" style="margin-top: 18px;">
                <div class="sheet-head">
                    <h3 class="sheet-title">PHIẾU TRẢ LỜI</h3>
                    <span class="sheet-counter" id="answered-counter">0/{{ count($questions) }}</span>
                </div>

                <div class="sheet-grid" style="grid-template-columns: repeat(auto-fill, minmax(42px, 1fr)); gap: 8px;">
                    @foreach($questions as $i => $q)
                        <button type="button" 
                                class="sheet-btn {{ $i === 0 ? 'is-active' : '' }}" 
                                id="sheet-btn-{{ $i }}" 
                                onclick="goToQuestion({{ $i }})"
                                title="Câu {{ $i + 1 }}">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="sheet-legend" style="margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #00b86b;"></span>
                        <span>Đã trả lời</span>
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot" style="background: #ffffff; border: 1px solid #cbd5e1;"></span>
                        <span>Chưa làm</span>
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot" style="background: #38bdf8;"></span>
                        <span>Đang xem</span>
                    </div>

                    <div class="legend-item">
                        <span style="color: #f59e0b; font-size: 14px;">★</span>
                        <span>Đánh dấu</span>
                    </div>
                </div>
            </section>

            {{-- 3. HƯỚNG DẪN QUAN TRỌNG KHI LÀM BÀI --}}
            <section class="glass-card" style="margin-top: 18px; padding: 18px; font-size: 13px; line-height: 1.6; color: #475569;">
                <h4 style="font-size: 13px; font-weight: 800; color: #1e293b; margin-bottom: 8px; text-transform: uppercase;">
                    📌 Lưu ý quan trọng khi làm bài
                </h4>
                <ul style="padding-left: 18px; margin: 0;">
                    <li style="margin-bottom: 6px;">Chọn đáp án để hệ thống tự động ghi nhận vào <strong>Phiếu trả lời</strong>.</li>
                    <li style="margin-bottom: 6px;">Khi <strong>hết thời gian làm bài</strong>, hệ thống sẽ tự động nộp bài và chấm điểm ngay lập tức.</li>
                    <li>Bạn có thể nhấp vào các ô số trong Phiếu trả lời để chuyển câu hỏi nhanh chóng.</li>
                </ul>
            </section>

        </aside>

    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const totalQuestions = {{ count($questions) }};
    let currentIndex = 0;
    const answeredMap = {};

    // 1. Chuyển đổi giữa các câu hỏi
    window.goToQuestion = function(index) {
        if (index < 0 || index >= totalQuestions) return;

        // Ẩn câu hiện tại, hiện câu đích
        document.getElementById('pane-' + currentIndex).style.display = 'none';
        document.getElementById('pane-' + index).style.display = 'block';

        // Cập nhật trạng thái active trên Phiếu trả lời
        document.getElementById('sheet-btn-' + currentIndex).classList.remove('is-active');
        document.getElementById('sheet-btn-' + index).classList.add('is-active');

        currentIndex = index;

        // Cập nhật tiến độ trên Header
        document.getElementById('progress-indicator').textContent = (currentIndex + 1) + '/' + totalQuestions + ' câu hỏi';
    };

    // 2. Ghi nhận khi người dùng chọn đáp án trắc nghiệm
    window.onOptionSelected = function(questionIndex, questionId, optionId) {
        // Xóa class selected khỏi các option khác của câu này
        const labels = document.querySelectorAll(`[id^="opt-label-${questionId}-"]`);
        labels.forEach(l => l.classList.remove('selected'));

        const currentLabel = document.getElementById(`opt-label-${questionId}-${optionId}`);
        if (currentLabel) currentLabel.classList.add('selected');

        answeredMap[questionIndex] = true;
        updateSheetState(questionIndex);
    };

    // 3. Ghi nhận khi người dùng gõ văn bản
    window.onInputTextAnswer = function(questionIndex, value) {
        if (value && value.trim().length > 0) {
            answeredMap[questionIndex] = true;
        } else {
            delete answeredMap[questionIndex];
        }
        updateSheetState(questionIndex);
    };

    // Cập nhật giao diện Phiếu trả lời
    function updateSheetState(index) {
        const btn = document.getElementById('sheet-btn-' + index);
        if (answeredMap[index]) {
            btn.classList.add('has-answer');
        } else {
            btn.classList.remove('has-answer');
        }

        const count = Object.keys(answeredMap).length;
        document.getElementById('answered-counter').textContent = count + '/' + totalQuestions;
    }

    // Đánh dấu cờ xem lại
    window.toggleFlag = function(index, btn) {
        const sheetBtn = document.getElementById('sheet-btn-' + index);
        sheetBtn.classList.toggle('is-flagged');
        if (sheetBtn.classList.contains('is-flagged')) {
            btn.textContent = '★ Đã đánh dấu xem lại';
            btn.style.color = '#f59e0b';
            btn.style.borderColor = '#f59e0b';
        } else {
            btn.textContent = '☆ Đánh dấu xem lại';
            btn.style.color = '';
            btn.style.borderColor = '';
        }
    };

    // Xác nhận khi rời trang
    window.confirmExit = function() {
        return confirm('Bạn có chắc chắn muốn rời bài thi? Tiến trình hiện tại sẽ không được lưu.');
    };

    // Xác nhận khi nộp bài
    window.confirmSubmit = function() {
        const answeredCount = Object.keys(answeredMap).length;
        const msg = answeredCount < totalQuestions 
            ? `Bạn mới trả lời ${answeredCount}/${totalQuestions} câu hỏi.\nBạn có chắc chắn muốn nộp bài ngay bây giờ?`
            : `Bạn đã hoàn thành ${answeredCount}/${totalQuestions} câu hỏi.\nBạn có muốn nộp bài để chấm điểm không?`;
        return confirm(msg);
    };

    // =========================================================================
    // 4. BỘ ĐẾM NGƯỢC THỜI GIAN & TỰ ĐỘNG CHẤM BÀI KHI HẾT GIỜ (REQUIREMENT 2)
    // =========================================================================
    const timerEl = document.getElementById('practice-countdown');
    const progressEl = document.getElementById('practice-timer-progress');
    const formEl = document.getElementById('practice-set-form');
    const timeSpentEl = document.getElementById('time_spent');
    const isTimeoutEl = document.getElementById('is_timeout');

    if (timerEl && formEl) {
        const totalDuration = Number(timerEl.dataset.seconds) || 300;
        const startTime = Date.now();
        const endTime = startTime + totalDuration * 1000;

        let timerInterval = setInterval(function() {
            const now = Date.now();
            const elapsed = Math.floor((now - startTime) / 1000);
            const remaining = Math.max(0, Math.ceil((endTime - now) / 1000));

            if (timeSpentEl) timeSpentEl.value = elapsed;

            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (progressEl) {
                const percent = Math.min(100, Math.max(0, (remaining / totalDuration) * 100));
                progressEl.style.width = percent + '%';
            }

            // Đổi màu cảnh báo đỏ khi còn dưới 60 giây
            if (remaining <= 60) {
                timerEl.style.color = '#dc2626';
                if (progressEl) progressEl.style.background = '#dc2626';
            }

            // KHI HẾT THỜI GIAN: TỰ ĐỘNG CHẤM BÀI VÀ NỘP BÀI LẬP TỨC
            if (remaining <= 0) {
                clearInterval(timerInterval);
                if (isTimeoutEl) isTimeoutEl.value = '1';

                alert('⏱️ ĐÃ HẾT THỜI GIAN LÀM BÀI!\n\nHệ thống đang tự động nộp bài làm của bạn để chấm điểm và hiển thị kết quả.');
                formEl.submit();
            }
        }, 1000);
    }
})();
</script>
@endpush

