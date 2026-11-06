@extends('layouts.app')

@section('title', 'Kết quả chấm AI - DET Practice')

@if ($traloi->trangthai_ai === 'pending')
    @push('styles')
        <meta http-equiv="refresh" content="5">
    @endpush
@endif

@section('content')
<div class="ai-status-container" style="max-width: 850px; margin: 2rem auto; padding: 0 1rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('practice.index') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 0.95rem;">&larr; Danh sách luyện tập</a>
        <h1 style="font-size: 1.8rem; color: var(--bento-heading); margin-top: 0.5rem;">Trạng thái chấm điểm AI</h1>
    </div>

    @if (session('success'))
        <div style="background: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('ai_limit'))
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong>Hết hạn mức hôm nay:</strong> {{ $errors->first('ai_limit') }}
        </div>
    @endif

    <!-- TRƯỜNG HỢP 1: ĐANG CHỜ CHẤM (PENDING) -->
    @if ($traloi->trangthai_ai === 'pending')
        <div style="background: #ffffff; border: 2px solid #3b82f6; border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="font-size: 1.5rem; color: #1e40af; margin-bottom: 0.75rem;">Hệ thống AI đang chấm bài của bạn...</h2>
            <p style="color: var(--bento-muted); font-size: 1.05rem; line-height: 1.6; max-width: 600px; margin: 0 auto 1.5rem auto;">
                Quá trình phân tích ngôn ngữ và đối chiếu rubric DET thường mất từ 10 đến 30 giây. Trang sẽ tự động tải lại mỗi 5 giây cho đến khi có điểm.
            </p>
            <div style="display: inline-block; background: #eff6ff; color: #1d4ed8; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.9rem; font-weight: 600;">
                Tự động làm mới bằng &lt;meta refresh&gt; (No-JS) · Lần chấm số: {{ $traloi->luot_cham }}
            </div>
        </div>

    <!-- TRƯỜNG HỢP 2: ĐÃ CHẤM XONG (DONE) -->
    @elseif ($traloi->trangthai_ai === 'done')
        <div style="background: #ffffff; border: 2px solid #22c55e; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--bento-line); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="background: #dcfce7; color: #15803d; font-size: 0.85rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 999px; text-transform: uppercase;">
                        Chấm điểm hoàn tất
                    </span>
                    <h2 style="font-size: 1.5rem; color: var(--bento-heading); margin-top: 0.5rem; margin-bottom: 0;">Điểm số bài làm</h2>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2.5rem; font-weight: 900; color: #16a34a; line-height: 1;">
                        {{ $traloi->diem_ai !== null ? number_format($traloi->diem_ai, 0) : 'N/A' }}
                    </div>
                    <span style="font-size: 0.85rem; color: var(--bento-muted); font-weight: 600;">Thang điểm DET: 10 - 160</span>
                </div>
            </div>

            @if ($isSpeaking)
                <!-- GHI CHÚ BẮT BUỘC THEO MỤC 1 & 4 -->
                <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 0.85rem 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; color: #92400e; font-weight: 600;">
                    ⚠️ Điểm số dựa trên bản chép lời tự động, không đánh giá hoàn toàn độ chính xác của phát âm.
                </div>
            @endif

            @if (!empty($traloi->audio))
                <div style="margin-bottom: 1.5rem; background: var(--bento-subtle); padding: 1rem; border-radius: 8px; border: 1px solid var(--bento-line);">
                    <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 0.95rem;">File ghi âm bài nói:</strong>
                    <audio controls style="width: 100%;">
                        <source src="{{ route('practice.ai.audio', $traloi->id) }}">
                        Trình duyệt không hỗ trợ phát âm thanh.
                    </audio>
                </div>
            @endif

            @if (!empty($traloi->noidung))
                <div style="margin-bottom: 1.5rem;">
                    <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">
                        {{ $isSpeaking ? 'Bản chép lời giọng nói (Transcript):' : 'Bài viết của bạn:' }}
                    </strong>
                    <div style="background: var(--bento-subtle); border: 1px solid var(--bento-line); border-radius: 8px; padding: 1rem; color: var(--bento-heading); font-size: 1rem; line-height: 1.6; white-space: pre-wrap;">{{ $traloi->noidung }}</div>
                </div>
            @endif

            <div>
                <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Nhận xét & Tiêu chí chi tiết từ AI Examiner:</strong>
                <div style="background: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 4px; padding: 1.25rem; color: #064e3b; font-size: 1rem; line-height: 1.6; white-space: pre-wrap;">{{ $nhanXet }}</div>
            </div>
        </div>

    <!-- TRƯỜNG HỢP 3: THẤT BẠI (FAILED) -->
    @elseif ($traloi->trangthai_ai === 'failed')
        <div style="background: #ffffff; border: 2px solid #ef4444; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="font-size: 2rem;">❌</span>
                <div>
                    <h2 style="font-size: 1.4rem; color: #991b1b; margin: 0;">Chấm bài không thành công</h2>
                    <span style="color: var(--bento-muted); font-size: 0.85rem;">Hệ thống đã tự động hoàn lại lượt sử dụng cho bạn.</span>
                </div>
            </div>

            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 1rem; color: #7f1d1d; margin-bottom: 1.5rem; line-height: 1.5;">
                <strong>Lý do:</strong> {{ $nhanXet ?: 'Không nhận được kết quả từ máy chủ AI.' }}
            </div>

            @if ($isTranscriptTooLong)
                <!-- Lỗi transcript quá dài: Ẩn nút Chấm lại theo mục 4 -->
                <div style="background: #fff7ed; border-left: 4px solid #ea580c; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; color: #9a3412;">
                    <strong>Yêu cầu:</strong> Bài nói của bạn quá dài so với giới hạn. Vui lòng quay lại nộp file ghi âm mới ngắn hơn, nút "Chấm lại" đã được ẩn để tránh lãng phí lượt.
                </div>
                @if ($cauhoi)
                    <a href="{{ route('practice.ai.question', $cauhoi->id) }}" style="display: inline-block; background: #ea580c; color: #ffffff; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none;">
                        Nộp bản ghi âm mới &rarr;
                    </a>
                @endif
            @else
                <!-- Nút Chấm lại hợp lệ (POST form) -->
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <span style="font-size: 0.9rem; color: var(--bento-muted);">
                        Bấm "Chấm lại" để gửi lại yêu cầu chấm (sẽ tiêu tốn 1 lượt mới).
                    </span>
                    <form method="POST" action="{{ route('practice.ai.retry', $traloi->id) }}">
                        @csrf
                        <button type="submit" style="background: #2563eb; color: #ffffff; font-weight: 700; padding: 0.75rem 1.75rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                            🔄 Chấm lại bài này
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

