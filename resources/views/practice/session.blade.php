@extends('layouts.app')

@section('title', $sessionTitle . ' - DET Practice')
@section('body_class', 'app-body mock-test-body')
@section('main_class', 'app-main--mock-exam')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
    <link rel="stylesheet" href="{{ asset('css/practice.css') }}">
    <style>
        .practice-session-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 0 40px;
        }
        .session-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            flex-wrap: wrap;
            gap: 12px;
        }
        .session-title-box h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 2px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
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
        .instant-option-card:hover:not(.locked) {
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .palette-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            font-weight: 700;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .palette-btn.current {
            border-color: #0284c7 !important;
            outline: 3px solid #bae6fd;
            font-weight: 800;
        }
        .palette-btn.answered-correct {
            background: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
        }
        .palette-btn.answered-wrong {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
        }
        @keyframes pulseRed {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 16px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn-voice-record {
            transition: all 0.25s ease;
        }
        .btn-voice-record.recording {
            background: #dc2626 !important;
            animation: pulseRed 1.4s infinite !important;
        }
    </style>
@endpush

@section('content')
<div class="practice-session-container">

    {{-- THANH ĐIỀU HƯỚNG TRÊN CÙNG (KHÔNG CÓ BỘ ĐẾM NGƯỢC THỜI GIAN) --}}
    <div class="session-topbar">
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('practice.select', ['loai_id' => $type->id]) }}" 
               onclick="return confirmExit()" 
               style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                ← Thoát luyện tập
            </a>
            <div style="height: 20px; width: 1px; background: #e2e8f0;"></div>
            <div class="session-title-box">
                <h1>
                    <span>{{ $skillMeta[$type->kynang]['icon'] ?? '🎯' }}</span>
                    <span>{{ $type->ten }}</span>
                    <span class="badge {{ $skillMeta[$type->kynang]['badge'] ?? 'badge-user' }}" style="font-size: 11px; margin-left: 4px;">
                        {{ $dokhoLabel }}
                    </span>
                    <span class="badge badge-success" style="font-size: 11px; margin-left: 4px;">
                        Chế độ luyện tập tự do
                    </span>
                </h1>
            </div>
        </div>

    </div>

    {{-- BỐ CỤC 2 CỘT CHÍNH --}}
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">

        {{-- CỘT TRÁI: KHU VỰC CÂU HỎI & GIẢI THÍCH TỨC THÌ --}}
        <main>
            <form id="practice-session-form" method="POST" action="{{ route('practice.session.submit') }}">
                @csrf
                <input type="hidden" name="loai_id" value="{{ $type->id }}">
                @if($dokho)
                    <input type="hidden" name="dokho" value="{{ $dokho }}">
                @endif
                <input type="hidden" name="time_spent" id="session_time_spent" value="0">

                @foreach($questions as $qIndex => $q)
                    <div class="question-container" id="q-container-{{ $qIndex }}" style="{{ $qIndex === 0 ? '' : 'display: none;' }}">
                        <div class="card" style="padding: 28px; border-radius: 16px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                            
                            {{-- Header câu hỏi: Số thứ tự, độ khó, điểm --}}
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-weight: 800; color: #00b86b; background: #e6f9f0; padding: 4px 12px; border-radius: 8px; font-size: 14px;">
                                        CÂU {{ $qIndex + 1 }} / {{ count($questions) }}
                                    </span>
                                    <span style="font-size: 13px; font-weight: 700; color: {{ ($q->dokho ?? 5) <= 3 ? '#166534' : (($q->dokho ?? 5) <= 7 ? '#854d0e' : '#991b1b') }};">
                                        Độ khó: {{ $q->dokho ?? 5 }}/10
                                    </span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                    <div style="font-size: 13px; color: #64748b; font-weight: 600;">
                                        Điểm câu: <strong style="color: #1e293b;">{{ $q->diem ?? 1 }}</strong> điểm
                                    </div>

                                    @php $isBm = in_array((int)$q->id, $bookmarkedIds ?? []); @endphp
                                    <button type="button" 
                                            class="btn-bookmark-question btn btn-sm" 
                                            id="btn-bookmark-{{ $q->id }}" 
                                            onclick="toggleSessionBookmark({{ $q->id }}, {{ $qIndex }})"
                                            title="Lưu câu hỏi này để xem lại sau"
                                            style="padding: 5px 12px; font-size: 12px; font-weight: 700; border-radius: 8px; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; {{ $isBm ? 'background: #fef3c7; color: #b45309; border: 1.5px solid #fcd34d;' : 'background: #ffffff; color: #64748b; border: 1.5px solid #cbd5e1;' }}">
                                        <span id="bm-icon-{{ $q->id }}">{{ $isBm ? '⭐' : '☆' }}</span>
                                        <span id="bm-text-{{ $q->id }}">{{ $isBm ? 'Đã lưu câu này' : 'Lưu câu hỏi' }}</span>
                                    </button>
                                </div>
                            </div>

                            {{-- ĐOẠN VĂN ĐỌC HIỂU (Nếu là dạng Interactive Reading) --}}
                            @if($type->kynang === 'reading' && (isset($q->cauhoi_doc) && $q->cauhoi_doc->isNotEmpty() || strlen($q->noidung) > 150))
                                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px 22px; margin-bottom: 24px;">
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px;">
                                        📖 ĐOẠN VĂN ĐỌC HIỂU TƯƠNG TÁC:
                                    </div>
                                    <div style="font-size: 16px; color: #1e293b; line-height: 1.7; font-weight: 500;">
                                        {!! nl2br(e($q->noidung)) !!}
                                    </div>
                                </div>
                            @else
                                {{-- NỘI DUNG CÂU HỎI THÔNG THƯỜNG --}}
                                <div style="margin-bottom: 22px;">
                                    <div style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">
                                        NỘI DUNG CÂU HỎI:
                                    </div>
                                    <div style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.6; background: #f8fafc; padding: 18px 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                        {!! nl2br(e($q->noidung)) !!}
                                    </div>
                                </div>
                            @endif

                            {{-- HÌNH ẢNH ĐÍNH KÈM NẾU CÓ --}}
                            @if(!empty($q->anh))
                                @php
                                    $cleanImg = ltrim(str_replace('\\', '/', $q->anh), '/');
                                    $imgUrl = file_exists(public_path($cleanImg)) ? asset($cleanImg) : (file_exists(public_path('images/questions/' . basename($cleanImg))) ? asset('images/questions/' . basename($cleanImg)) : asset($cleanImg));
                                @endphp
                                <div style="text-align: center; margin: 20px 0;">
                                    <img src="{{ $imgUrl }}" alt="Question" style="max-width: 100%; max-height: 340px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                </div>
                            @endif

                            {{-- ÂM THANH AUDIO ĐÍNH KÈM NẾU CÓ --}}
                            @if(!empty($q->audio))
                                @php
                                    $cleanAud = ltrim(str_replace('\\', '/', $q->audio), '/');
                                    $audUrl = file_exists(public_path($cleanAud)) ? asset($cleanAud) : (file_exists(public_path('audio/questions/' . basename($cleanAud))) ? asset('audio/questions/' . basename($cleanAud)) : asset($cleanAud));
                                    $ext = strtolower(pathinfo(parse_url($audUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                                    $mime = $ext === 'wav' ? 'audio/wav' : ($ext === 'ogg' ? 'audio/ogg' : 'audio/mpeg');
                                @endphp
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 12px; margin: 18px 0; text-align: center;">
                                    <div style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                        🎧 Nghe đoạn âm thanh câu hỏi:
                                    </div>
                                    <audio controls preload="auto" style="width: 100%; max-width: 480px;">
                                        <source src="{{ $audUrl }}" type="{{ $mime }}">
                                        <source src="{{ $audUrl }}">
                                        Trình duyệt của bạn không hỗ trợ phát âm thanh trực tiếp.
                                    </audio>
                                </div>
                            @endif

                            {{-- TRƯỜNG HỢP 1: CÂU HỎI TRẮC NGHIỆM TRỰC TIẾP (luachon) --}}
                            @if(isset($q->luachon) && $q->luachon->count() > 0)
                                <div style="margin-top: 20px;">

                                    <div class="options-container" id="opts-container-{{ $q->id }}">
                                        @foreach($q->luachon as $optIdx => $opt)
                                            <div class="instant-option-card" 
                                                 id="opt-card-{{ $q->id }}-{{ $opt->id }}"
                                                 data-is-correct="{{ !empty($opt->ladung) ? 1 : 0 }}"
                                                 onclick="handleSelectAnswer({{ $qIndex }}, {{ $q->id }}, {{ $opt->id }}, {{ !empty($opt->ladung) ? 1 : 0 }}, this)">
                                                <input type="radio" 
                                                       name="answers[{{ $q->id }}]" 
                                                       id="radio-{{ $q->id }}-{{ $opt->id }}"
                                                       value="{{ $opt->id }}" 
                                                       style="display: none;">
                                                
                                                <div class="opt-badge">
                                                    {{ chr(65 + $optIdx) }}
                                                </div>

                                                <div style="flex: 1; font-size: 15px; color: #1e293b; font-weight: 600; line-height: 1.4;">
                                                    {{ $opt->noidung }}
                                                </div>

                                                <div class="opt-status-tag" style="font-size: 13px; font-weight: 800; display: none;"></div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- HỘP HIỆN GIẢI THÍCH & KẾT QUẢ TỨC THÌ (INITIALLY HIDDEN) --}}
                                    <div class="feedback-box" id="feedback-box-{{ $q->id }}" style="display: none;">
                                        <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                            <span class="fb-icon"></span>
                                            <span class="fb-text"></span>
                                        </div>
                                        <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                            <strong> Giải thích chi tiết:</strong>
                                            <div style="margin-top: 4px;">
                                                {{ $q->giaithich ?: 'Đáp án đã được hệ thống xác nhận chính xác theo quy chuẩn đề thi Duolingo English Test.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- TRƯỜNG HỢP 2: CÁC CÂU HỎI CON TƯƠNG TÁC TRONG CAUHOI_DOC (Interactive Reading) --}}
                            @elseif(isset($q->cauhoi_doc) && $q->cauhoi_doc->count() > 0)
                                <div style="margin-top: 20px;">
                                    <div style="font-size: 14px; font-weight: 800; color: #0284c7; margin-bottom: 14px; text-transform: uppercase;">
                                        DANH SÁCH CÂU HỎI ĐỌC HIỂU TƯƠNG TÁC ({{ $q->cauhoi_doc->count() }} câu con):
                                    </div>

                                    @foreach($q->cauhoi_doc as $subIdx => $subQ)
                                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 18px;">
                                            <div style="font-weight: 700; color: #1e293b; font-size: 15px; margin-bottom: 12px;">
                                                Câu hỏi {{ $subIdx + 1 }}: {{ $subQ->noidung }}
                                            </div>

                                            @if(isset($subQ->luachon) && $subQ->luachon->count() > 0)
                                                <div class="options-container" id="opts-container-sub-{{ $subQ->id }}">
                                                    @foreach($subQ->luachon as $optIdx => $opt)
                                                        <div class="instant-option-card" 
                                                             id="opt-card-sub-{{ $subQ->id }}-{{ $opt->id }}"
                                                             data-is-correct="{{ !empty($opt->ladung) ? 1 : 0 }}"
                                                             onclick="handleSelectSubAnswer({{ $qIndex }}, {{ $q->id }}, {{ $subQ->id }}, {{ $opt->id }}, {{ !empty($opt->ladung) ? 1 : 0 }}, this)">
                                                            <input type="radio" 
                                                                   name="sub_answers[{{ $q->id }}][{{ $subQ->id }}]" 
                                                                   value="{{ $opt->id }}" 
                                                                   style="display: none;">
                                                            
                                                            <div class="opt-badge">
                                                                {{ chr(65 + $optIdx) }}
                                                            </div>

                                                            <div style="flex: 1; font-size: 15px; color: #1e293b; font-weight: 600;">
                                                                {{ $opt->noidung }}
                                                            </div>

                                                            <div class="opt-status-tag" style="font-size: 13px; font-weight: 800; display: none;"></div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="feedback-box" id="feedback-box-sub-{{ $subQ->id }}" style="display: none;">
                                                    <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                                        <span class="fb-icon"></span>
                                                        <span class="fb-text"></span>
                                                    </div>
                                                    <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                                        <strong>Giải thích chi tiết:</strong>
                                                        <div style="margin-top: 4px;">
                                                            {{ $subQ->giaithich ?: ($q->giaithich ?: 'Hãy đối chiếu câu trong đoạn văn để xác nhận thông tin chính xác.') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Trường hợp câu hỏi con dạng nhập câu trả lời / điền từ / text input (Interactive Reading) --}}
                                                <div style="margin-top: 10px;">
                                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                                        <input type="text" 
                                                               id="sub-input-{{ $subQ->id }}" 
                                                               name="sub_text_answers[{{ $q->id }}][{{ $subQ->id }}]" 
                                                               class="form-control" 
                                                               placeholder="Nhập câu trả lời của bạn..." 
                                                               data-correct-answer="{{ $subQ->dapandung ?? '' }}"
                                                               style="flex: 1; min-width: 220px; font-size: 15px; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                                                        <button type="button" 
                                                                class="btn btn-primary" 
                                                                onclick="checkSubTextInput({{ $qIndex }}, {{ $q->id }}, {{ $subQ->id }}, '{{ addslashes($subQ->dapandung ?? '') }}')" 
                                                                style="padding: 10px 18px; font-weight: 700; border-radius: 8px; white-space: nowrap;">
                                                            Kiểm tra câu này
                                                        </button>
                                                    </div>

                                                    {{-- Hộp giải thích tức thì cho câu nhập --}}
                                                    <div class="feedback-box" id="feedback-box-sub-{{ $subQ->id }}" style="display: none; margin-top: 12px;">
                                                        <div class="feedback-header" style="font-size: 15px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                                            <span class="fb-icon"></span>
                                                            <span class="fb-text"></span>
                                                        </div>
                                                        <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                                            @if(!empty($subQ->dapandung))
                                                                <div style="margin-bottom: 6px; color: #166534; font-weight: 700;">
                                                                    Đáp án đúng: <span class="correct-val-display" style="background: #eefcf5; padding: 2px 8px; border-radius: 4px; border: 1px solid #86efac;">{{ $subQ->dapandung }}</span>
                                                                </div>
                                                            @endif
                                                            <strong>Giải thích chi tiết:</strong>
                                                            <div style="margin-top: 4px;">
                                                                {{ $subQ->giaithich ?: ($q->giaithich ?: 'Hãy đối chiếu câu trong đoạn văn để xác nhận thông tin chính xác.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            {{-- TRƯỜNG HỢP 3: DẠNG BÀI NÓI SPEAKING (NÚT VOICE GHI ÂM TRỰC TIẾP ĐỂ AI KIỂM TRA) --}}
                            @elseif((!empty($type->kynang) && $type->kynang === 'speaking') || (!empty($q->loai_kynang) && $q->loai_kynang === 'speaking'))
                                <div style="margin-top: 24px;">


                                    <div class="voice-recording-studio" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 26px 20px; text-align: center; margin-bottom: 18px;">
                                        
                                        <div id="voice-controls-{{ $q->id }}">
              

                                            <!-- Nút Voice Ghi Âm Lớn -->
                                            <button type="button" 
                                                    class="btn-voice-record" 
                                                    id="btn-voice-{{ $q->id }}" 
                                                    onclick="toggleVoiceRecording({{ $qIndex }}, {{ $q->id }})"
                                                    style="width: 82px; height: 82px; border-radius: 50%; background: #009e5b; color: #ffffff; border: none; font-size: 34px; cursor: pointer; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4); transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;">
                                                🎙️
                                            </button>

                                            <div id="voice-status-text-{{ $q->id }}" style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 14px;">
                                                Nhấn vào nút Micro để bắt đầu ghi âm bài nói
                                            </div>

                                            <div id="voice-timer-box-{{ $q->id }}" style="display: none; margin-top: 12px;">
                                                <div style="display: inline-flex; align-items: center; gap: 8px; background: #fee2e2; color: #991b1b; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-family: monospace; font-size: 16px;">
                                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; animation: pulseRed 1s infinite;"></span>
                                                    <span id="voice-time-{{ $q->id }}">00:00</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trình phát nghe lại và nút gửi AI -->
                                        <div id="voice-playback-box-{{ $q->id }}" style="display: none; margin-top: 18px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                                            <p style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                                🎧 Nghe lại bài nói vừa ghi âm:
                                            </p>
                                            <audio id="voice-audio-{{ $q->id }}" controls style="width: 100%; max-width: 460px; margin-bottom: 16px;"></audio>

                                            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                                                <button type="button" class="btn btn-outline btn-sm" onclick="resetVoiceRecording({{ $qIndex }}, {{ $q->id }})" style="font-weight: 700;">
                                                    Ghi âm lại
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-ai-check-{{ $q->id }}" onclick="sendVoiceToAI({{ $qIndex }}, {{ $q->id }})" style="font-weight: 800; background: #00b86b; border-color: #00b86b; padding: 8px 20px;">
                                                    Nhấn để AI kiểm tra bài nói ngay
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Loading AI -->
                                        <div id="voice-ai-loading-{{ $q->id }}" style="display: none; margin-top: 18px;">
                                            <div style="display: inline-flex; align-items: center; gap: 10px; color: #0284c7; font-weight: 700; font-size: 14px;">
                                                <span class="spinner-border" style="width: 22px; height: 22px; border: 3px solid #0284c7; border-top-color: transparent; border-radius: 50%; display: inline-block; animation: spin 0.8s linear infinite;"></span>
                                                <span>AI đang lắng nghe và chấm điểm phát âm & độ lưu loát...</span>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Thẻ hiển thị kết quả AI đánh giá -->
                                    <div class="card" id="ai-result-card-{{ $q->id }}" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 14px; padding: 20px 22px; margin-top: 16px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                           
                                                <strong style="font-size: 16px; color: #166534;">Kết quả AI đánh giá bài nói (Duolingo AI Assessor)</strong>
                                            </div>
                                            <div style="background: #16a34a; color: #ffffff; font-weight: 800; padding: 5px 16px; border-radius: 20px; font-size: 14px;">
                                                Điểm bài nói: <span id="ai-score-{{ $q->id }}">--</span> / {{ $q->diem ?? 10 }}
                                            </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 14px;">
                                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">🗣️ Phát âm (Pronunciation):</span>
                                                <span id="ai-pronun-{{ $q->id }}" style="font-size: 13px; color: #334155;">--</span>
                                            </div>
                                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">⚡ Độ lưu loát (Fluency):</span>
                                                <span id="ai-fluency-{{ $q->id }}" style="font-size: 13px; color: #334155;">--</span>
                                            </div>
                                            <div style="background: #ffffff; padding: 12px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                                <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 2px;">🎯 Ước lượng DET:</span>
                                                <span id="ai-det-{{ $q->id }}" style="font-size: 14px; font-weight: 800; color: #00b86b;">--</span>
                                            </div>
                                        </div>

                                        <div style="background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #bbf7d0; font-size: 13px; line-height: 1.6; color: #1e293b;">
                                            <strong>💡 Nhận xét chi tiết & Lời khuyên:</strong>
                                            <p id="ai-tips-{{ $q->id }}" style="margin: 4px 0 0 0; color: #475569;">--</p>
                                        </div>
                                    </div>
                                </div>

                            {{-- TRƯỜNG HỢP 4: DẠNG ĐIỀN TỪ / GÕ VĂN BẢN / LUẬN --}}
                            @else
                                <div style="margin-top: 20px;">
                                    <label for="text-ans-{{ $q->id }}" style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block;">
                                        NHẬP CÂU TRẢ LỜI CỦA BẠN:
                                    </label>
                                    <textarea name="answers[{{ $q->id }}]" 
                                              id="text-ans-{{ $q->id }}" 
                                              rows="4" 
                                              class="form-control" 
                                              placeholder="Nhập câu trả lời bằng tiếng Anh tại đây..."
                                              style="font-size: 15px; line-height: 1.6; margin-bottom: 12px;"></textarea>

                                    <div style="display: flex; gap: 10px;">
                                        <button type="button" class="btn btn-outline btn-sm" onclick="checkTextAnswer({{ $qIndex }}, {{ $q->id }})" style="font-weight: 700;">
                                            Kiểm tra đáp án ngay
                                        </button>
                                    </div>

                                    <div class="feedback-box" id="feedback-box-{{ $q->id }}" style="display: none; margin-top: 14px;">
                                        <div class="feedback-desc" style="font-size: 14px; line-height: 1.6; background: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155;">
                                            <strong>💡 Hướng dẫn & Giải thích:</strong>
                                            <div style="margin-top: 4px;">
                                                {{ $q->giaithich ?: 'Hãy kiểm tra lại ngữ pháp, từ vựng và cấu trúc câu cho chuẩn format bài thi DET.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- NÚT ĐIỀU HƯỚNG CÂU HỎI (CÂU TRƯỚC / CÂU SAU / NỘP BÀI) --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
                            @if($qIndex > 0)
                                <button type="button" class="btn btn-outline" onclick="goToQuestion({{ $qIndex - 1 }})" style="padding: 10px 22px; font-weight: 600;">
                                    ← Câu trước
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div style="display: flex; gap: 12px; align-items: center;">
                                @if($qIndex < count($questions) - 1)
                                    <button type="button" class="btn btn-primary" id="btn-next-{{ $qIndex }}" onclick="goToQuestion({{ $qIndex + 1 }})" style="padding: 11px 32px; font-weight: 800; font-size: 15px;">
                                        Câu sau →
                                    </button>
                                @else
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </form>
        </main>

        {{-- CỘT PHẢI: BẢNG TIẾN ĐỘ & PHIẾU CÂU HỎI  --}}
        <aside>
            <div class="card" style="padding: 22px; border-radius: 16px; border: 1px solid #e2e8f0; position: sticky; top: 20px;">
                
                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 14px 0; display: flex; justify-content: space-between; align-items: center;">
                    <span>PHIẾU CÂU HỎI</span>
                    <span id="counter-answered" style="font-size: 13px; font-weight: 700; color: #00b86b; background: #e6f9f0; padding: 2px 8px; border-radius: 12px;">
                        0 / {{ count($questions) }}
                    </span>
                </h3>

                <!-- THỐNG KÊ KẾT QUẢ TỨC THÌ -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 18px; background: #f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 13px; color: #166534; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #16a34a;"></span>
                        Đúng: <span id="stat-correct">0</span>
                    </div>
                    <div style="font-size: 13px; color: #991b1b; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                        Chưa đúng: <span id="stat-wrong">0</span>
                    </div>
                </div>

                <!-- LƯỚI NÚT CHUYỂN CÂU HỎI -->
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 20px;">
                    @foreach($questions as $i => $q)
                        @php $qIsBm = in_array((int)$q->id, $bookmarkedIds ?? []); @endphp
                        <button type="button" 
                                class="palette-btn {{ $i === 0 ? 'current' : '' }}" 
                                id="palette-btn-{{ $i }}" 
                                onclick="goToQuestion({{ $i }})"
                                style="position: relative;">
                            <span>{{ $i + 1 }}</span>
                            <span id="palette-star-{{ $i }}" style="position: absolute; top: 1px; right: 2px; font-size: 9px; line-height: 1; {{ $qIsBm ? '' : 'display: none;' }}">⭐</span>
                        </button>
                    @endforeach
                </div>

                <!-- CHÚ THÍCH MÀU SẮC PHIẾU CÂU HỎI -->
                <div style="font-size: 12px; color: #64748b; line-height: 1.8; border-top: 1px solid #f1f5f9; padding-top: 14px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #16a34a; display: inline-block;"></span>
                        <span>Đã làm đúng (+ điểm)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #ef4444; display: inline-block;"></span>
                        <span>Đã làm sai (có giải thích)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #ffffff; border: 1px solid #cbd5e1; display: inline-block;"></span>
                        <span>Chưa trả lời</span>
                    </div>
                </div>

                <!-- NÚT NỘP BÀI LÀM LUÔN KHẢ DỤNG -->
                <button type="button" 
                        onclick="submitPracticeNow()" 
                        class="btn btn-primary" 
                        style="width: 100%; justify-content: center; font-weight: 800; padding: 12px; background: #00b86b; border-color: #00b86b; box-shadow: 0 4px 12px rgba(0, 184, 107, 0.2);">
                    Nộp bài làm & Xem tổng kết 
                </button>

                <p style="text-align: center; font-size: 11px; color: #94a3b8; margin: 10px 0 0 0;">
                    Bạn có thể nộp bài bất cứ lúc nào để hệ thống tổng hợp điểm số và lưu vào lịch sử.
                </p>

            </div>
        </aside>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const totalQuestions = {{ count($questions) }};
    let currentQuestionIndex = 0;
    let correctCount = 0;
    let wrongCount = 0;
    let answeredQuestions = new Set();
    let secondsElapsed = 0;

    // Bộ đếm thời gian làm bài tăng dần (để lưu vào lịch sử, không tạo áp lực)
    setInterval(function() {
        secondsElapsed++;
        const input = document.getElementById('session_time_spent');
        if (input) input.value = secondsElapsed;
    }, 1000);

    // Chuyển câu hỏi
    function goToQuestion(index) {
        if (index < 0 || index >= totalQuestions) return;

        // Ẩn câu hiện tại
        const currentContainer = document.getElementById('q-container-' + currentQuestionIndex);
        if (currentContainer) currentContainer.style.display = 'none';

        // Bỏ active nút palette
        const prevBtn = document.getElementById('palette-btn-' + currentQuestionIndex);
        if (prevBtn) prevBtn.classList.remove('current');

        // Hiện câu mới
        currentQuestionIndex = index;
        const newContainer = document.getElementById('q-container-' + currentQuestionIndex);
        if (newContainer) newContainer.style.display = 'block';

        // Active nút palette mới
        const nextBtn = document.getElementById('palette-btn-' + currentQuestionIndex);
        if (nextBtn) nextBtn.classList.add('current');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Xử lý chọn đáp án trắc nghiệm -> HIỆN KẾT QUẢ & GIẢI THÍCH NGAY LẬP TỨC
    function handleSelectAnswer(qIndex, qId, optId, isCorrect, clickedCard) {
        // Đánh dấu radio
        const radio = document.getElementById('radio-' + qId + '-' + optId);
        if (radio) radio.checked = true;

        const container = document.getElementById('opts-container-' + qId);
        if (!container) return;

        // Reset trạng thái các lựa chọn khác trong cùng câu
        const allCards = container.querySelectorAll('.instant-option-card');
        allCards.forEach(card => {
            card.classList.remove('is-correct', 'is-wrong', 'revealed-correct');
            const tag = card.querySelector('.opt-status-tag');
            if (tag) tag.style.display = 'none';
        });

        const feedbackBox = document.getElementById('feedback-box-' + qId);
        const paletteBtn = document.getElementById('palette-btn-' + qIndex);

        if (isCorrect === 1) {
            // ĐÚNG
            clickedCard.classList.add('is-correct');
            const tag = clickedCard.querySelector('.opt-status-tag');
            if (tag) {
                tag.style.display = 'inline-block';
                tag.style.color = '#16a34a';
                tag.textContent = '✓ Chính xác (+1)';
            }

            if (feedbackBox) {
                feedbackBox.className = 'feedback-box correct';
                feedbackBox.querySelector('.fb-icon').textContent = '🎉';
                feedbackBox.querySelector('.fb-text').textContent = 'Chính xác! Bạn đã chọn đúng đáp án.';
                feedbackBox.style.display = 'block';
            }

            if (paletteBtn) {
                paletteBtn.classList.remove('answered-wrong');
                paletteBtn.classList.add('answered-correct');
            }

            if (!answeredQuestions.has(qId)) {
                correctCount++;
            }
        } else {
            // SAI -> Hiện màu đỏ trên lựa chọn người dùng và tìm đáp án đúng để tô sáng
            clickedCard.classList.add('is-wrong');
            const tag = clickedCard.querySelector('.opt-status-tag');
            if (tag) {
                tag.style.display = 'inline-block';
                tag.style.color = '#ef4444';
                tag.textContent = '✗ Chưa đúng';
            }

            // Tìm và hiển thị đáp án đúng
            allCards.forEach(card => {
                if (card.getAttribute('data-is-correct') === '1') {
                    card.classList.add('revealed-correct');
                    const correctTag = card.querySelector('.opt-status-tag');
                    if (correctTag) {
                        correctTag.style.display = 'inline-block';
                        correctTag.style.color = '#16a34a';
                        correctTag.textContent = '✓ Đáp án đúng';
                    }
                }
            });

            if (feedbackBox) {
                feedbackBox.className = 'feedback-box wrong';
                feedbackBox.querySelector('.fb-icon').textContent = '💡';
                feedbackBox.querySelector('.fb-text').textContent = 'Chưa chính xác! Xem đáp án chuẩn và giải thích bên dưới:';
                feedbackBox.style.display = 'block';
            }

            if (paletteBtn) {
                paletteBtn.classList.remove('answered-correct');
                paletteBtn.classList.add('answered-wrong');
            }

            if (!answeredQuestions.has(qId)) {
                wrongCount++;
            }
        }

        answeredQuestions.add(qId);
        updateCounters();
    }

    // Xử lý câu hỏi đọc hiểu con (cauhoi_doc)
    function handleSelectSubAnswer(qIndex, qId, subId, optId, isCorrect, clickedCard) {
        const container = document.getElementById('opts-container-sub-' + subId);
        if (!container) return;

        const allCards = container.querySelectorAll('.instant-option-card');
        allCards.forEach(card => {
            card.classList.remove('is-correct', 'is-wrong', 'revealed-correct');
            const tag = card.querySelector('.opt-status-tag');
            if (tag) tag.style.display = 'none';
        });

        const feedbackBox = document.getElementById('feedback-box-sub-' + subId);
        const paletteBtn = document.getElementById('palette-btn-' + qIndex);

        if (isCorrect === 1) {
            clickedCard.classList.add('is-correct');
            if (feedbackBox) {
                feedbackBox.className = 'feedback-box correct';
                feedbackBox.querySelector('.fb-icon').textContent = '✓';
                feedbackBox.querySelector('.fb-text').textContent = 'Chính xác!';
                feedbackBox.style.display = 'block';
            }
            if (paletteBtn) {
                paletteBtn.classList.add('answered-correct');
            }
            if (!answeredQuestions.has('sub_' + subId)) correctCount++;
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
            if (paletteBtn && !paletteBtn.classList.contains('answered-correct')) {
                paletteBtn.classList.add('answered-wrong');
            }
            if (!answeredQuestions.has('sub_' + subId)) wrongCount++;
        }

        answeredQuestions.add('sub_' + subId);
        answeredQuestions.add(qId);
        updateCounters();
    }

    // Kiểm tra câu hỏi đọc hiểu điền từ dạng text
    function checkSubTextInput(qIndex, qId, subId, correctAnswer) {
        const input = document.getElementById('sub-text-input-' + subId);
        const feedbackBox = document.getElementById('sub-feedback-box-' + subId);
        const paletteBtn = document.getElementById('palette-btn-' + qIndex);
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
            if (paletteBtn) paletteBtn.classList.add('answered-correct');
            if (!answeredQuestions.has('sub_' + subId)) correctCount++;
        } else {
            input.style.borderColor = '#dc2626';
            input.style.backgroundColor = '#fef2f2';
            feedbackBox.className = 'feedback-box wrong';
            feedbackBox.querySelector('.fb-icon').textContent = '💡';
            feedbackBox.querySelector('.fb-text').textContent = 'Chưa chính xác! Hãy đối chiếu đáp án chuẩn bên dưới:';
            if (paletteBtn && !paletteBtn.classList.contains('answered-correct')) {
                paletteBtn.classList.add('answered-wrong');
            }
            if (!answeredQuestions.has('sub_' + subId)) wrongCount++;
        }
        feedbackBox.style.display = 'block';

        answeredQuestions.add('sub_' + subId);
        answeredQuestions.add(qId);
        updateCounters();
    }

    // Kiểm tra câu tự luận / điền từ
    function checkTextAnswer(qIndex, qId) {
        const feedbackBox = document.getElementById('feedback-box-' + qId);
        if (feedbackBox) feedbackBox.style.display = 'block';

        const paletteBtn = document.getElementById('palette-btn-' + qIndex);
        if (paletteBtn) paletteBtn.classList.add('answered-correct');

        answeredQuestions.add(qId);
        updateCounters();
    }

    // Cập nhật các chỉ số thống kê
    function updateCounters() {
        const counterEl = document.getElementById('counter-answered');
        if (counterEl) {
            const count = Array.from(answeredQuestions).filter(k => typeof k === 'number').length;
            counterEl.textContent = count + ' / ' + totalQuestions;
        }

        const statCorrect = document.getElementById('stat-correct');
        if (statCorrect) statCorrect.textContent = correctCount;

        const statWrong = document.getElementById('stat-wrong');
        if (statWrong) statWrong.textContent = wrongCount;
    }

    // Nộp bài
    function submitPracticeNow() {
        const answeredCount = Array.from(answeredQuestions).filter(k => typeof k === 'number').length;
        let confirmMsg = "Bạn đã hoàn thành " + answeredCount + "/" + totalQuestions + " câu hỏi. Bạn có chắc chắn muốn nộp bài để xem tổng kết điểm không?";
        if (answeredCount === 0) {
            confirmMsg = "Bạn chưa trả lời câu hỏi nào. Bạn có chắc chắn muốn nộp bài để kết thúc không?";
        }

        if (confirm(confirmMsg)) {
            document.getElementById('practice-session-form').submit();
        }
    }

    function confirmExit() {
        return confirm("Bạn có chắc muốn rời khỏi bài luyện tập này? Tiến độ làm bài hiện tại sẽ không được lưu vào lịch sử nếu chưa nộp bài.");
    }

    // ==========================================
    // PHẦN THU ÂM BÀI NÓI (VOICE RECORDING) & AI KIỂM TRA
    // ==========================================
    let mediaRecorders = {};
    let audioChunks = {};
    let recordingIntervals = {};
    let recordingSeconds = {};
    let recordedBlobs = {};

    async function toggleVoiceRecording(qIndex, qId) {
        if (mediaRecorders[qId] && mediaRecorders[qId].state === 'recording') {
            stopVoiceRecording(qIndex, qId);
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            startVoiceRecording(qIndex, qId, stream);
        } catch (err) {
            alert("Không thể truy cập Microphone: " + (err.message || 'Quyền bị từ chối') + "\nVui lòng cho phép trình duyệt truy cập Microphone để ghi âm bài nói.");
        }
    }

    function startVoiceRecording(qIndex, qId, stream) {
        audioChunks[qId] = [];
        recordingSeconds[qId] = 0;

        let options = { mimeType: 'audio/webm' };
        if (!MediaRecorder.isTypeSupported('audio/webm')) {
            options = { mimeType: 'audio/ogg' };
            if (!MediaRecorder.isTypeSupported('audio/ogg')) {
                options = {};
            }
        }

        const recorder = new MediaRecorder(stream, options);
        mediaRecorders[qId] = recorder;

        recorder.ondataavailable = function(e) {
            if (e.data && e.data.size > 0) {
                audioChunks[qId].push(e.data);
            }
        };

        recorder.onstop = function() {
            clearInterval(recordingIntervals[qId]);
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop());

            const blob = new Blob(audioChunks[qId], { type: recorder.mimeType || 'audio/webm' });
            recordedBlobs[qId] = blob;

            const audioUrl = URL.createObjectURL(blob);
            const audioPlayer = document.getElementById('voice-audio-' + qId);
            if (audioPlayer) {
                audioPlayer.src = audioUrl;
            }

            const playbackBox = document.getElementById('voice-playback-box-' + qId);
            if (playbackBox) playbackBox.style.display = 'block';

            const btnVoice = document.getElementById('btn-voice-' + qId);
            if (btnVoice) {
                btnVoice.classList.remove('recording');
                btnVoice.textContent = '🎙️';
                btnVoice.style.background = '#64748b';
            }

            const statusText = document.getElementById('voice-status-text-' + qId);
            if (statusText) statusText.textContent = 'Đã hoàn thành ghi âm (' + formatTime(recordingSeconds[qId]) + '). Nhấn nút AI bên dưới để kiểm tra.';
        };

        recorder.start();

        const btnVoice = document.getElementById('btn-voice-' + qId);
        if (btnVoice) {
            btnVoice.classList.add('recording');
            btnVoice.textContent = '⏹️';
            btnVoice.style.background = '#dc2626';
        }

        const timerBox = document.getElementById('voice-timer-box-' + qId);
        if (timerBox) timerBox.style.display = 'block';

        const statusText = document.getElementById('voice-status-text-' + qId);
        if (statusText) statusText.textContent = 'Đang ghi âm bài nói... Nhấn lại vào nút để DỪNG';

        recordingIntervals[qId] = setInterval(function() {
            recordingSeconds[qId]++;
            const timeEl = document.getElementById('voice-time-' + qId);
            if (timeEl) timeEl.textContent = formatTime(recordingSeconds[qId]);
        }, 1000);
    }

    function stopVoiceRecording(qIndex, qId) {
        if (mediaRecorders[qId] && mediaRecorders[qId].state === 'recording') {
            mediaRecorders[qId].stop();
        }
    }

    function resetVoiceRecording(qIndex, qId) {
        delete recordedBlobs[qId];
        delete audioChunks[qId];
        recordingSeconds[qId] = 0;

        const playbackBox = document.getElementById('voice-playback-box-' + qId);
        if (playbackBox) playbackBox.style.display = 'none';

        const timerBox = document.getElementById('voice-timer-box-' + qId);
        if (timerBox) timerBox.style.display = 'none';

        const aiCard = document.getElementById('ai-result-card-' + qId);
        if (aiCard) aiCard.style.display = 'none';

        const btnVoice = document.getElementById('btn-voice-' + qId);
        if (btnVoice) {
            btnVoice.classList.remove('recording');
            btnVoice.textContent = '🎙️';
            btnVoice.style.background = '#';
        }

        const statusText = document.getElementById('voice-status-text-' + qId);
        if (statusText) statusText.textContent = 'Nhấn vào nút Micro để bắt đầu ghi âm bài nói';
    }

    async function sendVoiceToAI(qIndex, qId) {
        const blob = recordedBlobs[qId];
        if (!blob) {
            alert("Chưa có đoạn ghi âm bài nói! Vui lòng nhấn nút Voice để ghi âm trước.");
            return;
        }

        const loadingEl = document.getElementById('voice-ai-loading-' + qId);
        if (loadingEl) loadingEl.style.display = 'block';

        const btnAi = document.getElementById('btn-ai-check-' + qId);
        if (btnAi) btnAi.disabled = true;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('cauhoi_id', qId);
        formData.append('duration', recordingSeconds[qId] || 15);
        formData.append('audio', blob, 'speaking_' + qId + '.webm');

        try {
            const response = await fetch('{{ route("practice.speaking.evaluate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                const resultCard = document.getElementById('ai-result-card-' + qId);
                if (resultCard) resultCard.style.display = 'block';

                const scoreEl = document.getElementById('ai-score-' + qId);
                if (scoreEl) scoreEl.textContent = data.score;

                const pronunEl = document.getElementById('ai-pronun-' + qId);
                if (pronunEl) pronunEl.textContent = data.feedback.pronunciation;

                const fluencyEl = document.getElementById('ai-fluency-' + qId);
                if (fluencyEl) fluencyEl.textContent = data.feedback.fluency;

                const detEl = document.getElementById('ai-det-' + qId);
                if (detEl) detEl.textContent = data.det_estimated;

                const tipsEl = document.getElementById('ai-tips-' + qId);
                if (tipsEl) tipsEl.textContent = data.feedback.overall + ' ' + data.feedback.tips;

                const paletteBtn = document.getElementById('palette-btn-' + qIndex);
                if (paletteBtn) {
                    paletteBtn.classList.remove('answered-wrong');
                    paletteBtn.classList.add('answered-correct');
                }

                if (!answeredQuestions.has(qId)) {
                    correctCount++;
                    answeredQuestions.add(qId);
                    updateCounters();
                }

                let hiddenAudio = document.getElementById('hidden-audio-' + qId);
                if (!hiddenAudio) {
                    hiddenAudio = document.createElement('input');
                    hiddenAudio.type = 'hidden';
                    hiddenAudio.id = 'hidden-audio-' + qId;
                    hiddenAudio.name = 'answers[' + qId + ']';
                    document.getElementById('practice-session-form').appendChild(hiddenAudio);
                }
                hiddenAudio.value = data.audio_url;

                resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                alert("Không thể đánh giá bài nói: " + (data.message || 'Lỗi không xác định'));
            }
        } catch (err) {
            alert("Lỗi khi kết nối với AI chấm điểm: " + err.message);
        } finally {
            if (loadingEl) loadingEl.style.display = 'none';
            if (btnAi) btnAi.disabled = false;
        }
    }

    async function toggleSessionBookmark(qid, qIndex) {
        const btn = document.getElementById('btn-bookmark-' + qid);
        const icon = document.getElementById('bm-icon-' + qid);
        const text = document.getElementById('bm-text-' + qid);
        const star = document.getElementById('palette-star-' + qIndex);

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
                    if (text) text.innerText = 'Đã lưu câu này';
                    if (star) star.style.display = 'inline';
                    showSessionToast('⭐ ' + data.message);
                } else {
                    if (btn) {
                        btn.style.background = '#ffffff';
                        btn.style.color = '#64748b';
                        btn.style.borderColor = '#cbd5e1';
                    }
                    if (icon) icon.innerText = '☆';
                    if (text) text.innerText = 'Lưu câu hỏi';
                    if (star) star.style.display = 'none';
                    showSessionToast('✓ ' + data.message);
                }
            } else {
                alert(data.message || 'Lỗi khi lưu câu hỏi.');
            }
        } catch (e) {
            console.error('Bookmark error:', e);
        }
    }

    function showSessionToast(msg) {
        let t = document.getElementById('session-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'session-toast';
            t.style.position = 'fixed';
            t.style.bottom = '24px';
            t.style.right = '24px';
            t.style.background = '#1e293b';
            t.style.color = '#ffffff';
            t.style.padding = '12px 20px';
            t.style.borderRadius = '10px';
            t.style.fontSize = '14px';
            t.style.fontWeight = '700';
            t.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            t.style.zIndex = '9999';
            document.body.appendChild(t);
        }
        t.innerText = msg;
        t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2600);
    }

    function formatTime(totalSec) {
        const m = Math.floor(totalSec / 60);
        const s = totalSec % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }
</script>
@endpush

