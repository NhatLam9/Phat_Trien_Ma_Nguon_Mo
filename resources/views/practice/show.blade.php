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
            <span class="badge badge-success" style="font-size: 12px; font-weight: 700;">Luyện tập tự do</span>
            <!-- Nút Bookmark / Đánh dấu câu yếu (LT07) -->
            <button type="button" 
                    id="single-bookmark-btn" 
                    onclick="toggleSingleBookmark({{ $question->id }})" 
                    class="btn btn-sm" 
                    style="font-size: 13px; font-weight: 700; border-radius: 8px; transition: all 0.2s; {{ $isBookmarked ? 'background: #fef3c7; color: #b45309; border: 1.5px solid #fcd34d;' : 'background: #ffffff; color: #64748b; border: 1.5px solid #cbd5e1;' }}">
                <span id="single-bm-star">{{ $isBookmarked ? '⭐' : '☆' }}</span>
                <span id="single-bm-label">{{ $isBookmarked ? 'Đã lưu câu hỏi' : 'Lưu câu hỏi này' }}</span>
            </button>
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
                    <img src="{{ $imageUrl }}" alt="Question Image" style="max-width: 100%; max-height: 380px; border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
            @endif

            <!-- Âm thanh audio nếu có (Sử dụng thẻ audio chuẩn HTML5, tương thích mọi trình duyệt) -->
            @if(!empty($audioUrl))
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 18px; border-radius: 12px; margin: 18px 0; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px;">
                        <span style="font-size: 18px;">🎧</span>
                        <span style="font-size: 14px; color: #1e293b; font-weight: 700;">Nghe đoạn âm thanh câu hỏi</span>
                    </div>
                    <audio controls preload="auto" style="width: 100%; max-width: 520px; outline: none; border-radius: 30px;">
                        <source src="{{ $audioUrl }}" type="{{ $audioType }}">
                        <source src="{{ $audioUrl }}">
                        Trình duyệt của bạn không hỗ trợ phát âm thanh trực tiếp. Bạn có thể <a href="{{ $audioUrl }}" target="_blank" download style="color: #00b86b; font-weight: 600;">tải file âm thanh tại đây</a>.
                    </audio>
                </div>
            @endif
        </div>

        <!-- FORM LÀM BÀI VÀ NỘP BÀI (LT04 )
        <form id="practice-single-form" method="POST" action="{{ route('practice.submit', $question->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="thoigianlam" id="single-thoigianlam" value="0">

            @if(isset($question->cauhoi_doc) && $question->cauhoi_doc->count() > 0)
                <!-- Dạng câu hỏi Đọc hiểu tương tác (Interactive Reading) -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 14px; font-weight: 800; color: #0284c7; margin-bottom: 16px; text-transform: uppercase;">
                        DANH SÁCH CÂU HỎI ĐỌC HIỂU TƯƠNG TÁC ({{ $question->cauhoi_doc->count() }} câu):
                    </div>

                    @foreach($question->cauhoi_doc as $subIdx => $subQ)
                        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="font-weight: 700; color: #1e293b; font-size: 15px; margin-bottom: 14px;">
                                Câu hỏi {{ $subIdx + 1 }}: {{ $subQ->noidung }}
                            </div>

                            @if(isset($subQ->luachon) && $subQ->luachon->count() > 0)
                                <div class="options-container" id="single-sub-opts-{{ $subQ->id }}">
                                    @foreach($subQ->luachon as $optIdx => $opt)
                                        <div class="instant-option-card" 
                                             id="sub-opt-{{ $subQ->id }}-{{ $opt->id }}"
                                             data-is-correct="{{ !empty($opt->ladung) ? 1 : 0 }}"
                                             onclick="handleSelectSingleSubChoice({{ $subQ->id }}, {{ $opt->id }}, {{ !empty($opt->ladung) ? 1 : 0 }}, this)">
                                            <input type="radio" 
                                                   name="sub_answers[{{ $subQ->id }}]" 
                                                   id="sub-radio-{{ $subQ->id }}-{{ $opt->id }}" 
                                                   value="{{ $opt->id }}" 
                                                   style="display: none;">
                                            <div class="opt-badge">{{ chr(65 + $optIdx) }}</div>
                                            <div style="flex: 1; font-size: 15px; color: #1e293b; font-weight: 600;">{{ $opt->noidung }}</div>
                                            <div class="opt-status-tag" style="font-size: 13px; font-weight: 800; display: none;"></div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Hộp giải thích tức thì cho câu con có trắc nghiệm --}}
                                <div class="feedback-box" id="sub-feedback-box-{{ $subQ->id }}" style="display: none;">
                                    <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                        <span class="fb-icon"></span>
                                        <span class="fb-text"></span>
                                    </div>
                                    <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                        <strong>Giải thích chi tiết:</strong>
                                        <div style="margin-top: 4px;">
                                            {{ $subQ->giaithich ?: ($question->giaithich ?: 'Hãy đối chiếu câu trong đoạn văn để xác nhận thông tin chính xác.') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Trường hợp điền từ / nhập câu trả lời cho Interactive Reading --}}
                                <div>
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <input type="text" 
                                               id="sub-text-input-{{ $subQ->id }}"
                                               name="sub_answers[{{ $subQ->id }}]" 
                                               class="form-control" 
                                               placeholder="Nhập câu trả lời của bạn..." 
                                               style="flex: 1; min-width: 220px; font-size: 15px; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                                        <button type="button" 
                                                class="btn btn-primary" 
                                                onclick="checkSingleSubTextInput({{ $subQ->id }}, '{{ addslashes($subQ->dapandung ?? '') }}')" 
                                                style="padding: 10px 18px; font-weight: 700; border-radius: 8px; white-space: nowrap;">
                                            Kiểm tra câu này
                                        </button>
                                    </div>

                                    <div class="feedback-box" id="sub-feedback-box-{{ $subQ->id }}" style="display: none; margin-top: 12px;">
                                        <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                            <span class="fb-icon"></span>
                                            <span class="fb-text"></span>
                                        </div>
                                        <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                            @if(!empty($subQ->dapandung))
                                                <div style="margin-bottom: 6px; color: #166534; font-weight: 700;">
                                                    Đáp án đúng: <span style="background: #eefcf5; padding: 2px 8px; border-radius: 4px; border: 1px solid #86efac;">{{ $subQ->dapandung }}</span>
                                                </div>
                                            @endif
                                            <strong>Giải thích chi tiết:</strong>
                                            <div style="margin-top: 4px;">
                                                {{ $subQ->giaithich ?: ($question->giaithich ?: 'Hãy đối chiếu câu trong đoạn văn để xác nhận thông tin chính xác.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif(isset($question->luachon) && count($question->luachon) > 0)
                <!-- Dạng câu hỏi trắc nghiệm: Chọn đáp án hiển thị kết quả và giải thích tức thì -->
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">


                    </div>

                    <div class="options-container" id="single-opts-container">
                        @foreach($question->luachon as $index => $opt)
                            <div class="instant-option-card" 
                                 id="single-opt-card-{{ $opt->id }}"
                                 data-is-correct="{{ !empty($opt->ladung) ? 1 : 0 }}"
                                 onclick="handleSelectSingleChoice({{ $opt->id }}, {{ !empty($opt->ladung) ? 1 : 0 }}, this)">
                                <input type="radio" 
                                       name="luachon_id" 
                                       id="single-radio-{{ $opt->id }}"
                                       value="{{ $opt->id }}" 
                                       style="display: none;">
                                
                                <div class="opt-badge">
                                    {{ chr(65 + $index) }}
                                </div>

                                <div style="flex: 1; font-size: 15px; color: #1e293b; font-weight: 600; line-height: 1.4;">
                                    {{ $opt->noidung }}
                                </div>

                                <div class="opt-status-tag" style="font-size: 13px; font-weight: 800; display: none;"></div>
                            </div>
                        @endforeach
                    </div>

                    {{-- HỘP HIỆN GIẢI THÍCH & KẾT QUẢ TỨC THÌ --}}
                    <div class="feedback-box" id="single-feedback-box" style="display: none; margin-top: 18px;">
                        <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <span class="fb-icon"></span>
                            <span class="fb-text"></span>
                        </div>
                        <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 14px 18px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                            <div class="single-correct-answer-display" style="margin-bottom: 8px; font-size: 14px; color: #166534; font-weight: 700;"></div>
                            <strong>Giải thích chi tiết:</strong>
                            <div style="margin-top: 4px;">
                                {{ $question->giaithich ?: 'Hãy chú ý ngữ cảnh và cấu trúc ngữ pháp để chọn đáp án chuẩn xác nhất.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!empty($type->kynang) && $type->kynang === 'speaking')
                <!-- Dạng Speaking: Luyện tập nói với Voice Recording Studio & AI chấm điểm -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px; font-size: 15px;">
                        🎙️ Ghi âm bài nói của bạn để AI chấm điểm:
                    </label>

                    <input type="hidden" name="audio_path" id="single-audio-path" value="">
                    <input type="hidden" name="diem_ai" id="single-diem-ai" value="">
                    <input type="hidden" name="nhanxet_ai" id="single-nhanxet-ai" value="">

                    <div class="voice-recording-studio" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 26px 20px; text-align: center; margin-bottom: 18px;">
                        <div id="voice-controls">
                            <div style="font-size: 14px; color: #475569; margin-bottom: 16px; font-weight: 600;">
                                Nhấn nút Micro bên dưới và đọc câu/trả lời vào mic. Sau khi ghi âm, bạn có thể nghe lại và nhấn <strong>AI kiểm tra</strong> để nhận nhận xét tức thì.
                            </div>

                            <!-- Nút Micro ghi âm -->
                            <button type="button" 
                                    id="btn-voice-toggle" 
                                    onclick="toggleSingleRecording({{ $question->id }})"
                                    style="width: 82px; height: 82px; border-radius: 50%; background: #ef4444; color: #ffffff; border: none; font-size: 34px; cursor: pointer; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4); transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;">
                                🎙️
                            </button>

                            <div id="voice-status-text" style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 14px;">
                                Nhấn vào nút Micro để bắt đầu ghi âm bài nói
                            </div>

                            <div id="voice-timer-box" style="display: none; margin-top: 12px;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; background: #fee2e2; color: #991b1b; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-family: monospace; font-size: 16px;">
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; animation: pulseRed 1s infinite;"></span>
                                    <span id="voice-time">00:00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Trình phát nghe lại và nút gửi AI -->
                        <div id="voice-playback-box" style="display: none; margin-top: 18px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                            <p style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                🎧 Nghe lại bài nói vừa ghi âm:
                            </p>
                            <audio id="voice-audio-playback" controls style="width: 100%; max-width: 460px; margin-bottom: 16px;"></audio>

                            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="resetSingleRecording()" style="font-weight: 700;">
                                    Ghi âm lại
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" id="btn-ai-check" onclick="sendSingleVoiceToAI({{ $question->id }})" style="font-weight: 800; background: #00b86b; border-color: #00b86b; padding: 8px 20px;">
                                     Nhấn để AI kiểm tra bài nói ngay
                                </button>
                            </div>
                        </div>

                        <!-- Loading AI -->
                        <div id="voice-ai-loading" style="display: none; margin-top: 18px;">
                            <div style="display: inline-flex; align-items: center; gap: 10px; color: #0284c7; font-weight: 700; font-size: 14px;">
                                <span class="spinner-border" style="width: 22px; height: 22px; border: 3px solid #0284c7; border-top-color: transparent; border-radius: 50%; display: inline-block; animation: spin 0.8s linear infinite;"></span>
                                <span>AI đang lắng nghe và chấm điểm phát âm & độ lưu loát...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ hiển thị kết quả AI đánh giá -->
                    <div class="card" id="ai-result-card" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 14px; padding: 20px 22px; margin-top: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 24px;">✨</span>
                                <strong style="font-size: 16px; color: #166534;">Kết quả AI đánh giá bài nói (Duolingo AI Assessor)</strong>
                            </div>
                            <div style="background: #16a34a; color: #ffffff; font-weight: 800; padding: 5px 16px; border-radius: 20px; font-size: 14px;">
                                Điểm bài nói: <span id="ai-score-text">--</span> / {{ $question->diem ?? 10 }}
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 14px;">
                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">🗣️ Phát âm (Pronunciation):</span>
                                <span id="ai-pronunciation" style="font-size: 13px; color: #1e293b;">--</span>
                            </div>
                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">⚡ Độ lưu loát (Fluency):</span>
                                <span id="ai-fluency" style="font-size: 13px; color: #1e293b;">--</span>
                            </div>
                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">🎯 DET Ước tính:</span>
                                <span id="ai-det" style="font-size: 13px; color: #1e293b; font-weight: 800;">--</span>
                            </div>
                        </div>

                        <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 10px;">
                            <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">💡 Góp ý cải thiện:</span>
                            <span id="ai-tips" style="font-size: 13px; color: #334155;">--</span>
                        </div>
                    </div>

                    <!-- Tùy chọn tải lên tệp âm thanh có sẵn từ máy tính nếu không dùng microphone -->
                    <details style="margin-top: 14px; font-size: 13px; color: #64748b;">
                        <summary style="cursor: pointer; font-weight: 600; color: #475569;">
                             Hoặc tải lên file ghi âm có sẵn từ máy tính
                        </summary>
                        <div style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-top: 8px;">
                            <input type="file" id="audio" name="audio" accept="audio/*" class="form-control" style="font-size: 13px; padding: 6px;">
                            <small style="display: block; color: #64748b; font-size: 12px; margin-top: 4px;">
                                Hỗ trợ: MP3, WAV, OGG, WEBM, M4A (Tối đa 10MB)
                            </small>
                        </div>
                    </details>
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
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    margin-bottom: 12px;
    position: relative;
}
.instant-option-card:hover {
    border-color: #00b86b;
    background: #f8fafc;
    transform: translateY(-1px);
}
.instant-option-card.is-correct {
    border-color: #16a34a !important;
    background-color: #f0fdf4 !important;
}
.instant-option-card.is-correct .opt-badge {
    background: #16a34a !important;
    color: #ffffff !important;
}
.instant-option-card.is-wrong {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}
.instant-option-card.is-wrong .opt-badge {
    background: #ef4444 !important;
    color: #ffffff !important;
}
.instant-option-card.revealed-correct {
    border-color: #16a34a !important;
    background-color: #f0fdf4 !important;
    border-style: dashed !important;
}
.opt-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e2e8f0;
    font-weight: 800;
    font-size: 14px;
    color: #475569;
    flex-shrink: 0;
    transition: all 0.2s ease;
}
.feedback-box {
    border-radius: 12px;
    padding: 16px 20px;
    margin-top: 18px;
    animation: fadeIn 0.3s ease-in-out;
    border: 1px solid transparent;
}
.feedback-box.correct {
    background: #f0fdf4;
    border-color: #86efac;
    color: #166534;
}
.feedback-box.wrong {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #991b1b;
}
@keyframes pulseRed {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.35); opacity: 0.6; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const thoigianlamEl = document.getElementById('single-thoigianlam');
    let elapsed = 0;
    setInterval(function() {
        elapsed++;
        if (thoigianlamEl) thoigianlamEl.value = elapsed;
    }, 1000);
})();

// Xử lý Voice Recording và AI kiểm tra cho câu hỏi đơn lẻ
let singleMediaRecorder = null;
let singleAudioChunks = [];
let singleRecordInterval = null;
let singleRecordSeconds = 0;
let singleAudioBlob = null;
let singleIsRecording = false;

function toggleSingleRecording(qid) {
    if (singleIsRecording) {
        stopSingleRecording();
    } else {
        startSingleRecording(qid);
    }
}

async function startSingleRecording(qid) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        singleAudioChunks = [];
        
        let options = {};
        if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
            options = { mimeType: 'audio/webm;codecs=opus' };
        } else if (MediaRecorder.isTypeSupported('audio/webm')) {
            options = { mimeType: 'audio/webm' };
        }

        singleMediaRecorder = new MediaRecorder(stream, options);

        singleMediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) {
                singleAudioChunks.push(e.data);
            }
        };

        singleMediaRecorder.onstop = () => {
            singleAudioBlob = new Blob(singleAudioChunks, { type: singleMediaRecorder.mimeType || 'audio/webm' });
            const audioUrl = URL.createObjectURL(singleAudioBlob);
            const audioEl = document.getElementById('voice-audio-playback');
            if (audioEl) {
                audioEl.src = audioUrl;
            }

            const playbackBox = document.getElementById('voice-playback-box');
            if (playbackBox) playbackBox.style.display = 'block';

            const statusText = document.getElementById('voice-status-text');
            if (statusText) statusText.innerHTML = '<span style="color:#00b86b;">✓ Ghi âm hoàn tất!</span> Hãy nghe lại hoặc nhấn "AI kiểm tra"';

            stream.getTracks().forEach(track => track.stop());
        };

        singleMediaRecorder.start(250);
        singleIsRecording = true;

        const btn = document.getElementById('btn-voice-toggle');
        if (btn) {
            btn.style.background = '#0f172a';
            btn.innerHTML = '⏹️';
        }

        const statusText = document.getElementById('voice-status-text');
        if (statusText) statusText.innerText = 'Đang ghi âm bài nói... Nhấn để dừng!';

        const timerBox = document.getElementById('voice-timer-box');
        if (timerBox) timerBox.style.display = 'block';

        singleRecordSeconds = 0;
        clearInterval(singleRecordInterval);
        singleRecordInterval = setInterval(() => {
            singleRecordSeconds++;
            const m = String(Math.floor(singleRecordSeconds / 60)).padStart(2, '0');
            const s = String(singleRecordSeconds % 60).padStart(2, '0');
            const timeEl = document.getElementById('voice-time');
            if (timeEl) timeEl.innerText = `${m}:${s}`;
        }, 1000);

    } catch (err) {
        console.error('Microphone error:', err);
        alert('Không thể truy cập microphone. Vui lòng cấp quyền micro cho trình duyệt hoặc tải file ghi âm lên.');
    }
}

function stopSingleRecording() {
    if (singleMediaRecorder && singleMediaRecorder.state !== 'inactive') {
        singleMediaRecorder.stop();
    }
    singleIsRecording = false;
    clearInterval(singleRecordInterval);

    const btn = document.getElementById('btn-voice-toggle');
    if (btn) {
        btn.style.background = '#ef4444';
        btn.innerHTML = '🎙️';
    }

    const timerBox = document.getElementById('voice-timer-box');
    if (timerBox) timerBox.style.display = 'none';
}

function resetSingleRecording() {
    stopSingleRecording();
    singleAudioChunks = [];
    singleAudioBlob = null;
    singleRecordSeconds = 0;

    const playbackBox = document.getElementById('voice-playback-box');
    if (playbackBox) playbackBox.style.display = 'none';

    const resultCard = document.getElementById('ai-result-card');
    if (resultCard) resultCard.style.display = 'none';

    const statusText = document.getElementById('voice-status-text');
    if (statusText) statusText.innerText = 'Nhấn vào nút Micro để bắt đầu ghi âm bài nói';
}

async function sendSingleVoiceToAI(qid) {
    if (!singleAudioBlob) {
        alert('Chưa có đoạn ghi âm. Vui lòng ghi âm trước khi gửi AI kiểm tra!');
        return;
    }

    const loadingEl = document.getElementById('voice-ai-loading');
    const btnAi = document.getElementById('btn-ai-check');

    if (loadingEl) loadingEl.style.display = 'block';
    if (btnAi) {
        btnAi.disabled = true;
        btnAi.innerText = '⏳ Đang phân tích...';
    }

    const formData = new FormData();
    formData.append('cauhoi_id', qid);
    formData.append('audio', singleAudioBlob, 'voice_' + qid + '.webm');
    formData.append('duration', singleRecordSeconds);
    formData.append('_token', '{{ csrf_token() }}');

    try {
        const response = await fetch('{{ route('practice.speaking.evaluate') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (loadingEl) loadingEl.style.display = 'none';
        if (btnAi) {
            btnAi.disabled = false;
            btnAi.innerText = 'Kiểm tra lại với AI';
        }

        if (data.success) {
            const resultCard = document.getElementById('ai-result-card');
            if (resultCard) resultCard.style.display = 'block';

            const scoreText = document.getElementById('ai-score-text');
            if (scoreText) scoreText.innerText = data.score;

            const pronunc = document.getElementById('ai-pronunciation');
            if (pronunc) pronunc.innerText = data.feedback.pronunciation;

            const fluency = document.getElementById('ai-fluency');
            if (fluency) fluency.innerText = data.feedback.fluency;

            const det = document.getElementById('ai-det');
            if (det) det.innerText = data.det_estimated;

            const tips = document.getElementById('ai-tips');
            if (tips) tips.innerText = data.feedback.tips;

            // Cập nhật giá trị vào form nộp bài
            if (data.audio_url) {
                const audioPathInput = document.getElementById('single-audio-path');
                if (audioPathInput) audioPathInput.value = data.audio_url;
            }
            const diemInput = document.getElementById('single-diem-ai');
            if (diemInput) diemInput.value = data.score;
            const nhanxetInput = document.getElementById('single-nhanxet-ai');
            if (nhanxetInput) nhanxetInput.value = data.nhanxet_ai;

            resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            alert('AI không thể xử lý đoạn ghi âm lúc này. Vui lòng thử lại!');
        }
    } catch (e) {
        console.error('AI check error:', e);
        if (loadingEl) loadingEl.style.display = 'none';
        if (btnAi) {
            btnAi.disabled = false;
            btnAi.innerText = 'Nhấn để AI kiểm tra bài nói ngay';
        }
        alert('Lỗi kết nối khi gửi bài nói tới AI: ' + e.message);
    }
}

function handleSelectSingleChoice(optId, isCorrect, clickedCard) {
    const container = document.getElementById('single-opts-container');
    if (!container) return;

    // Reset all cards
    const allCards = container.querySelectorAll('.instant-option-card');
    allCards.forEach(card => {
        card.classList.remove('is-correct', 'is-wrong', 'revealed-correct');
        const tag = card.querySelector('.opt-status-tag');
        if (tag) tag.style.display = 'none';
    });

    // Check the radio input
    const radio = document.getElementById('single-radio-' + optId);
    if (radio) {
        radio.checked = true;
    }

    const feedbackBox = document.getElementById('single-feedback-box');
    const fbIcon = feedbackBox ? feedbackBox.querySelector('.fb-icon') : null;
    const fbText = feedbackBox ? feedbackBox.querySelector('.fb-text') : null;
    const correctDisplay = feedbackBox ? feedbackBox.querySelector('.single-correct-answer-display') : null;

    if (isCorrect === 1) {
        clickedCard.classList.add('is-correct');
        const tag = clickedCard.querySelector('.opt-status-tag');
        if (tag) {
            tag.innerText = '✓ Chính xác (+{{ $question->diem ?? 1.0 }} điểm)';
            tag.style.color = '#16a34a';
            tag.style.display = 'block';
        }

        if (feedbackBox) {
            feedbackBox.className = 'feedback-box correct';
            if (fbIcon) fbIcon.textContent = '✓';
            if (fbText) fbText.textContent = 'Chính xác! Làm rất tốt!';
            if (correctDisplay) correctDisplay.style.display = 'none';
            feedbackBox.style.display = 'block';
        }
    } else {
        clickedCard.classList.add('is-wrong');
        const wrongTag = clickedCard.querySelector('.opt-status-tag');
        if (wrongTag) {
            wrongTag.innerText = '✗ Chưa chính xác';
            wrongTag.style.color = '#dc2626';
            wrongTag.style.display = 'block';
        }

        // Highlight correct card
        allCards.forEach(card => {
            if (card.getAttribute('data-is-correct') === '1') {
                card.classList.add('revealed-correct');
                const tag = card.querySelector('.opt-status-tag');
                if (tag) {
                    tag.innerText = '★ Đáp án đúng';
                    tag.style.color = '#15803d';
                    tag.style.display = 'block';
                }
            }
        });

        if (feedbackBox) {
            feedbackBox.className = 'feedback-box wrong';
            if (fbIcon) fbIcon.textContent = '💡';
            if (fbText) fbText.textContent = 'Chưa chính xác! Hãy đối chiếu đáp án chuẩn bên dưới:';
            if (correctDisplay) {
                const correctCard = container.querySelector('.instant-option-card[data-is-correct="1"]');
                const correctText = correctCard ? correctCard.querySelector('div:nth-child(3)').innerText : '';
                correctDisplay.innerHTML = 'Đáp án chuẩn: <span style="background: #ffffff; padding: 2px 8px; border-radius: 4px; border: 1px solid #86efac;">' + correctText + '</span>';
                correctDisplay.style.display = 'block';
            }
            feedbackBox.style.display = 'block';
        }
    }

    if (feedbackBox) {
        feedbackBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function handleSelectSingleSubChoice(subId, optId, isCorrect, clickedCard) {
    const container = document.getElementById('single-sub-opts-' + subId);
    if (!container) return;

    const allCards = container.querySelectorAll('.instant-option-card');
    allCards.forEach(card => {
        card.classList.remove('is-correct', 'is-wrong', 'revealed-correct');
        const tag = card.querySelector('.opt-status-tag');
        if (tag) tag.style.display = 'none';
    });

    const radio = document.getElementById('sub-radio-' + subId + '-' + optId);
    if (radio) {
        radio.checked = true;
    }

    const feedbackBox = document.getElementById('sub-feedback-box-' + subId);

    if (isCorrect === 1) {
        clickedCard.classList.add('is-correct');
        if (feedbackBox) {
            feedbackBox.className = 'feedback-box correct';
            feedbackBox.querySelector('.fb-icon').textContent = '✓';
            feedbackBox.querySelector('.fb-text').textContent = 'Chính xác!';
            feedbackBox.style.display = 'block';
        }
    } else {
        clickedCard.classList.add('is-wrong');
        allCards.forEach(card => {
            if (card.getAttribute('data-is-correct') === '1') {
                card.classList.add('revealed-correct');
            }
        });
        if (feedbackBox) {
            feedbackBox.className = 'feedback-box wrong';
            feedbackBox.querySelector('.fb-icon').textContent = '💡';
            feedbackBox.querySelector('.fb-text').textContent = 'Chưa chính xác! Hãy xem gợi ý giải thích:';
            feedbackBox.style.display = 'block';
        }
    }
}

function checkSingleSubTextInput(subId, correctAnswer) {
    const input = document.getElementById('sub-text-input-' + subId);
    const feedbackBox = document.getElementById('sub-feedback-box-' + subId);
    if (!input || !feedbackBox) return;

    const val = input.value.trim().toLowerCase();
    const correct = (correctAnswer || '').trim().toLowerCase();
    const isCorrect = (val.length > 0 && (val === correct || (correct.length > 0 && correct.includes(val))));

    if (isCorrect) {
        input.style.borderColor = '#16a34a';
        input.style.backgroundColor = '#f0fdf4';
        feedbackBox.className = 'feedback-box correct';
        feedbackBox.querySelector('.fb-icon').textContent = '✓';
        feedbackBox.querySelector('.fb-text').textContent = 'Chính xác!';
    } else {
        input.style.borderColor = '#dc2626';
        input.style.backgroundColor = '#fef2f2';
        feedbackBox.className = 'feedback-box wrong';
        feedbackBox.querySelector('.fb-icon').textContent = '💡';
        feedbackBox.querySelector('.fb-text').textContent = 'Chưa chính xác! Hãy đối chiếu đáp án chuẩn bên dưới:';
    }
    feedbackBox.style.display = 'block';
}

async function toggleSingleBookmark(qid) {
    const btn = document.getElementById('single-bookmark-btn');
    const star = document.getElementById('single-bm-star');
    const label = document.getElementById('single-bm-label');

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
                if (star) star.innerText = '⭐';
                if (label) label.innerText = 'Đã lưu câu hỏi';
            } else {
                if (btn) {
                    btn.style.background = '#ffffff';
                    btn.style.color = '#64748b';
                    btn.style.borderColor = '#cbd5e1';
                }
                if (star) star.innerText = '☆';
                if (label) label.innerText = 'Lưu câu hỏi này';
            }
        } else {
            alert(data.message || 'Lỗi khi lưu câu hỏi.');
        }
    } catch (e) {
        console.error('Bookmark error:', e);
    }
}
</script>
@endpush
@endsection
