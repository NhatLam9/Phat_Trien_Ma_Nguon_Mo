@extends('layouts.app')

@section('title', 'Dịch thuật AI 2 chiều - DET Practice')

@section('content')
<div class="tutor-tool-container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <a href="{{ route('tutor.index') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 0.95rem;">&larr; Quay lại AI Tutor</a>
            <h1 style="font-size: 1.8rem; color: var(--bento-heading); margin-top: 0.25rem;">Dịch thuật AI 2 chiều chuẩn DET</h1>
        </div>
        <a href="{{ route('tutor.history', ['loai' => 'translate']) }}" style="font-size: 0.9rem; color: var(--bento-orange-dark); text-decoration: none;">Xem lịch sử dịch &rarr;</a>
    </div>

    @if ($errors->has('ai_limit'))
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong>Hết hạn mức hôm nay:</strong> {{ $errors->first('ai_limit') }}
        </div>
    @elseif ($errors->has('api_error'))
        <div style="background: #fffbeb; border-left: 4px solid #f59e0b; color: #92400e; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong>Thông báo:</strong> {{ $errors->first('api_error') }}
        </div>
    @endif

    @if (session('success'))
        <div style="background: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #ffffff; border: 1px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 2rem;">
        <form method="POST" action="{{ route('tutor.translate.process') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label for="direction" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                    Chọn chiều dịch thuật:
                </label>
                <select id="direction" name="direction" style="padding: 0.65rem 1rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 1rem; background: #ffffff;">
                    <option value="en_vi" {{ old('direction', session('direction', 'en_vi')) === 'en_vi' ? 'selected' : '' }}>
                        🇬🇧 Tiếng Anh &rarr; 🇻🇳 Tiếng Việt
                    </option>
                    <option value="vi_en" {{ old('direction', session('direction')) === 'vi_en' ? 'selected' : '' }}>
                        🇻🇳 Tiếng Việt &rarr; 🇬🇧 Tiếng Anh (Học thuật DET)
                    </option>
                </select>
                @error('direction')
                    <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="content" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                    Nhập văn bản cần dịch (tối đa 3000 ký tự):
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    maxlength="3000"
                    placeholder="Nhập câu hoặc đoạn văn bản tại đây..."
                    required
                    style="width: 100%; box-sizing: border-box; padding: 0.85rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 1rem; line-height: 1.5; resize: vertical;"
                >{{ old('content') }}</textarea>
                @error('content')
                    <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-size: 0.85rem; color: var(--bento-muted);">Mỗi lần dịch tiêu tốn 1 lượt sử dụng AI hàng ngày.</span>
                <button type="submit" style="background: var(--bento-orange-dark); color: #ffffff; font-weight: 700; padding: 0.75rem 1.75rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                    Thực hiện dịch
                </button>
            </div>
        </form>
    </div>

    @if (session('ai_result'))
        @php $result = session('ai_result'); @endphp
        <div style="background: var(--bento-subtle); border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; margin-top: 2rem;">
            <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--bento-line); padding-bottom: 0.75rem;">
                <h2 style="font-size: 1.4rem; color: #0f172a; margin: 0;">Bản dịch từ AI Tutor</h2>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Kết quả dịch:</strong>
                <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 8px; padding: 1rem; color: #0f172a; font-size: 1.05rem; line-height: 1.6;">
                    {{ $result['translation'] ?? 'Không có bản dịch.' }}
                </div>
            </div>

            @if (!empty($result['key_vocabulary']) && is_array($result['key_vocabulary']))
                <div style="margin-bottom: 1.5rem;">
                    <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Từ vựng & Mẫu câu trọng tâm:</strong>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                        @foreach ($result['key_vocabulary'] as $vocab)
                            <div style="background: #ffffff; border-left: 4px solid #f59e0b; padding: 0.75rem 1rem; border-radius: 4px;">
                                <strong style="color: #b45309; font-size: 1rem;">{{ $vocab['word'] ?? '' }}</strong>:
                                <span style="color: var(--bento-ink);">{{ $vocab['meaning'] ?? '' }}</span>
                                @if (!empty($vocab['example']))
                                    <div style="color: var(--bento-muted); font-size: 0.85rem; font-style: italic; margin-top: 0.25rem;">
                                        Ví dụ: "{{ $vocab['example'] }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($result['grammar_notes']))
                <div style="background: #ffffff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 4px;">
                    <strong style="color: #1e3a8a; display: block; margin-bottom: 0.25rem;">Ghi chú ngữ pháp & Cấu trúc hay:</strong>
                    <p style="color: var(--bento-ink); margin: 0; font-size: 0.95rem; line-height: 1.5;">{{ $result['grammar_notes'] }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

