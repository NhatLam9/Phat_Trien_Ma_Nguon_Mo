@extends('layouts.app')

@section('title', 'Kết quả: ' . $result['session_title'] . ' - DET Practice')

@section('content')
<div style="max-width: 960px; margin: 0 auto; padding-bottom: 50px;">

    {{-- HEADER KẾT QUẢ --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge badge-reading" style="font-size: 12px; text-transform: uppercase;">
                    {{ $result['kynang'] }}
                </span>
                <span style="font-size: 13px; color: var(--bento-muted);">• {{ $result['created_at'] }}</span>
            </div>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--bento-heading); margin: 0;">
                Hoàn thành bài luyện tập
            </h1>
            <p style="color: var(--bento-muted); font-size: 15px; margin: 4px 0 0 0;">
                {{ $result['session_title'] }}
            </p>
        </div>
    </div>

    {{-- BẢNG THẺ THỐNG KÊ KẾT QUẢ TỔNG HỢP --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        
        {{-- 1. Điểm số đạt được --}}
        <div class="card" style="margin-bottom: 0; padding: 20px; border-radius: 14px; text-align: center; border: 1.5px solid var(--bento-line);">
            <div style="font-size: 13px; font-weight: 700; color: var(--bento-muted); text-transform: uppercase; margin-bottom: 6px;">
                ĐIỂM ĐẠT ĐƯỢC
            </div>
            <div style="font-size: 32px; font-weight: 800; color: var(--bento-orange); line-height: 1.1;">
                {{ $result['total_score'] }} <span style="font-size: 16px; color: #94a3b8; font-weight: 600;">/ {{ $result['max_score'] }}</span>
            </div>
            <div style="font-size: 12px; color: var(--bento-muted); margin-top: 6px;">
                Tổng số điểm đạt được
            </div>
        </div>

        {{-- 2. Tỷ lệ chính xác --}}
        <div class="card" style="margin-bottom: 0; padding: 20px; border-radius: 14px; text-align: center; border: 1.5px solid var(--bento-line);">
            <div style="font-size: 13px; font-weight: 700; color: var(--bento-muted); text-transform: uppercase; margin-bottom: 6px;">
                TỶ LỆ CHÍNH XÁC
            </div>
            <div style="font-size: 32px; font-weight: 800; color: {{ $result['percentage'] >= 70 ? '#16a34a' : ($result['percentage'] >= 50 ? '#ca8a04' : '#ef4444') }}; line-height: 1.1;">
                {{ $result['percentage'] }}%
            </div>
            <div style="font-size: 12px; color: var(--bento-muted); margin-top: 6px;">
                {{ $result['correct_count'] }} câu đúng trên tổng {{ $result['total_questions'] }} câu
            </div>
        </div>

        {{-- 3. Số câu đúng / sai --}}
        <div class="card" style="margin-bottom: 0; padding: 20px; border-radius: 14px; text-align: center; border: 1.5px solid var(--bento-line);">
            <div style="font-size: 13px; font-weight: 700; color: var(--bento-muted); text-transform: uppercase; margin-bottom: 6px;">
                KẾT QUẢ CÂU HỎI
            </div>
            <div style="display: flex; justify-content: center; gap: 14px; align-items: baseline;">
                <span style="font-size: 26px; font-weight: 800; color: #16a34a;">
                    {{ $result['correct_count'] }} ✓
                </span>
                <span style="font-size: 22px; font-weight: 800; color: #ef4444;">
                    {{ $result['wrong_count'] }} ✗
                </span>
            </div>
            <div style="font-size: 12px; color: var(--bento-muted); margin-top: 6px;">
                Chưa làm: {{ $result['unanswered_count'] }} câu
            </div>
        </div>

        {{-- 4. Thời gian làm bài --}}
        <div class="card" style="margin-bottom: 0; padding: 20px; border-radius: 14px; text-align: center; border: 1.5px solid var(--bento-line);">
            <div style="font-size: 13px; font-weight: 700; color: var(--bento-muted); text-transform: uppercase; margin-bottom: 6px;">
                THỜI GIAN RÈN LUYỆN
            </div>
            <div style="font-size: 30px; font-weight: 800; color: #0284c7; line-height: 1.1;">
                {{ intdiv($result['time_spent'], 60) > 0 ? intdiv($result['time_spent'], 60) . 'p ' : '' }}{{ $result['time_spent'] % 60 }}s
            </div>
            <div style="font-size: 12px; color: var(--bento-muted); margin-top: 6px;">
                Tự do rèn luyện
            </div>
        </div>

    </div>

    {{-- DANH SÁCH CHI TIẾT TỪNG CÂU HỎI, ĐÁP ÁN VÀ GIẢI THÍCH --}}
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--bento-heading); margin-bottom: 16px;">
            {{ ($result['kynang'] ?? '') === 'speaking' ? 'Chi tiết từng câu hỏi & Nhận xét:' : 'Chi tiết từng câu hỏi & Đáp án giải thích:' }}
        </h2>

        @foreach($result['evaluations'] as $qid => $eval)
            <div class="card" style="margin-bottom: 18px; border-radius: 14px; border: 1.5px solid {{ $eval['is_correct'] ? '#86efac' : ($eval['has_answered'] ? '#fca5a5' : 'var(--bento-line)') }};">
                
                {{-- Header câu hỏi: Số câu, kết quả, điểm --}}
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-weight: 800; color: var(--bento-heading); font-size: 14px;">
                            Câu hỏi #{{ $qid }}
                        </span>
                        <span style="font-size: 12px; color: var(--bento-muted);">
                            Độ khó: {{ $eval['question']->dokho ?? 5 }}/10
                        </span>
                        @php $isBm = in_array((int)$qid, $bookmarkedIds ?? []); @endphp
                        <button type="button" 
                                class="btn btn-sm" 
                                id="res-bm-btn-{{ $qid }}" 
                                onclick="toggleResultBookmark({{ $qid }})"
                                title="Lưu câu hỏi này để ôn tập"
                                style="padding: 3px 10px; font-size: 11px; font-weight: 700; border-radius: 6px; {{ $isBm ? 'background: #fef3c7; color: #b45309; border: 1px solid #fcd34d;' : 'background: var(--bento-subtle); color: var(--bento-muted); border: 1px solid var(--bento-line);' }}">
                            <span id="res-bm-icon-{{ $qid }}">{{ $isBm ? '⭐' : '☆' }}</span>
                            <span id="res-bm-text-{{ $qid }}">{{ $isBm ? 'Đã lưu' : 'Lưu câu' }}</span>
                        </button>
                    </div>

                    <div>
                        @if($eval['is_correct'])
                            <span class="badge badge-speaking" style="font-size: 12px; font-weight: 700;">
                                ✓ Đúng (+{{ $eval['diem_dat'] }} điểm)
                            </span>
                        @elseif($eval['has_answered'])
                            <span class="badge badge-writing" style="font-size: 12px; font-weight: 700; background: #fee2e2; color: #991b1b;">
                                ✗ Sai (0 điểm)
                            </span>
                        @else
                            <span class="badge" style="font-size: 12px; font-weight: 700; background: #f1f5f9; color: var(--bento-muted);">
                                — Chưa làm
                            </span>
                        @endif

                        @if(!empty($eval['diem_ai']))
                            <span style="background: #2563eb; color: #ffffff; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 12px; margin-left: 6px;">
                                Ước lượng DET: {{ $eval['diem_ai'] }}/160
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Nội dung đề bài --}}
                <div style="font-size: 16px; font-weight: 700; color: var(--bento-heading); margin-bottom: 14px; line-height: 1.6;">
                    {{ $eval['question']->noidung }}
                </div>

                {{-- Câu trả lời của người dùng; Speaking chỉ hiển thị bài nói, không hiện đáp án mẫu --}}
                @php
                    $laSpeaking = ($result['kynang'] ?? '') === 'speaking';
                @endphp
                <div style="display: grid; grid-template-columns: {{ $laSpeaking ? '1fr' : '1fr 1fr' }}; gap: 12px; margin-bottom: 14px;">
                    <div style="background: var(--bento-subtle); padding: 12px 14px; border-radius: 8px; border: 1px solid var(--bento-line);">
                        <span style="font-size: 12px; font-weight: 700; color: var(--bento-muted); display: block; margin-bottom: 4px;">
                            CÂU TRẢ LỜI CỦA BẠN:
                        </span>

                        @if ($laSpeaking)
                            @php
                                $traLoi = (string) ($eval['user_answer_text'] ?? '');
                                $laDuongDanAudio = $traLoi !== '' && (
                                    str_starts_with($traLoi, 'storage/')
                                    || str_starts_with($traLoi, '/storage/')
                                    || str_starts_with($traLoi, 'http://')
                                    || str_starts_with($traLoi, 'https://')
                                );
                                $nguonAudio = str_starts_with($traLoi, 'http://') || str_starts_with($traLoi, 'https://')
                                    ? $traLoi
                                    : asset(ltrim($traLoi, '/'));
                            @endphp

                            @if ($laDuongDanAudio)
                                <audio controls preload="none" style="width: 100%; margin-top: 4px;">
                                    <source src="{{ $nguonAudio }}">
                                    Trình duyệt của bạn không hỗ trợ phát âm thanh.
                                </audio>
                                <span style="display: block; margin-top: 6px; font-size: 12px; color: var(--bento-muted);">
                                    Bản ghi âm bài nói của bạn
                                </span>
                            @else
                                <span style="font-weight: 700; color: {{ $eval['has_answered'] ? 'var(--bento-heading)' : '#94a3b8' }}; font-size: 14px;">
                                    {{ $traLoi ?: '(Chưa ghi âm câu trả lời)' }}
                                </span>
                            @endif
                        @else
                            <span style="font-weight: 700; color: {{ $eval['is_correct'] ? '#16a34a' : ($eval['has_answered'] ? '#dc2626' : '#94a3b8') }}; font-size: 14px;">
                                {{ $eval['user_answer_text'] ?: '(Chưa nhập đáp án)' }}
                            </span>
                        @endif
                    </div>

                    @if (! $laSpeaking)
                        <div style="background: #f0fdf4; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                            <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 4px;">
                                ĐÁP ÁN CHÍNH XÁC:
                            </span>
                            <span style="font-weight: 700; color: #15803d; font-size: 14px;">
                                {{ !empty($eval['correct_answers']) ? implode(', ', $eval['correct_answers']) : ($eval['question']->giaithich ?: 'Đáp án chuẩn từ ngân hàng đề thi') }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- GIẢI THÍCH CHI TIẾT --}}
                @if(!empty($eval['giaithich']))
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 16px; font-size: 14px; line-height: 1.6; color: #1e3a8a;">
                        <strong> Giải thích chi tiết:</strong>
                        <div style="margin-top: 4px;">
                            {{ $eval['giaithich'] }}
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    {{-- CÁC NÚT ĐIỀU HƯỚNG DƯỚI CÙNG --}}
    <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; margin-top: 30px;">
        <a href="{{ route('practice.session', ['loai_id' => $result['type_id'], 'dokho' => $result['dokho']]) }}" class="btn btn-primary" style="padding: 12px 28px; font-weight: 700;">
             Luyện tập lại dạng bài này
        </a>
        <a href="{{ route('practice.select') }}" class="btn btn-outline" style="padding: 12px 24px; font-weight: 700;">
             Chọn kỹ năng & dạng bài khác
        </a>
        <a href="{{ route('practice.bookmarks') }}" class="btn btn-outline" style="padding: 12px 24px; font-weight: 700;">
             Xem câu hỏi đã lưu
        </a>
        <a href="{{ route('practice.history') }}" class="btn btn-outline" style="padding: 12px 24px; font-weight: 700;">
             Xem lịch sử luyện tập
        </a>
    </div>

</div>

<div id="res-toast" style="position: fixed; bottom: 24px; right: 24px; background: var(--bento-heading); color: #ffffff; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; display: none;"></div>

@push('scripts')
<script>
async function toggleResultBookmark(qid) {
    const btn = document.getElementById('res-bm-btn-' + qid);
    const icon = document.getElementById('res-bm-icon-' + qid);
    const text = document.getElementById('res-bm-text-' + qid);

    try {
        const response = await fetch('/practice/question/' + qid + '/bookmark', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success) {
            if (data.is_bookmarked) {
                if (btn) {
                    btn.style.background = '#fef3c7';
                    btn.style.color = '#b45309';
                    btn.style.borderColor = '#fcd34d';
                }
                if (icon) icon.innerText = '⭐';
                if (text) text.innerText = 'Đã lưu';
                showResToast('⭐ ' + data.message);
            } else {
                if (btn) {
                    btn.style.background = '#f8fafc';
                    btn.style.color = '#64748b';
                    btn.style.borderColor = '#cbd5e1';
                }
                if (icon) icon.innerText = '☆';
                if (text) text.innerText = 'Lưu câu';
                showResToast('✓ ' + data.message);
            }
        }
    } catch (e) {
        console.error(e);
    }
}

function showResToast(msg) {
    const t = document.getElementById('res-toast');
    if (t) {
        t.innerText = msg;
        t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2500);
    }
}
</script>
@endpush
@endsection

