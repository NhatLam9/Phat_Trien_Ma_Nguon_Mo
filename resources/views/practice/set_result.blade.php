@extends('layouts.app')

@section('title', 'Kết quả mã đề ' . $set->code . ' - DET Practice')

@section('content')
<div style="max-width: 920px; margin: 0 auto; padding-bottom: 40px;">

    {{-- THANH ĐIỀU HƯỚNG --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('practice.select') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600;">
            ← Quay lại danh sách mã đề
        </a>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.history') }}" class="btn btn-outline btn-sm">
                 Xem lịch sử luyện tập
            </a>
            <a href="{{ route('practice.set.show', $set->code) }}" class="btn btn-primary btn-sm">
                Làm lại mã đề này
            </a>
        </div>
    </div>

    {{-- 1. THẺ BẢNG ĐIỂM TỔNG QUAN --}}
    <div class="card" style="text-align: center; padding: 36px 24px; border-top: 6px solid {{ $result['percentage'] >= 70 ? '#00b86b' : ($result['percentage'] >= 50 ? '#f59e0b' : '#ef4444') }}; border-radius: 16px; margin-bottom: 28px;">
        <div style="font-size: 48px; margin-bottom: 6px;">
            @if($result['percentage'] >= 70)
                🎉
            @elseif($result['percentage'] >= 50)
                💡
            @else
                📚
            @endif
        </div>

        <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
            @if($result['percentage'] >= 70)
                <span style="color: #00b86b;">Hoàn thành xuất sắc bài thi!</span>
            @elseif($result['percentage'] >= 50)
                <span style="color: #d97706;">Hoàn thành bài thi - Kết quả Khá!</span>
            @else
                <span style="color: #ef4444;">Đã hoàn thành - Cần luyện tập thêm!</span>
            @endif
        </h1>

        <p style="color: #64748b; font-size: 14px; margin-bottom: 12px;">
            {{ $result['set_title'] }} • Nộp bài lúc {{ $result['created_at'] }}
        </p>

        @if(!empty($result['is_timeout']))
            <div style="display: inline-block; background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 18px;">
                ⏱️ Đã tự động nộp bài và chấm điểm khi hết thời gian quy định
            </div>
        @endif

        {{-- BẢNG THỐNG KÊ ĐIỂM SỐ CHÍNH --}}
        <div style="display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; background: #f8fafc; padding: 18px 24px; border-radius: 14px; border: 1px solid #e2e8f0; margin: 10px auto 24px; max-width: 680px;">
            <div style="min-width: 120px;">
                <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Điểm số đạt được</div>
                <div style="font-size: 28px; font-weight: 800; color: #00b86b; margin-top: 4px;">
                    {{ $result['total_score'] }} <span style="font-size: 16px; color: #64748b; font-weight: 600;">/ {{ $result['max_score'] }}</span>
                </div>
            </div>

            <div style="border-left: 1px solid #cbd5e1;"></div>

            <div style="min-width: 120px;">
                <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Tỷ lệ chính xác</div>
                <div style="font-size: 28px; font-weight: 800; color: {{ $result['percentage'] >= 70 ? '#00b86b' : ($result['percentage'] >= 50 ? '#d97706' : '#ef4444') }}; margin-top: 4px;">
                    {{ $result['percentage'] }}%
                </div>
            </div>

            <div style="border-left: 1px solid #cbd5e1;"></div>

            <div style="min-width: 120px;">
                <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Thời gian hoàn thành</div>
                <div style="font-size: 20px; font-weight: 800; color: #1e293b; margin-top: 8px;">
                    {{ intdiv($result['time_spent'], 60) }}m {{ $result['time_spent'] % 60 }}s
                </div>
            </div>
        </div>

        {{-- CÁC HUY HIỆU THỐNG KÊ CHI TIẾT --}}
        <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
            <span style="background: #e6f9f0; color: #166534; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px;">
                ✓ Đúng: {{ $result['correct_count'] }} câu
            </span>
            <span style="background: #fee2e2; color: #991b1b; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px;">
                ✗ Sai: {{ $result['wrong_count'] }} câu
            </span>
            @if($result['unanswered_count'] > 0)
                <span style="background: #fef3c7; color: #92400e; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px;">
                    ○ Chưa làm: {{ $result['unanswered_count'] }} câu
                </span>
            @endif
        </div>
    </div>

    {{-- 2. CHI TIẾT TỪNG CÂU HỎI VÀ ĐÁP ÁN GIẢI THÍCH --}}
    <div class="card" style="border-radius: 16px; padding: 26px;">
        <h2 style="font-size: 19px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
             Chi tiết bài làm và giải thích đáp án
        </h2>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            @foreach($result['evaluations'] as $index => $eval)
                @php
                    $q = $eval['question'];
                    $isCorrect = $eval['is_correct'];
                    $hasAnswered = $eval['has_answered'];
                @endphp

                <div style="border: 1px solid {{ $isCorrect ? '#86efac' : ($hasAnswered ? '#fca5a5' : '#fde68a') }}; border-radius: 12px; padding: 18px 20px; background: {{ $isCorrect ? '#f0fdf4' : ($hasAnswered ? '#fff1f2' : '#fffbeb') }};">
                    {{-- Header từng câu: Số câu + Trạng thái Đúng/Sai/Chưa làm --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-weight: 800; font-size: 14px; color: #1e293b;">
                            Câu {{ $loop->iteration }}/{{ count($result['evaluations']) }}
                        </span>

                        <div>
                            @if($isCorrect)
                                <span style="background: #00b86b; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                    ✓ Chính xác (+{{ $eval['diem_dat'] }}đ)
                                </span>
                            @elseif($hasAnswered)
                                <span style="background: #ef4444; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                    ✗ Chưa chính xác (0đ)
                                </span>
                            @else
                                <span style="background: #f59e0b; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                    ⏱️ Hết giờ / Chưa làm
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Đề bài --}}
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.5; margin-bottom: 14px;">
                        {{ $q->noidung }}
                    </div>

                    {{-- Câu trả lời của học viên --}}
                    <div style="background: #ffffff; padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e2e8f0; font-size: 14px;">
                        <span style="font-weight: 700; color: #475569;">Câu trả lời của bạn:</span>
                        @if($hasAnswered)
                            <strong style="color: {{ $isCorrect ? '#00b86b' : '#ef4444' }}; margin-left: 6px;">
                                {{ $eval['user_answer_text'] }}
                            </strong>
                        @else
                            <em style="color: #94a3b8; margin-left: 6px;">(Không có câu trả lời do hết giờ)</em>
                        @endif
                    </div>

                    {{-- Đáp án chuẩn của hệ thống --}}
                    @if(!empty($eval['correct_answers']))
                        <div style="background: #ffffff; padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #86efac; font-size: 14px;">
                            <span style="font-weight: 700; color: #166534;">Đáp án chuẩn:</span>
                            @foreach($eval['correct_answers'] as $cAns)
                                <span style="background: #dcfce7; color: #166534; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-left: 6px;">
                                    {{ $cAns }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Lời giải thích --}}
                    @if(!empty($eval['giaithich']))
                        <div style="font-size: 13px; color: #334155; line-height: 1.6; margin-top: 8px; padding-top: 8px; border-top: 1px dashed rgba(0,0,0,0.1);">
                            💡 <strong>Giải thích chi tiết:</strong> {{ $eval['giaithich'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Nút điều hướng cuối trang --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 28px; border-top: 1px solid #f1f5f9; padding-top: 20px; flex-wrap: wrap; gap: 12px;">
            <a href="{{ route('practice.set.show', $set->code) }}" class="btn btn-primary" style="padding: 10px 24px;">
                 Làm lại mã đề này
            </a>

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('practice.select') }}" class="btn btn-outline">
                     Chọn mã đề khác
                </a>
                <a href="{{ route('practice.history') }}" class="btn btn-outline">
                     Lịch sử luyện tập
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

