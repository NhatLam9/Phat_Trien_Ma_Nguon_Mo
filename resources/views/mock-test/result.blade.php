@extends('layouts.app')

@section('title', 'Kết quả ' . $deThi->ten . ' | DET Practice')
@section('main_class', 'app-main--mock-result')
@section('body_class', 'mock-test-result-body')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test-result.css') }}?v=6">

    <style>
        .mr-q-btn--partial {
            color: #075985;
            background: #e0f2fe;
            border-color: #38bdf8;
        }

        .mr-result-status--partial {
            color: #075985;
            background: #e0f2fe;
        }

        .mr-legend-dot--partial {
            background: #38bdf8;
        }

        .mr-q-btn--graded {
            color: #5b21b6;
            background: #ede9fe;
            border-color: #a78bfa;
        }

        .mr-result-status--graded {
            color: #5b21b6;
            background: #ede9fe;
        }

        .mr-legend-dot--graded {
            background: #a78bfa;
        }

        .mr-interactive-review {
            margin-top: 16px;
        }

        .mr-interactive-review article {
            margin-bottom: 12px;
            padding: 14px;
            border: 1px solid var(--bento-line);
            border-radius: 12px;
        }

        .mr-interactive-review p,
        .mr-free-answer p,
        .mr-explanation p {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .mr-review-note {
            margin: 12px 0;
            color: #667085;
            font-size: .9rem;
        }
    </style>
@endpush

@section('content')
    <div class="mr-page">
        <div class="mr-topline">
            <div class="mr-crumbs">
                <a href="{{ route('mock-test.index') }}">Thoát</a>
                <span>/</span>
                <span>Luyện thi DET</span>
                <span>/</span>
                <strong>Kết quả</strong>
            </div>

            <div class="mr-statuses">
                <span class="mr-pill mr-pill--success">● ĐÃ HOÀN THÀNH</span>
                <span class="mr-pill mr-pill--neutral">
                    {{ $ketQua['da_cham_xong']
                        ? '✓ Không còn câu chờ chấm'
                        : '◷ Đang chờ chấm' }}
                </span>
            </div>
        </div>

        <div class="mr-heading">
            <div>
                <p class="mr-kicker">
                    BÀI THI THỬ
                    <span>
                        · {{ $deThi->kieu === 'adaptive' ? 'Thích ứng' : 'Cố định' }}
                    </span>
                </p>

                <h1>Kết quả {{ $deThi->ten }}</h1>

                <p class="mr-subtitle">
                    <span>
                        Nộp bài:
                        {{ $lanThi->ketthuc?->format('H:i · d/m/Y') ?? 'Chưa ghi nhận' }}
                    </span>
                    <span>
                        Thời gian: {{ intdiv($soGiayLamBai, 60) }} phút
                        {{ $soGiayLamBai % 60 }} giây
                    </span>
                </p>
            </div>

            <div class="mr-heading-actions">
                <button type="button"
                        class="mr-btn mr-btn--outline"
                        id="mr-share">
                    Chia sẻ
                </button>

                @if ($chungChi)
                    <a class="mr-btn mr-btn--primary" href="{{ route('certificate.show', ['id' => $chungChi->id]) }}">
                        Xem chứng chỉ
                    </a>
                @elseif ($coTheCapChungChi)
                    <form method="POST"
                          action="{{ route('certificate.issue', ['id' => $lanThi->id]) }}">
                        @csrf

                        <button class="mr-btn mr-btn--primary" type="submit">
                            Cấp / xem chứng chỉ
                        </button>
                    </form>
                @else
                    <span class="mr-pill mr-pill--neutral">
                        Chứng chỉ chưa sẵn sàng
                    </span>
                @endif
            </div>
        </div>

        <section class="mr-card mr-scoreboard" aria-label="Tổng quan kết quả">
            <div class="mr-score-grid">
                <div class="mr-primary-score">
                    <span class="mr-label">ĐIỂM THI THỬ</span>

                    <div class="mr-score">
                        <strong>{{ $lanThi->tongdiem ?? '—' }}</strong>
                        <span>/ 160 điểm</span>
                    </div>

                    <span class="mr-score-caption">
                        @if (!$ketQua['da_cham_xong'])
                            Điểm tạm · Còn câu chờ chấm
                        @elseif ($lanThi->tongdiem === null)
                            Chưa có đủ điểm tổng
                        @else
                            Quy đổi theo công thức của dự án
                        @endif
                    </span>
                </div>

                <div class="mr-accuracy">
                    <span class="mr-label">CÂU ĐÚNG HOÀN TOÀN</span>
                    <strong>
                        {{ $ketQua['so_cau_dung'] }} / {{ $tongSoCau }}
                    </strong>
                    <span>
                        Đúng một phần: {{ $ketQua['so_cau_dung_mot_phan'] }} câu
                    </span>
                </div>

                <div class="mr-assessment">
                    <strong>◈ Trạng thái kết quả</strong>

                    @if (!$ketQua['da_cham_xong'])
                        <p>
                            Còn {{ $ketQua['so_cau_cho_cham'] }} câu chờ chấm.
                            Điểm có thể thay đổi sau khi cập nhật kết quả.
                        </p>
                    @elseif ($lanThi->tongdiem === null)
                        <p>
                            Chưa có đủ điểm tổng.
                            Xem các điểm thành phần và chi tiết câu hỏi bên dưới.
                        </p>
                    @else
                        <p>
                            Đối chiếu bốn điểm thành phần và xem lại các câu
                            chưa đạt tối đa để chọn nội dung luyện tập.
                        </p>
                    @endif
                </div>
            </div>

            <div class="mr-stats">
                <div class="mr-stat">
                    <span class="mr-stat-icon mr-stat-icon--correct">✓</span>
                    <div>
                        <small>Đúng hoàn toàn</small>
                        <strong>{{ $ketQua['so_cau_dung'] }}</strong>
                    </div>
                </div>

                <div class="mr-stat">
                    <span class="mr-stat-icon mr-stat-icon--incorrect">×</span>
                    <div>
                        <small>Sai</small>
                        <strong>{{ $ketQua['so_cau_sai'] }}</strong>
                    </div>
                </div>

                <div class="mr-stat">
                    <span class="mr-stat-icon mr-stat-icon--skipped">–</span>
                    <div>
                        <small>Bỏ trống</small>
                        <strong>{{ $ketQua['so_cau_bo_trong'] }}</strong>
                    </div>
                </div>

                <div class="mr-stat">
                    <span class="mr-stat-icon mr-stat-icon--pending">◷</span>
                    <div>
                        <small>Chờ chấm</small>
                        <strong>{{ $ketQua['so_cau_cho_cham'] }}</strong>
                    </div>
                </div>
            </div>

            @if ($ketQua['so_cau_dung_mot_phan'] > 0)
                <p class="mr-review-note">
                    {{ $ketQua['so_cau_dung_mot_phan'] }} câu đúng một phần.
                    Điểm của các câu này đã được tính vào điểm bài thi.
                </p>
            @endif

            @if ($ketQua['so_cau_da_cham_khac'] > 0)
                <p class="mr-review-note">
                    {{ $ketQua['so_cau_da_cham_khac'] }} câu đã có điểm
                    nhưng không phân loại đúng/sai.
                </p>
            @endif
        </section>

        <div class="mr-columns">
            <div class="mr-left">
                <section class="mr-card mr-skills-section">
                    <div class="mr-section-title">
                        <h2>Điểm thành phần</h2>
                        <span>Thang 10–160</span>
                    </div>

                    <div class="mr-skills">
                        @foreach ($phanTichKyNang as $kyNang)
                            @php
                                $doDay = $kyNang['diem'] !== null
                                    ? max(0, min(100, ($kyNang['diem'] - 10) / 150 * 100))
                                    : 0;
                            @endphp

                            <div class="mr-skill">
                                <div class="mr-ring" style="--mr-p: {{ $doDay }}%">
                                    <span>{{ $kyNang['diem'] ?? '—' }}</span>
                                </div>

                                <strong>{{ $kyNang['ten'] }}</strong>

                                <small>
                                    @if ($kyNang['diem'] === null)
                                        Chưa có đủ điểm
                                    @elseif (!$ketQua['da_cham_xong'])
                                        Điểm tạm / 160
                                    @else
                                        Điểm / 160
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="mr-card mr-review" id="review">
                    <div class="mr-section-title">
                        <h2>
                            {{ $xemTatCa
                                ? 'Toàn bộ lời giải'
                                : sprintf('Question %d / %d', $cauSo, $tongSoCau) }}
                        </h2>
                        <span>
                            {{ $xemTatCa ? 'Tất cả câu đã cấp' : 'Xem lại câu hỏi' }}
                        </span>
                    </div>

                    <p class="mr-review-note">
                        Lời giải đối chiếu với đáp án hiện tại trong ngân hàng câu hỏi.
                        Điểm hiển thị là điểm đã được lưu của lần thi.
                    </p>

                    @forelse ($dsHienThi as $cau)
                        <article class="mr-review-item" id="cau-{{ $cau->thutu }}">
                            <div class="mr-review-header">
                                <strong>
                                    Question {{ $cau->thutu }} · {{ $cau->tenloai }}
                                </strong>

                                <span class="mr-result-status mr-result-status--{{ $cau->trangthai }}">
                                    {{ $cau->nhan_trangthai }}
                                </span>
                            </div>

                            <div class="mr-question-text">
                                {{ $cau->cauhoi_noidung }}
                            </div>

                            @if ($cau->anh)
                                <img class="mr-question-media"
                                     src="{{ asset(ltrim($cau->anh, '/')) }}"
                                     alt="Ảnh minh họa câu hỏi">
                            @endif

                            @if ($cau->cauhoi_audio)
                                <audio class="mr-audio"
                                       controls
                                       preload="none"
                                       src="{{ asset(ltrim($cau->cauhoi_audio, '/')) }}">
                                </audio>
                            @endif

                            @php
                                $luaChon = $luaChonTheoCau->get(
                                    $cau->cauhoi_id,
                                    collect()
                                );
                            @endphp

                            @if ($cau->chi_tiet_interactive !== null)
                                <div class="mr-interactive-review">
                                    @if (!$cau->dap_an_interactive_hop_le && $cau->datraloi)
                                        <p class="mr-review-note">
                                            Câu trả lời cũ không chứa đáp án từng câu con.
                                            Không thể phục dựng chi tiết để đối chiếu.
                                        </p>

                                        <div class="mr-free-answer">
                                            <strong>Nội dung đã ghi nhận</strong>
                                            <p>{{ $cau->traloi_noidung ?: 'Không có nội dung.' }}</p>
                                        </div>
                                    @endif

                                    @forelse ($cau->chi_tiet_interactive as $con)
                                        <article>
                                            <strong>Subquestion {{ $loop->iteration }}</strong>
                                            <p>{{ $con['noidung'] }}</p>

                                            @if ($cau->dap_an_interactive_hop_le || !$cau->datraloi)
                                                <strong>Câu trả lời của bạn</strong>
                                                <p>{{ $con['traloi'] }}</p>
                                            @endif

                                            <strong>Đáp án chuẩn hiện tại</strong>
                                            <p>
                                                {{ $con['dapandung'] !== ''
                                                    ? $con['dapandung']
                                                    : 'Chưa có đáp án chuẩn.' }}
                                            </p>
                                        </article>
                                    @empty
                                        <p class="mr-review-note">
                                            Không còn dữ liệu câu hỏi con để xem lại.
                                        </p>
                                    @endforelse
                                </div>

                            @elseif ($luaChon->isNotEmpty())
                                <div class="mr-options">
                                    @foreach ($luaChon as $lua)
                                        @php
                                            $duocChon = $cau->luachon_id !== null
                                                && (int) $cau->luachon_id === (int) $lua->id;

                                            $laDapAnDung = (int) $lua->ladung === 1;

                                            $optionClass = $laDapAnDung
                                                ? 'mr-option--correct'
                                                : ($duocChon ? 'mr-option--incorrect' : '');
                                        @endphp

                                        <div class="mr-option {{ $optionClass }}">
                                            <span class="mr-option-letter">
                                                {{ chr(65 + $loop->index) }}
                                            </span>

                                            <span class="mr-option-text">
                                                {{ $lua->noidung }}
                                            </span>

                                            @if ($laDapAnDung && $duocChon)
                                                <small>✓ Bạn chọn · Đáp án đúng</small>
                                            @elseif ($laDapAnDung)
                                                <small>✓ Đáp án đúng</small>
                                            @elseif ($duocChon)
                                                <small>× Bạn đã chọn</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            @else
                                <div class="mr-free-answer">
                                    <strong>Câu trả lời của bạn</strong>

                                    <p>
                                        {{ $cau->traloi_noidung
                                            ?: 'Không có câu trả lời bằng văn bản.' }}
                                    </p>

                                    @if ($cau->traloi_audio)
                                        <audio class="mr-audio"
                                               controls
                                               preload="none"
                                               src="{{ route('practice.ai.audio', $cau->traloi_id) }}">
                                        </audio>
                                    @endif

                                    @if ($cau->cachcham === 'auto')
                                        @php
                                            $dapAnDung = $dapAnChuanTheoCau->get(
                                                $cau->cauhoi_id,
                                                collect()
                                            );
                                        @endphp

                                        @if ($dapAnDung->isNotEmpty())
                                            <strong>Đáp án chấp nhận</strong>
                                            <p>
                                                {{ $dapAnDung->pluck('noidung')->implode(' / ') }}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            @if ($cau->giaithich)
                                <div class="mr-explanation">
                                    <strong>Giải thích chi tiết</strong>
                                    <p>{{ $cau->giaithich }}</p>
                                </div>
                            @endif

                            @if ($cau->traloi_id && in_array($cau->trangthai_ai, ['pending', 'failed'], true))
                                <p class="mr-review-note">
                                    <a href="{{ route('practice.ai.status', $cau->traloi_id) }}">
                                        {{ $cau->trangthai_ai === 'failed' ? 'Xem lỗi / chấm lại bằng AI' : 'Theo dõi tiến trình chấm AI' }}
                                    </a>
                                </p>
                            @endif

                            @if ($cau->nhanxet_ai)
                                <div class="mr-explanation">
                                    <strong>Nhận xét chấm điểm</strong>
                                    <p>{{ $cau->nhanxet_ai }}</p>
                                </div>
                            @endif

                            @if (in_array($cau->trangthai, [
                                'correct',
                                'partial',
                                'incorrect',
                                'graded',
                            ], true))
                                <div class="mr-earned-score">
                                    Điểm câu này:
                                    <strong>
                                        {{ number_format((float) $cau->diem, 2) }} /
                                        {{ number_format((float) $cau->diemtoida, 2) }}
                                    </strong>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="mr-empty-review">
                            Câu này chưa được cấp trong lần thi,
                            nên không có đáp án để xem lại.
                        </div>
                    @endforelse
                </section>
            </div>

            <aside class="mr-card mr-answer-sheet">
                <div class="mr-section-title">
                    <h2>Phiếu trả lời {{ $tongSoCau }} câu</h2>
                    <span>Chọn số để xem lại</span>
                </div>

                <div class="mr-legend">
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--correct"></i>
                        Đúng
                    </span>
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--partial"></i>
                        Đúng một phần
                    </span>
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--incorrect"></i>
                        Sai
                    </span>
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--skipped"></i>
                        Bỏ trống
                    </span>
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--pending"></i>
                        Chờ chấm
                    </span>
                    <span>
                        <i class="mr-legend-dot mr-legend-dot--graded"></i>
                        Đã chấm
                    </span>
                </div>

                <div class="mr-answer-grid">
                    @foreach ($phieuTraLoi as $cau)
                        @if ($cau['co_cau_hoi'])
                            <a href="{{ route('mock-test.result', [
                                    'id' => $lanThi->id,
                                    'cau' => $cau['thutu'],
                                ]) }}#review"
                               class="mr-q-btn mr-q-btn--{{ $cau['trangthai'] }} {{ !$xemTatCa && $cauSo === $cau['thutu'] ? 'mr-q-btn--active' : '' }}"
                               aria-label="Question {{ $cau['thutu'] }}: {{ $cau['nhan_trangthai'] }}"
                               title="{{ $cau['nhan_trangthai'] }}">
                                {{ $cau['thutu'] }}
                            </a>
                        @else
                            <span class="mr-q-btn mr-q-btn--unissued"
                                  title="Câu chưa được cấp">
                                {{ $cau['thutu'] }}
                            </span>
                        @endif
                    @endforeach
                </div>

                <p class="mr-sheet-note">
                    Ô nhạt: câu chưa được cấp khi bài thi kết thúc sớm.
                    Câu đã chấm có thể được cho điểm mà không phân loại đúng/sai.
                </p>
            </aside>
        </div>

        <div class="mr-bottom-actions">
            <a href="{{ route('mock-test.index') }}"
               class="mr-btn mr-btn--outline">
                Về danh sách đề thi
            </a>

            <div>
                <a href="{{ route('mock-test.result', [
                        'id' => $lanThi->id,
                        'all' => 1,
                    ]) }}#review"
                   class="mr-btn mr-btn--outline">
                    Xem toàn bộ lời giải
                </a>

                <form method="POST"
                      action="{{ route('mock-test.start') }}"
                      class="mr-retry-form">
                    @csrf

                    <input type="hidden"
                           name="dethi_id"
                           value="{{ $lanThi->dethi_id }}">

                    <button type="submit" class="mr-btn mr-btn--primary">
                        ↻ Làm lại đề thi
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('mr-share')?.addEventListener('click', async function () {
            const url = window.location.href;

            try {
                if (navigator.share) {
                    await navigator.share({ title: document.title, url });
                    return;
                }

                if (navigator.clipboard) {
                    await navigator.clipboard.writeText(url);
                    this.textContent = 'Đã sao chép liên kết';
                    return;
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }
            }

            window.prompt('Sao chép liên kết kết quả:', url);
        });
    </script>
@endpush