@extends('layouts.app')

@section('title', 'Luyện tập câu hỏi #' . $question->id . ' - DET Practice')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <!-- THANH ĐIỀU HƯỚNG VÀ THỜI GIAN LÀM BÀI -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('practice.select', ['loai_id' => $question->loai_id ?? 1]) }}" style="color: var(--bento-muted); text-decoration: none; font-size: 14px;">
            ← Chọn câu hỏi khác
        </a>

        <div style="display: flex; align-items: center; gap: 14px;">
            <span class="badge badge-success" style="font-size: 12px; font-weight: 700;">Luyện tập tự do</span>
            <!-- Nút Bookmark / Đánh dấu câu yếu (LT07 - Chuẩn No-JS) -->
            <form method="POST" action="{{ route('practice.bookmark.toggle', $question->id) }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" 
                        class="btn btn-sm" 
                        style="font-size: 13px; font-weight: 700; border-radius: 8px; transition: all 0.2s; cursor: pointer; {{ $isBookmarked ? 'background: #fef3c7; color: #b45309; border: 1.5px solid #fcd34d;' : 'background: #ffffff; color: var(--bento-muted); border: 1.5px solid var(--bento-line);' }}">
                    <span>{{ $isBookmarked ? '⭐' : '☆' }}</span>
                    <span>{{ $isBookmarked ? 'Đã lưu câu hỏi' : 'Lưu câu hỏi này' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- KHUNG ĐỀ BÀI VÀ NỘI DUNG -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
            <div>
                <span class="badge {{ !empty($type->kynang) ? 'badge-' . $type->kynang : 'badge-user' }}">
                    {{ $type->ten ?? 'Duolingo English Test' }}
                </span>
                <span style="font-size: 13px; color: var(--bento-muted); margin-left: 8px;">
                    Độ khó: {{ $question->dokho ?? 5 }}/10
                </span>
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--bento-muted);">Điểm câu: {{ $question->diem ?? 1.0 }}</span>
        </div>

        @if(($type->cachcham ?? '') === 'ai' || in_array($type->kynang ?? '', ['writing', 'speaking']))
            <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="font-size: 13px; color: #166534; font-weight: 600;">
                    🤖 Dạng bài này hỗ trợ chấm điểm chuyên sâu bằng Trí tuệ nhân tạo (AI Grader).
                </div>
                <a href="{{ route('practice.ai.question', $question->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px; font-weight: 700;">
                    Làm bài với AI chuyên sâu (Thang DET 160) →
                </a>
            </div>
        @endif

        <!-- Nội dung câu hỏi -->
        <div style="margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--bento-heading); line-height: 1.6; margin-bottom: 16px;">
                {{ $question->noidung }}
            </h2>

            @php
                // Chuẩn hóa đường dẫn ảnh
                $imageUrl = '';
                if (!empty($question->anh)) {
                    $rawImg = trim($question->anh);
                    if (str_starts_with($rawImg, 'http://') || str_starts_with($rawImg, 'https://')) {
                        $imageUrl = $rawImg;
                    } else {
                        $cleanImg = ltrim(str_replace('\\', '/', $rawImg), '/');
                        if (file_exists(public_path($cleanImg))) {
                            $imageUrl = asset($cleanImg);
                        } elseif (file_exists(public_path('storage/' . $cleanImg))) {
                            $imageUrl = asset('storage/' . $cleanImg);
                        } elseif (file_exists(public_path('images/' . $cleanImg))) {
                            $imageUrl = asset('images/' . $cleanImg);
                        } elseif (file_exists(public_path('images/questions/' . basename($cleanImg)))) {
                            $imageUrl = asset('images/questions/' . basename($cleanImg));
                        } else {
                            $imageUrl = asset($cleanImg);
                        }
                    }
                }

                // Chuẩn hóa đường dẫn và định dạng âm thanh
                $audioUrl = '';
                $audioType = 'audio/mpeg';
                if (!empty($question->audio)) {
                    $rawAudio = trim($question->audio);
                    if (str_starts_with($rawAudio, 'http://') || str_starts_with($rawAudio, 'https://')) {
                        $audioUrl = $rawAudio;
                    } else {
                        $cleanAudio = ltrim(str_replace('\\', '/', $rawAudio), '/');
                        if (file_exists(public_path($cleanAudio))) {
                            $audioUrl = asset($cleanAudio);
                        } elseif (file_exists(public_path('storage/' . $cleanAudio))) {
                            $audioUrl = asset('storage/' . $cleanAudio);
                        } elseif (file_exists(public_path('audio/' . $cleanAudio))) {
                            $audioUrl = asset('audio/' . $cleanAudio);
                        } elseif (file_exists(public_path('audio/questions/' . basename($cleanAudio)))) {
                            $audioUrl = asset('audio/questions/' . basename($cleanAudio));
                        } else {
                            $audioUrl = asset($cleanAudio);
                        }
                    }

                    $ext = strtolower(pathinfo(parse_url($audioUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                    if ($ext === 'wav') {
                        $audioType = 'audio/wav';
                    } elseif ($ext === 'ogg') {
                        $audioType = 'audio/ogg';
                    } elseif ($ext === 'webm') {
                        $audioType = 'audio/webm';
                    } elseif ($ext === 'm4a' || $ext === 'mp4') {
                        $audioType = 'audio/mp4';
                    } else {
                        $audioType = 'audio/mpeg';
                    }
                }
            @endphp

            <!-- Hình ảnh đính kèm nếu có -->
            @if(!empty($imageUrl))
                <div style="text-align: center; margin: 20px 0;">
                    <img src="{{ $imageUrl }}" alt="Question Image" style="max-width: 100%; max-height: 380px; border-radius: 8px; border: 1px solid var(--bento-line);">
                </div>
            @endif

            <!-- Âm thanh audio nếu có (Sử dụng thẻ audio chuẩn HTML5, tương thích mọi trình duyệt) -->
            @if(!empty($audioUrl))
                <div style="background: var(--bento-subtle); border: 1px solid var(--bento-line); padding: 18px; border-radius: 12px; margin: 18px 0; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px;">
                        <span style="font-size: 18px;">🎧</span>
                        <span style="font-size: 14px; color: var(--bento-heading); font-weight: 700;">Nghe đoạn âm thanh câu hỏi</span>
                    </div>
                    <audio controls preload="auto" style="width: 100%; max-width: 520px; outline: none; border-radius: 30px;">
                        <source src="{{ $audioUrl }}" type="{{ $audioType }}">
                        <source src="{{ $audioUrl }}">
                        Trình duyệt của bạn không hỗ trợ phát âm thanh trực tiếp. Bạn có thể <a href="{{ $audioUrl }}" target="_blank" download style="color: var(--bento-orange); font-weight: 600;">tải file âm thanh tại đây</a>.
                    </audio>
                </div>
            @endif
        </div>

        <!-- FORM LÀM BÀI VÀ NỘP BÀI (LT04 - Chuẩn Form HTML No-JS) -->
        <form id="practice-single-form" method="POST" action="{{ route('practice.submit', $question->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="thoigianlam" id="single-thoigianlam" value="0">

            @php
                $subList = collect();
                $isNgheSub = false;
                if (isset($question->cauhoi_doc) && $question->cauhoi_doc->count() > 0) {
                    $subList = $question->cauhoi_doc;
                } elseif (isset($question->cauhoi_nghe) && $question->cauhoi_nghe->count() > 0) {
                    $subList = $question->cauhoi_nghe;
                    $isNgheSub = true;
                }
            @endphp

            @if($subList->count() > 0)
                <!-- Dạng câu hỏi Tương tác (Interactive Reading & Interactive Listening) -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 14px; font-weight: 800; color: #0284c7; margin-bottom: 16px; text-transform: uppercase;">
                        {{ $isNgheSub ? 'DANH SÁCH CÂU HỎI NGHE HIỂU TƯƠNG TÁC' : 'DANH SÁCH CÂU HỎI ĐỌC HIỂU TƯƠNG TÁC' }} ({{ $subList->count() }} câu):
                    </div>

                    @foreach($subList as $subIdx => $subQ)
                        <div style="background: #ffffff; border: 1.5px solid var(--bento-line); border-radius: 12px; padding: 20px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="font-weight: 700; color: var(--bento-heading); font-size: 15px; margin-bottom: 14px;">
                                Câu hỏi {{ $subIdx + 1 }}: {{ $subQ->noidung }}
                            </div>

                            @if(isset($subQ->luachon) && $subQ->luachon->count() > 0)
                                <div class="options-container">
                                    @foreach($subQ->luachon as $optIdx => $opt)
                                        <label for="sub-radio-{{ $subQ->id }}-{{ $opt->id }}" class="instant-option-card" style="cursor: pointer;">
                                            <input type="radio" 
                                                   name="sub_answers[{{ $subQ->id }}]" 
                                                   id="sub-radio-{{ $subQ->id }}-{{ $opt->id }}" 
                                                   value="{{ $opt->id }}" 
                                                   style="margin-right: 8px;">
                                            <div class="opt-badge">{{ chr(65 + $optIdx) }}</div>
                                            <div style="flex: 1; font-size: 15px; color: var(--bento-heading); font-weight: 600;">{{ $opt->noidung }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                {{-- Trường hợp điền từ / nhập câu trả lời cho Interactive --}}
                                <div>
                                    <input type="text" 
                                           id="sub-text-input-{{ $subQ->id }}"
                                           name="sub_answers[{{ $subQ->id }}]" 
                                           class="form-control" 
                                           placeholder="Nhập câu trả lời của bạn..." 
                                           style="width: 100%; font-size: 15px; padding: 10px 14px; border: 1.5px solid var(--bento-line); border-radius: 8px; box-sizing: border-box;">
                                </div>
                            @endif

                            {{-- Hỗ trợ No-JS xem gợi ý & đáp án giải thích --}}
                            <details style="margin-top: 10px;">
                                <summary style="font-size: 12px; font-weight: 600; color: var(--bento-muted); cursor: pointer;">
                                    👁️ Xem gợi ý & đáp án tham khảo (Không cần JavaScript)
                                </summary>
                                <div style="margin-top: 6px; padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #334155;">
                                    @if(!empty($subQ->dapandung))
                                        <strong style="color: #166534;">Đáp án chuẩn:</strong> <span style="background: #ffffff; padding: 2px 6px; border-radius: 4px; border: 1px solid #bbf7d0; font-weight: 700;">{{ $subQ->dapandung }}</span><br>
                                    @endif
                                    <strong>Giải thích:</strong> {{ $subQ->giaithich ?: ($question->giaithich ?: 'Hãy đối chiếu câu trong đoạn văn/audio để xác nhận thông tin chính xác.') }}
                                </div>
                            </details>
                        </div>
                    @endforeach
                </div>
            @elseif(isset($question->luachon) && count($question->luachon) > 0)
                <!-- Dạng câu hỏi trắc nghiệm: Chọn đáp án thuần HTML No-JS -->
                <div style="margin-bottom: 24px;">
                    <div class="options-container" id="single-opts-container">
                        @foreach($question->luachon as $index => $opt)
                            <label for="single-radio-{{ $opt->id }}" class="instant-option-card" style="cursor: pointer;">
                                <input type="radio" 
                                       name="luachon_id" 
                                       id="single-radio-{{ $opt->id }}"
                                       value="{{ $opt->id }}" 
                                       style="margin-right: 8px;">
                                <div class="opt-badge">
                                    {{ chr(65 + $index) }}
                                </div>
                                <div style="flex: 1; font-size: 15px; color: var(--bento-heading); font-weight: 600; line-height: 1.4;">
                                    {{ $opt->noidung }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @elseif((!empty($type->kynang) && $type->kynang === 'speaking') || (!empty($question->loai_kynang) && $question->loai_kynang === 'speaking'))
                <!-- Dạng Speaking: Tải lên file ghi âm để Trợ lý AI chấm điểm chuyên sâu -->
                <div style="margin-bottom: 24px;">
                    <label for="audio" style="display: block; font-weight: 700; color: var(--bento-heading); margin-bottom: 8px; font-size: 15px;">
                        🎙️ Tải lên file ghi âm bài nói của bạn để AI chấm điểm (Thang điểm DET 10 - 160):
                    </label>

                    <div style="padding: 20px 22px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; margin-bottom: 14px;">
                        <input type="file" 
                               id="audio" 
                               name="audio" 
                               accept="audio/*" 
                               class="form-control" 
                               required
                               style="font-size: 14px; padding: 10px; background: #ffffff; border: 1.5px dashed #16a34a; border-radius: 8px; width: 100%; box-sizing: border-box;">
                        <small style="display: block; color: #166534; font-size: 13px; font-weight: 600; margin-top: 8px;">
                            Hỗ trợ các định dạng file âm thanh: MP3, WAV, OGG, WEBM, M4A (Tối đa 10MB). Hệ thống AI sẽ phân tích phát âm, ngữ pháp và độ lưu loát.
                        </small>
                    </div>
                </div>
            @else
                <!-- Dạng câu hỏi điền từ / viết bài luận / nghe và gõ lại -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="noidung" style="font-weight: 700; color: var(--bento-ink); font-size: 14px; margin-bottom: 8px; display: block;">
                        Nhập câu trả lời của bạn:
                    </label>
                    <textarea id="noidung" 
                              name="noidung" 
                              rows="6" 
                              class="form-control" 
                              placeholder="Gõ câu trả lời của bạn tại đây bằng tiếng Anh..." 
                              required 
                              autofocus 
                              style="width: 100%; box-sizing: border-box; font-family: inherit; font-size: 15px; line-height: 1.6; padding: 12px; border: 1.5px solid var(--bento-line); border-radius: 8px;"></textarea>
                    @error('noidung')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 14px; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-weight: 700; border-radius: 8px;">
                    Nộp bài làm
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/practice.css') }}">
<style>
.instant-option-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    border: 2px solid var(--bento-line);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    margin-bottom: 10px;
}
.instant-option-card:hover {
    border-color: var(--bento-orange);
    background: #fffbf5;
}
.opt-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--bento-subtle);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: var(--bento-heading);
}
</style>
@endpush
@endsection
