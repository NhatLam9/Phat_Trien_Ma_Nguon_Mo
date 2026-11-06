@extends('layouts.app')

@section('title', 'Kết quả luyện tập - DET Practice')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('practice.select') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 14px;">
            ← Quay lại danh sách câu hỏi
        </a>
    </div>

    <!-- THẺ KẾT QUẢ CHÍNH (LT05) -->
    <div class="card" style="text-align: center; padding: 36px 24px; border-top: 6px solid {{ $attempt->ladung ? 'var(--bento-orange)' : '#ef4444' }};">
        <div style="font-size: 52px; margin-bottom: 8px;">
            @if($attempt->ladung)
                🎉
            @elseif($attempt->trangthai_ai === 'pending')
                ⏳
            @else
                💡
            @endif
        </div>

        <h1 style="font-size: 26px; font-weight: 800; color: var(--bento-heading); margin-bottom: 6px;">
            @if($attempt->ladung)
                <span style="color: var(--bento-orange);">Chính xác! Làm rất tốt!</span>
            @elseif($attempt->trangthai_ai === 'pending')
                <span style="color: #f59e0b;">Đang chờ AI chấm điểm...</span>
            @else
                <span style="color: #ef4444;">Chưa chính xác! Hãy xem giải thích bên dưới</span>
            @endif
        </h1>

        <p style="color: var(--bento-muted); font-size: 14px; margin-bottom: 20px;">
            Lần làm bài lúc {{ date('H:i d/m/Y', strtotime($attempt->ngaylam)) }} • Thời gian làm: {{ $attempt->thoigianlam ?? 0 }} giây
        </p>

        <div style="display: inline-flex; gap: 24px; background: var(--bento-subtle); padding: 14px 28px; border-radius: 12px; margin-bottom: 24px; border: 1px solid var(--bento-line);">
            <div>
                <div style="font-size: 12px; color: var(--bento-muted); text-transform: uppercase; font-weight: 600;">Điểm số</div>
                <div style="font-size: 24px; font-weight: 800; color: {{ $attempt->diem > 0 ? 'var(--bento-orange)' : 'var(--bento-muted)' }};">
                    {{ $attempt->diem ?? 0 }} / {{ $cauhoi->diem ?? 1.0 }}
                </div>
            </div>
            <div style="border-left: 1px solid var(--bento-line);"></div>
            <div>
                <div style="font-size: 12px; color: var(--bento-muted); text-transform: uppercase; font-weight: 600;">Trạng thái</div>
                <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: {{ $attempt->ladung ? 'var(--bento-orange)' : '#ef4444' }};">
                    {{ $attempt->ladung ? 'Đạt' : ($attempt->trangthai_ai === 'pending' ? 'Chờ AI' : 'Chưa đạt') }}
                </div>
            </div>
        </div>

        @if(empty($attempt->ladung) && empty($attempt->trangthai_ai))
            <!-- LT06: Nút gửi bài cho AI chấm nếu cần chấm chuyên sâu -->
            <div style="margin-bottom: 20px;">
                <form method="POST" action="{{ route('practice.ai', $attempt->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">
                         Gửi bài cho trợ lý AI chấm chi tiết (Writing/Speaking)
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- CHI TIẾT ĐỀ BÀI VÀ ĐÁP ÁN -->
    <div class="card">
        <h2 style="font-size: 18px; font-weight: 700; color: var(--bento-heading); margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
             Chi tiết câu hỏi và bài làm của bạn
        </h2>

        <!-- Đề bài -->
        <div style="margin-bottom: 20px;">
            <div style="font-size: 13px; font-weight: 700; color: var(--bento-muted); margin-bottom: 4px;">ĐỀ BÀI:</div>
            <div style="font-size: 16px; color: var(--bento-heading); line-height: 1.6; font-weight: 600;">
                {{ $cauhoi->noidung ?? 'Nội dung câu hỏi' }}
            </div>
        </div>

        <!-- Bài làm của người dùng -->
        <div style="margin-bottom: 20px; background: var(--bento-subtle); padding: 14px 18px; border-radius: 8px; border-left: 4px solid {{ $attempt->ladung ? 'var(--bento-orange)' : '#ef4444' }};">
            <div style="font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 4px;">CÂU TRẢ LỜI CỦA BẠN:</div>
            <div style="font-size: 15px; color: var(--bento-heading);">
                @if(!empty($attempt->luachon_id) && isset($cauhoi->luachon))
                    @php
                        $userChoice = collect($cauhoi->luachon)->firstWhere('id', $attempt->luachon_id);
                    @endphp
                    <strong>Lựa chọn:</strong> {{ $userChoice->noidung ?? ('Mã lựa chọn #' . $attempt->luachon_id) }}
                @else
                    {{ $attempt->noidung ?? '(Không có nội dung)' }}
                @endif
            </div>
            @if(!empty($attempt->audio))
                <div style="margin-top: 10px;">
                    <audio controls style="height: 36px; max-width: 100%;">
                        <source src="{{ asset($attempt->audio) }}">
                    </audio>
                </div>
            @endif
        </div>

        @if(!empty($attempt->nhanxet_ai))
            <!-- Nhận xét chi tiết từ AI (AIService) -->
            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                    <div style="font-size: 14px; font-weight: 800; color: #1d4ed8; display: flex; align-items: center; gap: 6px;">
                        🤖 NHẬN XÉT & ĐÁNH GIÁ TỪ TRỢ LÝ AI (AI GRADER):
                    </div>
                    @if(!empty($attempt->diem_ai))
                        <span style="background: #2563eb; color: #ffffff; font-size: 13px; font-weight: 800; padding: 3px 12px; border-radius: 20px;">
                            Ước lượng DET: {{ $attempt->diem_ai }}/160
                        </span>
                    @endif
                </div>
                <div style="font-size: 14px; color: #1e3a8a; line-height: 1.7; white-space: pre-line;">
                    {{ $attempt->nhanxet_ai }}
                </div>
            </div>
        @endif

        <!-- Đáp án đúng & Giải thích -->
        <div style="background: #eefcf5; padding: 16px 20px; border-radius: 8px; border: 1px solid #c8f3dc; margin-bottom: 24px;">
            <div style="font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 6px;">💡 ĐÁP ÁN CHUẨN & GIẢI THÍCH:</div>
            
            @if(isset($cauhoi->luachon))
                <div style="margin-bottom: 8px; font-size: 14px; color: #14532d;">
                    <strong>Đáp án đúng:</strong> 
                    @foreach(collect($cauhoi->luachon)->where('ladung', 1) as $c)
                        <span style="background: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: 600; margin-right: 6px; border: 1px solid #86efac;">
                            {{ $c->noidung }}
                        </span>
                    @endforeach
                </div>
            @endif

            <p style="font-size: 14px; color: #166534; line-height: 1.6; margin: 0;">
                {{ $cauhoi->giaithich ?? 'Hãy chú ý từ vựng chính xác và cấu trúc câu để trả lời chuẩn theo tiêu chí Duolingo English Test.' }}
            </p>
        </div>

        <!-- Nút hành động -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
            @if(isset($cauhoi->id))
                <a href="{{ route('practice.show', $cauhoi->id) }}" class="btn btn-outline">
                    Làm lại câu này
                </a>
            @endif

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('practice.history') }}" class="btn btn-outline">
                    Lịch sử luyện tập
                </a>
                <a href="{{ route('practice.select') }}" class="btn btn-primary">
                    Tiếp tục luyện câu khác →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

