@extends('layouts.app')

@section('title', 'Kết quả ' . $deThi->ten . ' | DET Practice')
@section('main_class', 'app-main--mock-result')
@section('body_class', 'mock-test-result-body')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test-result.css') }}?v=5">
@endpush

@section('content')
<div class="mr-page">
    <div class="mr-topline">
        <div class="mr-crumbs">
            <a href="{{ route('mock-test.index') }}">← Thoát</a>
            <span>/</span><span>Luyện thi DET</span><span>/</span>
            <strong>Kết quả #{{ $lanThi->id }}</strong>
        </div>
        <div class="mr-statuses">
            <span class="mr-pill mr-pill--success">● ĐÃ HOÀN THÀNH</span>
            <span class="mr-pill mr-pill--neutral">
                {{ $ketQua['da_cham_xong'] ? '✓ Đã chấm điểm' : '◷ Đang chờ chấm' }}
            </span>
        </div>
    </div>

    <div class="mr-heading">
        <div>
            <p class="mr-kicker">BÀI THI THỬ · #{{ $lanThi->id }}
                <span>· {{ $deThi->kieu === 'adaptive' ? 'Thích ứng' : 'Cố định' }}</span>
            </p>
            <h1>Kết quả {{ $deThi->ten }}</h1>
            <p class="mr-subtitle">
                <span>Nộp bài: {{ $lanThi->ketthuc?->format('H:i · d/m/Y') ?? 'Chưa ghi nhận' }}</span>
                <span>Thời gian: {{ intdiv($soGiayLamBai, 60) }} phút {{ $soGiayLamBai % 60 }} giây</span>
            </p>
        </div>
        <div class="mr-heading-actions">
            <button type="button" class="mr-btn mr-btn--outline" onclick="window.print()">⤓ In / Lưu PDF</button>
            <button type="button" class="mr-btn mr-btn--outline" id="mr-share">Chia sẻ</button>
        </div>
    </div>

    <section class="mr-card mr-scoreboard" aria-label="Tổng quan kết quả">
        <div class="mr-score-grid">
            <div class="mr-primary-score">
                <span class="mr-label">ĐIỂM BÀI THI</span>
                <div class="mr-score">
                    <strong>{{ number_format((float) $ketQua['diem_da_cham'], 2) }}</strong>
                    <span>/ {{ number_format((float) $ketQua['diem_toi_da'], 2) }} điểm</span>
                </div>
                <span class="mr-score-caption">Điểm thô · Chưa quy đổi DET 10–160</span>
            </div>
            <div class="mr-accuracy">
                <span class="mr-label">TỶ LỆ ĐIỂM</span>
                <strong>{{ $ketQua['ty_le_phan_tram'] !== null ? number_format($ketQua['ty_le_phan_tram'], 1) . '%' : 'Chờ chấm' }}</strong>
                <span>{{ $ketQua['so_cau_dung'] }} câu đúng trên {{ $tongSoCau }} câu</span>
            </div>
            <div class="mr-assessment">
                <strong>◈ Đánh giá kết quả</strong>
                @if (!$ketQua['da_cham_xong'])
                    <p>Còn {{ $ketQua['so_cau_cho_cham'] }} câu chưa có điểm. Điểm và tỷ lệ cuối cùng sẽ được cập nhật sau khi chấm.</p>
                @elseif ($ketQua['ty_le_phan_tram'] >= 80)
                    <p>Bạn đạt tỷ lệ điểm tốt trong đề này. Xem lại những câu sai để cải thiện thêm.</p>
                @elseif ($ketQua['ty_le_phan_tram'] >= 50)
                    <p>Bạn đã đạt một phần đáng kể số điểm. Hãy xem lời giải các câu sai và luyện tập các kỹ năng còn yếu.</p>
                @else
                    <p>Bạn nên xem lại từng câu, đối chiếu đáp án và luyện tập thêm trước lần thi tiếp theo.</p>
                @endif
            </div>
        </div>
        <div class="mr-stats">
            <div class="mr-stat"><span class="mr-stat-icon mr-stat-icon--correct">✓</span><div><small>Đúng</small><strong>{{ $ketQua['so_cau_dung'] }} <em>/ {{ $tongSoCau }}</em></strong></div></div>
            <div class="mr-stat"><span class="mr-stat-icon mr-stat-icon--incorrect">×</span><div><small>Sai</small><strong>{{ $ketQua['so_cau_sai'] }} <em>/ {{ $tongSoCau }}</em></strong></div></div>
            <div class="mr-stat"><span class="mr-stat-icon mr-stat-icon--skipped">–</span><div><small>Bỏ trống</small><strong>{{ $ketQua['so_cau_bo_trong'] }} <em>/ {{ $tongSoCau }}</em></strong></div></div>
            <div class="mr-stat"><span class="mr-stat-icon mr-stat-icon--pending">◷</span><div><small>Chờ chấm</small><strong>{{ $ketQua['so_cau_cho_cham'] }}</strong></div></div>
        </div>
    </section>

    <div class="mr-columns">
        <div class="mr-left">
            <section class="mr-card mr-skills-section">
                <div class="mr-section-title"><h2>Phân tích theo kỹ năng</h2><span>{{ $phanTichKyNang->count() }} kỹ năng</span></div>
                @forelse ($phanTichKyNang as $kyNang)
                    @if ($loop->first)<div class="mr-skills">@endif
                    <div class="mr-skill">
                        <div class="mr-ring" style="--mr-p: {{ max(0, min(100, $kyNang['phan_tram'] ?? 0)) }}%">
                            <span>{{ $kyNang['phan_tram'] !== null ? number_format($kyNang['phan_tram'], 0) . '%' : '—' }}</span>
                        </div>
                        <strong>{{ $kyNang['ten'] }}</strong>
                        <small>
                            @if ($kyNang['so_cho_cham'] > 0)
                                {{ $kyNang['so_cho_cham'] }} câu chờ chấm
                            @else
                                {{ $kyNang['so_dung'] }}/{{ $kyNang['so_cau'] }} câu đúng
                            @endif
                        </small>
                    </div>
                    @if ($loop->last)</div>@endif
                @empty
                    <p class="mr-muted">Phiên thi chưa có dữ liệu câu hỏi để phân tích.</p>
                @endforelse
            </section>

            <section class="mr-card mr-review" id="review">
                <div class="mr-section-title">
                    <h2>{{ $xemTatCa ? 'Toàn bộ lời giải' : sprintf('Câu %02d / %02d', $cauSo, $tongSoCau) }}</h2>
                    <span>{{ $xemTatCa ? 'Tất cả câu đã cấp' : 'Xem lại câu hỏi' }}</span>
                </div>

                @forelse ($dsHienThi as $cau)
                    <article class="mr-review-item" id="cau-{{ $cau->thutu }}">
                        <div class="mr-review-header">
                            <strong>Câu {{ $cau->thutu }} · {{ $cau->tenloai }}</strong>
                            <span class="mr-result-status mr-result-status--{{ $cau->trangthai }}">
                                @switch($cau->trangthai)
                                    @case('correct') ✓ Đúng @break
                                    @case('incorrect') × Sai @break
                                    @case('skipped') — Bỏ qua @break
                                    @case('pending') ◷ Chờ chấm @break
                                    @default ✓ Đã chấm
                                @endswitch
                            </span>
                        </div>
                        <div class="mr-question-text">{{ $cau->cauhoi_noidung }}</div>

                        @if ($cau->anh)
                            <img class="mr-question-media" src="{{ asset(ltrim($cau->anh, '/')) }}" alt="Ảnh minh họa câu hỏi">
                        @endif
                        @if ($cau->cauhoi_audio)
                            <audio class="mr-audio" controls preload="none" src="{{ asset(ltrim($cau->cauhoi_audio, '/')) }}"></audio>
                        @endif

                        @php $luaChon = $luaChonTheoCau->get($cau->cauhoi_id, collect()); @endphp
                        @if ($luaChon->isNotEmpty())
                            <div class="mr-options">
                                @foreach ($luaChon as $lua)
                                    @php
                                        $duocChon = $cau->luachon_id !== null && (int) $cau->luachon_id === (int) $lua->id;
                                        $laDapAnDung = (int) $lua->ladung === 1;
                                    @endphp
                                    <div class="mr-option {{ $laDapAnDung ? 'mr-option--correct' : ($duocChon ? 'mr-option--incorrect' : '') }}">
                                        <span class="mr-option-letter">{{ chr(65 + $loop->index) }}</span>
                                        <span class="mr-option-text">{{ $lua->noidung }}</span>
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
                                <p>{{ $cau->traloi_noidung ?: 'Không có câu trả lời bằng văn bản.' }}</p>
                                @if ($cau->traloi_audio)
                                    <audio class="mr-audio" controls preload="none" src="{{ asset(ltrim($cau->traloi_audio, '/')) }}"></audio>
                                @endif
                                @if ($cau->cachcham === 'auto')
                                    @php $dapAnDung = $dapAnChuanTheoCau->get($cau->cauhoi_id, collect()); @endphp
                                    @if ($dapAnDung->isNotEmpty())
                                        <strong>Đáp án chấp nhận</strong>
                                        <p>{{ $dapAnDung->pluck('noidung')->implode(' / ') }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif

                        @if ($cau->giaithich)
                            <div class="mr-explanation"><strong>◈ Giải thích chi tiết</strong><p>{{ $cau->giaithich }}</p></div>
                        @endif
                        @if ($cau->nhanxet_ai)
                            <div class="mr-explanation"><strong>Nhận xét chấm điểm</strong><p>{{ $cau->nhanxet_ai }}</p></div>
                        @endif
                        @if ($cau->diem !== null)
                            <div class="mr-earned-score">Điểm câu này: <strong>{{ number_format((float) $cau->diem, 2) }} / {{ number_format((float) $cau->diemtoida, 2) }}</strong></div>
                        @endif
                    </article>
                @empty
                    <div class="mr-empty-review">Câu này chưa được cấp trong phiên thi, nên không có đáp án để xem lại.</div>
                @endforelse
            </section>
        </div>

        <aside class="mr-card mr-answer-sheet">
            <div class="mr-section-title"><h2>Phiếu trả lời {{ $tongSoCau }} câu</h2><span>Chọn số để xem lại</span></div>
            <div class="mr-legend">
                <span><i class="mr-legend-dot mr-legend-dot--correct"></i> Đúng</span>
                <span><i class="mr-legend-dot mr-legend-dot--incorrect"></i> Sai</span>
                <span><i class="mr-legend-dot mr-legend-dot--skipped"></i> Bỏ qua</span>
                <span><i class="mr-legend-dot mr-legend-dot--pending"></i> Chờ chấm</span>
            </div>
            <div class="mr-answer-grid">
                @foreach ($phieuTraLoi as $cau)
                    @if ($cau['co_cau_hoi'])
                        <a href="{{ route('mock-test.result', ['id' => $lanThi->id, 'cau' => $cau['thutu']]) }}#review"
                           class="mr-q-btn mr-q-btn--{{ $cau['trangthai'] }} {{ !$xemTatCa && $cauSo === $cau['thutu'] ? 'mr-q-btn--active' : '' }}"
                           aria-label="Xem lại câu {{ $cau['thutu'] }}">
                            {{ $cau['thutu'] }}
                        </a>
                    @else
                        <span class="mr-q-btn mr-q-btn--unissued" title="Câu chưa được cấp">{{ $cau['thutu'] }}</span>
                    @endif
                @endforeach
            </div>
            <p class="mr-sheet-note">Ô nhạt: câu chưa được cấp (nếu bài thi kết thúc sớm).</p>
        </aside>
    </div>

    <div class="mr-bottom-actions">
        <a href="{{ route('mock-test.index') }}" class="mr-btn mr-btn--outline">← Về danh sách đề thi</a>
        <div>
            <a href="{{ route('mock-test.result', ['id' => $lanThi->id, 'all' => 1]) }}#review" class="mr-btn mr-btn--outline">Xem toàn bộ lời giải</a>
            <form method="POST" action="{{ route('mock-test.start') }}" class="mr-retry-form">
                @csrf
                <input type="hidden" name="dethi_id" value="{{ $lanThi->dethi_id }}">
                <button type="submit" class="mr-btn mr-btn--primary">↻ Làm lại đề thi</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mr-share')?.addEventListener('click', async function () {
        const url = window.location.href;
        if (navigator.share) {
            try { await navigator.share({ title: document.title, url }); }
            catch (e) { /* Người dùng huỷ chia sẻ. */ }
        } else if (navigator.clipboard) {
            await navigator.clipboard.writeText(url);
            this.textContent = 'Đã sao chép liên kết';
        } else {
            window.prompt('Sao chép liên kết kết quả:', url);
        }
    });
</script>
@endpush
