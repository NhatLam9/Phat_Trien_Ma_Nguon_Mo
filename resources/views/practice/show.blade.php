@extends('layouts.app')

@section('title', 'Luyện tập câu hỏi #' . $question->id . ' - DET Practice')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <!-- THANH ĐIỀU HƯỚNG VÀ THỜI GIAN LÀM BÀI -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('practice.select', ['loai_id' => $question->loai_id ?? 1]) }}" style="color: #64748b; text-decoration: none; font-size: 14px;">
            ← Chọn câu hỏi khác
        </a>

        <div style="display: flex; align-items: center; gap: 14px;">
            <!-- Nút Bookmark / Đánh dấu câu yếu (LT07) -->
            <form method="POST" action="{{ route('practice.bookmark.toggle', $question->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-danger' : 'btn-outline' }}" style="font-size: 13px;">
                    {{ $isBookmarked ? '⭐ Đã lưu câu hỏi' : '☆ Đánh dấu câu yếu' }}
                </button>
            </form>

            <!-- Thời gian làm bài theo format đề thi DET -->
            @if(!empty($question->thoigian))
                <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 6px 14px; border-radius: 20px; font-weight: 700; color: #00b86b; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                    <span>⏱️ Giới hạn:</span>
                    <span>{{ $question->thoigian }} giây</span>
                </div>
            @endif
        </div>
    </div>

    <!-- KHUNG ĐỀ BÀI VÀ NỘI DUNG -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
            <div>
                <span class="badge {{ !empty($type->kynang) ? 'badge-' . $type->kynang : 'badge-user' }}">
                    {{ $type->ten ?? 'Duolingo English Test' }}
                </span>
                <span style="font-size: 13px; color: #64748b; margin-left: 8px;">
                    Độ khó: {{ $question->dokho ?? 5 }}/10
                </span>
            </div>
            <span style="font-size: 13px; font-weight: 600; color: #64748b;">Điểm câu: {{ $question->diem ?? 1.0 }}</span>
        </div>

        <!-- Nội dung câu hỏi -->
        <div style="margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.6; margin-bottom: 16px;">
                {{ $question->noidung }}
            </h2>

            <!-- Hình ảnh đính kèm nếu có -->
            @if(!empty($question->anh))
                <div style="text-align: center; margin: 20px 0;">
                    <img src="{{ str_starts_with($question->anh, 'http') ? $question->anh : asset('storage/' . $question->anh) }}" alt="Question Image" style="max-width: 100%; max-height: 380px; border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
            @endif

            <!-- Âm thanh audio nếu có (Sử dụng thẻ audio chuẩn HTML5, không dùng JavaScript) -->
            @if(!empty($question->audio))
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin: 16px 0; text-align: center;">
                    <p style="font-size: 13px; color: #475569; margin-bottom: 8px; font-weight: 600;">🎧 Nghe đoạn âm thanh:</p>
                    <audio controls style="width: 100%; max-width: 500px;">
                        <source src="{{ str_starts_with($question->audio, 'http') ? $question->audio : asset('storage/' . $question->audio) }}" type="audio/mpeg">
                        Trình duyệt của bạn không hỗ trợ phát âm thanh.
                    </audio>
                </div>
            @endif
        </div>

        <!-- FORM LÀM BÀI VÀ NỘP BÀI (LT04 - Thuần HTML5, hỗ trợ nộp bài viết và tải file ghi âm) -->
        <form method="POST" action="{{ route('practice.submit', $question->id) }}" enctype="multipart/form-data">
            @csrf

            @if(isset($question->luachon) && count($question->luachon) > 0)
                <!-- Dạng câu hỏi trắc nghiệm: hiển thị danh sách lựa chọn (A/B/C/D) -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 12px; font-size: 14px;">
                        Chọn đáp án của bạn:
                    </label>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($question->luachon as $index => $opt)
                            <label style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.15s; background: #ffffff;">
                                <input type="radio" name="luachon_id" value="{{ $opt->id }}" required style="accent-color: #00b86b; width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #00b86b; width: 22px;">{{ chr(65 + $index) }}.</span>
                                <span style="font-size: 15px; color: #1e293b;">{{ $opt->noidung }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @elseif(!empty($type->kynang) && $type->kynang === 'speaking')
                <!-- Dạng Speaking: Người học tải lên file ghi âm theo quy chuẩn không dùng JavaScript (mục 1.3) -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="audio" style="font-weight: 700; color: #334155; font-size: 14px;">
                        🎙️ Tải lên file ghi âm bài nói của bạn:
                    </label>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;">
                        Bạn hãy sử dụng ứng dụng Ghi âm (Voice Recorder) trên thiết bị của mình để ghi âm câu trả lời, sau đó chọn tệp để tải lên bên dưới.
                    </p>
                    <input type="file" id="audio" name="audio" accept="audio/*" class="form-control" required style="font-size: 14px; padding: 8px;">
                    <small style="display: block; color: #64748b; font-size: 12px; margin-top: 6px;">
                        Hỗ trợ định dạng: MP3, WAV, OGG, WEBM, M4A. Dung lượng tối đa: 10MB.
                    </small>
                    @error('audio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <!-- Dạng câu hỏi điền từ / viết bài văn luận -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="noidung" style="font-weight: 700; color: #334155; font-size: 14px;">
                        Nhập câu trả lời của bạn:
                    </label>
                    <textarea id="noidung" name="noidung" rows="6" class="form-control" placeholder="Gõ câu trả lời của bạn tại đây bằng tiếng Anh..." required autofocus style="font-family: inherit; font-size: 15px; line-height: 1.6;"></textarea>
                    @error('noidung')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 14px; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <button type="submit" class="btn btn-primary" style="padding: 11px 28px; font-size: 15px;">
                    Nộp bài làm →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
