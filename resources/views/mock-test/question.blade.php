
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Phiên thi #{{ $lanThi->id }} | DET Practice
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/mock-test.css') }}"
    >

    {{-- MT04: Tu tai lai khi den han thi --}}
    @if ($lanThi->trangthai === 'in_progress')
        <meta
            http-equiv="refresh"
            content="{{ max(1, $soGiayConLai + 1) }}"
        >
    @endif
</head>

<body>
    {{-- Header --}}
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ url('/') }}" class="logo">
                <img
                    src="{{ asset('images/fox-logo.jpg') }}"
                    alt="DET Practice"
                    class="fox-logo"
                >

                DET <span>Practice</span>
            </a>

            <span class="header-status">
                Phiên thi #{{ $lanThi->id }}
            </span>
        </div>
    </header>

    <main class="container page">

        {{-- Gioi thieu bai thi --}}
        <div class="page-intro">
            <span class="eyebrow">
                DET PRACTICE · MOCK TEST
            </span>

            <h1>{{ $deThi->ten ?? 'Phiên thi thử' }}</h1>

            <p>
                Hình thức:
                {{ ($deThi->kieu ?? '') === 'adaptive'
                    ? 'Adaptive'
                    : 'Fixed' }}

                · Đã cấp {{ $soCauDaCap }}/{{ $soCauTrongDe }}
                câu hỏi
            </p>
        </div>

        {{-- MT04: Thoi gian con lai --}}
        @if ($lanThi->trangthai === 'in_progress')
            <section
                class="exam-timer"
                aria-label="Thời gian bài thi"
            >
                <div class="timer-information">
                    <span class="timer-label">
                        THỜI GIAN CÒN LẠI
                    </span>

                    <strong class="timer-value">
                        {{ sprintf(
                            '%02d:%02d',
                            intdiv($soGiayConLai, 60),
                            $soGiayConLai % 60
                        ) }}
                    </strong>

                    <span class="timer-hint">
                        Thời gian được cập nhật khi tải trang
                    </span>
                </div>

                <div class="timer-deadline">
                    <span>Hết hạn lúc</span>

                    <strong>
                        {{ $hanThi->format('H:i:s') }}
                    </strong>

                    <small>
                        {{ $hanThi->format('d/m/Y') }}
                    </small>
                </div>
            </section>
        @endif

        {{-- Thong bao tu session --}}
        @if (session('success'))
            <div
                class="alert alert-success"
                role="status"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Thong bao validation --}}
        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Noi dung phien thi --}}
        <section class="session-card" id="question-area">

            {{-- MT04 / MT08: Phien thi da bi dong --}}
            @if ($lanThi->trangthai === 'abandoned')

                <div class="abandoned-state">
                    @if ($biHetGio)
                        <span class="eyebrow">
                            MT04 · TIMEOUT
                        </span>

                        <h2>Bài thi đã hết thời gian</h2>

                        <p>
                            Phiên thi #{{ $lanThi->id }}
                            đã tự động kết thúc vì vượt quá
                            thời gian quy định.
                        </p>
                    @else
                        <span class="eyebrow">
                            MT08 · ABANDONED
                        </span>

                        <h2>Bài thi đã bỏ dở</h2>

                        <p>
                            Phiên thi #{{ $lanThi->id }}
                            đã kết thúc theo yêu cầu của bạn.
                        </p>
                    @endif

                    <div class="session-summary">
                        <div class="summary-item">
                            <span>Thời gian bắt đầu</span>

                            <strong>
                                {{ $lanThi->batdau
                                    ? $lanThi->batdau->format('d/m/Y H:i:s')
                                    : 'Chưa ghi nhận' }}
                            </strong>
                        </div>

                        <div class="summary-item">
                            <span>Thời gian kết thúc</span>

                            <strong>
                                {{ $lanThi->ketthuc
                                    ? $lanThi->ketthuc->format('d/m/Y H:i:s')
                                    : 'Chưa ghi nhận' }}
                            </strong>
                        </div>
                    </div>

                    <p class="state-description">
                        Phiên thi này không được tính điểm.
                        Bạn có thể chọn một đề thi khác để bắt đầu.
                    </p>

                    <a
                        href="{{ route('mock-test.index') }}"
                        class="btn btn-primary"
                    >
                        Chọn bài thi khác →
                    </a>
                </div>

            {{-- Bai thi da hoan thanh --}}
            @elseif ($lanThi->trangthai === 'completed')

                <div class="session-notice">
                    <span class="eyebrow">
                        MOCK TEST · COMPLETED
                    </span>

                    <h2>Bài thi đã hoàn thành</h2>

                    <p>
                        Bạn đã hoàn thành phiên thi này.
                        Không thể tiếp tục nhận câu hỏi.
                    </p>

                    <p>
                        Điểm số và báo cáo chi tiết sẽ được
                        tích hợp trong chức năng MT06.
                    </p>
                </div>

            {{-- MT02: Cau hoi dang lam --}}
            @elseif ($cauDuocCap && $cauHoi)

                <div class="session-heading">
                    <div>
                        <span class="eyebrow">
                            CÂU HỎI {{ $cauDuocCap->thutu }}
                        </span>

                        <h2>
                            {{ $cauHoi->tenloai }}
                        </h2>
                    </div>

                    <span class="status-pill">
                        {{ ucfirst(
                            $cauHoi->kynang ?? 'Luyện tập'
                        ) }}
                    </span>
                </div>

                <div class="question-content">
                    <p class="question-label">
                        Nội dung câu hỏi
                    </p>

                    <div class="question-text">
                        {{ $cauHoi->noidung }}
                    </div>
                </div>

                {{-- Tai nguyen media: cho tich hop TV3 --}}
                @if ($cauHoi->anh)
                    <div class="media-note">
                        Câu hỏi có hình ảnh đính kèm.
                        Nội dung media sẽ được tích hợp
                        từ kho dữ liệu câu hỏi.
                    </div>
                @endif

                @if ($cauHoi->audio)
                    <div class="media-note">
                        Câu hỏi có âm thanh đính kèm.
                        Chức năng phát audio sẽ được
                        tích hợp từ kho dữ liệu câu hỏi.
                    </div>
                @endif

                <form method="POST"
                action="{{ route('mock-test.submit', $lanThi->id) }}"
                class="answer-form">

                @csrf

                <input type="hidden"
                    name="lanthicauhoi_id"
                    value="{{ $cauDuocCap->id }}">

                @if ($luaChon->isNotEmpty())

                    <fieldset class="answer-options">
                        <legend>Chọn đáp án của bạn</legend>

                        @foreach ($luaChon as $item)
                            <label class="answer-option">
                                <input type="radio"
                                    name="luachon_id"
                                    value="{{ $item->id }}"
                                    {{ old('luachon_id') == $item->id ? 'checked' : '' }}
                                    required>

                                <span class="option-letter">
                                    {{ $loop->iteration }}
                                </span>

                                <span class="option-content">
                                    {{ $item->noidung }}
                                </span>
                            </label>
                        @endforeach
                    </fieldset>

                @else

                    <div class="answer-text-group">
                        <label for="noidung">Câu trả lời của bạn</label>

                        <textarea
                            id="noidung"
                            name="noidung"
                            rows="6"
                            maxlength="10000"
                            placeholder="Nhập câu trả lời..."
                            required>{{ old('noidung') }}</textarea>
                    </div>

                @endif

                <div class="answer-actions">
                    <button type="submit" class="btn btn-primary">
                        Nộp câu trả lời
                    </button>
                </div>
            </form>

            {{-- MT02 - Bo qua cau hoi --}}
            <form method="POST"
                action="{{ route('mock-test.skip', $lanThi->id) }}"
                class="skip-form">
                @csrf

                <input type="hidden"
                    name="lanthicauhoi_id"
                    value="{{ $cauDuocCap->id }}">

                <button type="submit" class="btn btn-dark">
                    Bỏ qua / Câu tiếp theo →
                </button>
            </form>

            {{-- Chua co cau hoi hop le --}}
            @elseif ($soCauHopLeConLai === 0)
                <div class="session-notice">
                    @if ($soCauDaCap > 0)
                        <span class="eyebrow">
                            MT05 · FINISH
                        </span>
                        <h2>Đã hoàn thành các câu hỏi</h2>
                        <p> Bạn có thể chốt và nộp bài thi. </p>
                    @else
                        <h2>Đề thi chưa có câu hỏi</h2>
                        <p>
                            Hiện không có câu hỏi hợp lệ.
                            Vui lòng chọn đề thi khác.
                        </p>
                    @endif
                </div>


            {{-- San sang cap cau hoi --}}
            @else

                <div class="session-notice">
                    <span class="eyebrow">
                        MOCK TEST · READY
                    </span>

                    <h2>Sẵn sàng nhận câu hỏi?</h2>

                    <p>
                        Hệ thống sẽ lựa chọn câu hỏi
                        tiếp theo dựa trên hình thức đề thi.
                    </p>

                    <form
                        method="POST"
                        action="{{ route(
                            'mock-test.question.next',
                            $lanThi->id
                        ) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Nhận câu hỏi →
                        </button>
                    </form>
                </div>
            @endif

            {{-- MT05: Xac nhan nop bai --}}
            @if ($lanThi->trangthai === 'in_progress')
                <div class="finish-section">
                    <a href="#confirm-finish"
                    class="btn btn-primary">
                        Chốt bài thi
                    </a>
                </div>

                <div id="confirm-finish" class="finish-overlay">
                    <div class="finish-modal" role="dialog"
                        aria-modal="true"
                        aria-labelledby="finish-title">

                        <h2 id="finish-title">Xác nhận nộp bài</h2>

                    @php
                        $soCauChuaCap = $soCauHopLeConLai;
                    @endphp

                    @if ($cauChuaTraLoi->count() > 0 || $soCauChuaCap > 0)

                        <p>
                            Bạn vẫn còn câu hỏi chưa hoàn thành.
                        </p>

                        @if ($cauChuaTraLoi->isNotEmpty())
                            <p>
                                Câu
                                <strong>{{ $cauChuaTraLoi->pluck('thutu')->implode(', ') }}</strong>
                                chưa chọn đáp án.
                            </p>
                        @endif

                        @if ($soCauChuaCap > 0)
                            <p>
                                Bạn còn <strong>{{ $soCauChuaCap }}</strong>
                                câu hỏi chưa được cấp.
                            </p>
                        @endif

                        <p>
                            Bạn vẫn muốn nộp bài?
                            Các câu chưa làm sẽ được tính là bỏ trống.
                        </p>


            @else
                <p>Bạn có chắc chắn muốn nộp bài thi?</p>
            @endif

            @if ($cauChuaTraLoi->isNotEmpty())
            <div class="question-links">
                @foreach ($cauChuaTraLoi as $item)
                    <a href="{{ route('mock-test.show', [
                        'id' => $lanThi->id,
                        'cau' => $item->id
                    ]) }}#question-area" class="btn btn-dark">
                        Câu {{ $item->thutu }}
                    </a>
                @endforeach
            </div>
            @endif
            <div class="finish-actions">

            @if ($cauChuaTraLoi->isNotEmpty())
                <a href="{{ route('mock-test.show', [
                    'id' => $lanThi->id,
                    'cau' => $cauChuaTraLoi->first()->id
                ]) }}#question-area" class="btn btn-dark">
                    Đi đến câu {{ $cauChuaTraLoi->first()->thutu }}
                </a>
            @elseif ($soCauHopLeConLai > 0)
                <form method="POST"
                    action="{{ route('mock-test.question.next', $lanThi->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-dark">
                        Nhận câu tiếp theo
                    </button>
                </form>
            @else
                <a href="#question-area" class="btn btn-dark">
                    Quay lại
                </a>
            @endif
                    <form
                        method="POST"
                        action="{{ route('mock-test.finish', $lanThi->id) }}">
                        @csrf
                        <input
                            type="hidden"
                            name="xac_nhan_bo_trong"
                            value="1">
                        <button type="submit" class="btn btn-primary">
                            Nộp bài
                        </button>
                    </form>
                </div>
                </div> {{-- Dong finish-modal --}}
                </div> {{-- Dong finish-overlay --}}
            @endif {{-- Dong dieu kien MT05 in_progress --}}

            {{-- MT08: Xac nhan bo do --}}
            @if ($lanThi->trangthai === 'in_progress')
                <div class="abandon-section">
                    <details class="abandon-confirm">
                        <summary class="btn btn-danger-outline">
                            Bỏ dở bài thi
                        </summary>

                        <div class="abandon-warning">
                            <h3>
                                Xác nhận bỏ dở bài thi?
                            </h3>

                            <p>
                                Bài thi sẽ kết thúc ngay sau khi
                                xác nhận. Bạn không thể tiếp tục
                                phiên thi này và kết quả sẽ
                                không được tính điểm.
                            </p>

                            <form
                                method="POST"
                                action="{{ route(
                                    'mock-test.abandon',
                                    $lanThi->id
                                ) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Xác nhận bỏ dở
                                </button>
                            </form>

                            <p class="abandon-cancel-hint">
                                Nhấn lại "Bỏ dở bài thi"
                                để đóng phần xác nhận.
                            </p>
                        </div>
                    </details>
                </div>
            @endif

            {{-- Dieu huong --}}
            <div class="session-actions">
                <a
                    href="{{ route('mock-test.index') }}"
                    class="btn btn-dark"
                >
                    ← Danh sách đề thi
                </a>
            </div>

        </section>
    </main>

    <footer class="site-footer">
        DET Practice
    </footer>
</body>
</html>
