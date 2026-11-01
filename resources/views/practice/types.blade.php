@extends('layouts.app')

@section('title', 'Danh sách dạng bài luyện tập - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; color: #1e293b;">📚 Danh mục dạng bài luyện tập DET</h1>
            <p style="color: #64748b; font-size: 15px; margin-top: 4px;">
                Chọn dạng bài bạn muốn rèn luyện để nâng cao phản xạ và cải thiện từng kỹ năng cho bài thi Duolingo English Test.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.bookmarks') }}" class="btn btn-outline btn-sm">⭐ Câu hỏi đã lưu</a>
            <a href="{{ route('practice.history') }}" class="btn btn-outline btn-sm">⏱️ Lịch sử luyện tập</a>
        </div>
    </div>
</div>

@php
    $skillNames = [
        'reading' => ['name' => 'Reading (Kỹ năng Đọc)', 'icon' => '📖', 'badge' => 'badge-reading', 'desc' => 'Rèn luyện khả năng nhận diện từ vựng, ngữ pháp và đọc hiểu đoạn văn tương tác.'],
        'listening' => ['name' => 'Listening (Kỹ năng Nghe)', 'icon' => '🎧', 'badge' => 'badge-listening', 'desc' => 'Nghe hiểu câu thoại thực tế, nhận diện từ vựng phát âm chuẩn và đàm thoại.'],
        'writing' => ['name' => 'Writing (Kỹ năng Viết)', 'icon' => '✍️', 'badge' => 'badge-writing', 'desc' => 'Miêu tả hình ảnh và viết bài luận tự do theo chủ đề học thuật hoặc đời sống.'],
        'speaking' => ['name' => 'Speaking (Kỹ năng Nói)', 'icon' => '🎙️', 'badge' => 'badge-speaking', 'desc' => 'Đọc thành tiếng, nói mô tả tranh và trả lời câu hỏi trực tiếp qua microphone.'],
    ];
@endphp

@foreach($groupedTypes as $skill => $skillTypes)
    @if($skillTypes->isNotEmpty())
        <div style="margin-bottom: 36px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-size: 24px;">{{ $skillNames[$skill]['icon'] ?? '📝' }}</span>
                <div>
                    <h2 style="font-size: 20px; font-weight: 700; color: #1e293b;">
                        {{ $skillNames[$skill]['name'] ?? ucfirst($skill) }}
                    </h2>
                    <p style="color: #64748b; font-size: 13px;">{{ $skillNames[$skill]['desc'] ?? '' }}</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                @foreach($skillTypes as $type)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <span class="badge {{ $skillNames[$skill]['badge'] ?? 'badge-user' }}">{{ $type->kynang }}</span>
                                @if(!empty($type->thoigian))
                                    <span style="font-size: 12px; color: #64748b;">⏳ {{ $type->thoigian }} giây</span>
                                @endif
                            </div>
                            <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                                {{ $type->ten }}
                            </h3>
                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
                                {{ $type->mota ?? 'Luyện tập câu hỏi định dạng chuẩn theo format đề thi Duolingo English Test.' }}
                            </p>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                            <span style="font-size: 12px; color: #94a3b8;">
                                Chấm: {{ ($type->cachcham ?? 'auto') === 'ai' ? '🤖 AI chấm' : '⚡ Tự động' }}
                            </span>
                            <a href="{{ route('practice.select', ['loai_id' => $type->id]) }}" class="btn btn-primary btn-sm">
                                Luyện tập ngay →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
@endsection

