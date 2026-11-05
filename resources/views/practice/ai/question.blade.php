@extends('layouts.app')

@section('title', 'Luyện tập ' . ($isSpeaking ? 'Speaking' : 'Writing') . ' - DET Practice')

@section('content')
<div class="practice-container" style="max-width: 850px; margin: 2rem auto; padding: 0 1rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('practice.index') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 0.95rem;">&larr; Danh sách dạng bài luyện tập</a>
        <h1 style="font-size: 1.8rem; color: var(--bento-heading); margin-top: 0.5rem;">
            Luyện tập: {{ $cauhoi->loai?->ten ?? ($isSpeaking ? 'Speaking' : 'Writing') }}
        </h1>
        <p style="color: var(--bento-muted); font-size: 0.95rem;">Bài làm của bạn sẽ được hệ thống AI chuyên sâu chấm điểm và phản hồi chi tiết theo chuẩn DET.</p>
    </div>

    @if ($errors->has('ai_limit'))
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong>Hết hạn mức hôm nay:</strong> {{ $errors->first('ai_limit') }}
        </div>
    @endif

    <!-- Đề bài câu hỏi -->
    <div style="background: #ffffff; border: 1.5px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 2rem;">
        <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--bento-muted); margin-bottom: 0.5rem; letter-spacing: 0.05em;">
            Yêu cầu đề bài:
        </div>
        <div style="font-size: 1.15rem; color: var(--bento-ink); line-height: 1.6; margin-bottom: 1rem;">
            {!! nl2br(e($cauhoi->noidung)) !!}
        </div>

        @if (!empty($cauhoi->anh))
            <div style="margin: 1.5rem 0; text-align: center;">
                <img src="{{ asset(ltrim($cauhoi->anh, '/')) }}" alt="Prompt Image" style="max-width: 100%; max-height: 350px; border-radius: 8px; border: 1px solid var(--bento-line);">
            </div>
        @endif

        @if (!empty($cauhoi->audio))
            <div style="margin: 1rem 0;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem;">Nghe đoạn âm thanh đề bài:</label>
                <audio controls style="width: 100%;">
                    <source src="{{ asset(ltrim($cauhoi->audio, '/')) }}">
                    Trình duyệt của bạn không hỗ trợ phát âm thanh.
                </audio>
            </div>
        @endif
    </div>

    <!-- Form làm bài No-JS -->
    <div style="background: #ffffff; border: 1.5px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('practice.ai.submit', $cauhoi->id) }}" enctype="multipart/form-data">
            @csrf

            @if ($isSpeaking)
                <div style="margin-bottom: 1.5rem;">
                    <label for="audio" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                        Tải lên file ghi âm bài nói của bạn (audio/*):
                    </label>
                    <input
                        type="file"
                        id="audio"
                        name="audio"
                        accept="audio/*"
                        required
                        style="display: block; width: 100%; padding: 0.75rem; border: 1.5px dashed #9ca3af; border-radius: 8px; background: var(--bento-subtle); font-size: 0.95rem;"
                    >
                    <span style="display: block; font-size: 0.85rem; color: var(--bento-muted); margin-top: 0.5rem;">
                        Định dạng hỗ trợ: MP3, WAV, M4A, OGG, WEBM. Dung lượng tối đa: 10 MB.
                    </span>
                    @error('audio')
                        <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <div style="margin-bottom: 1.5rem;">
                    <label for="noidung" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                        Nhập bài viết của bạn (tối đa 3000 ký tự):
                    </label>
                    <textarea
                        id="noidung"
                        name="noidung"
                        rows="8"
                        maxlength="3000"
                        placeholder="Bắt đầu viết bài luận hoặc câu trả lời của bạn tại đây..."
                        required
                        style="width: 100%; box-sizing: border-box; padding: 0.85rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 1rem; line-height: 1.6; resize: vertical;"
                    >{{ old('noidung') }}</textarea>
                    @error('noidung')
                        <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-size: 0.85rem; color: var(--bento-muted);">Hệ thống sẽ trừ 1 lượt chấm AI khi bạn nộp bài.</span>
                <button type="submit" style="background: #2563eb; color: #ffffff; font-weight: 700; padding: 0.85rem 2rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                    Nộp bài & Chấm điểm AI
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

