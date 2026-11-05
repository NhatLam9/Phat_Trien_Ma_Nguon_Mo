@extends('layouts.app')

@section('title', 'Viết lại câu nâng band điểm - DET Practice')

@section('content')
<div class="tutor-tool-container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <a href="{{ route('tutor.index') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 0.95rem;">&larr; Quay lại AI Tutor</a>
            <h1 style="font-size: 1.8rem; color: var(--bento-heading); margin-top: 0.25rem;">Viết lại câu & Nâng cấp từ vựng DET</h1>
        </div>
        <a href="{{ route('tutor.history', ['loai' => 'rewrite']) }}" style="font-size: 0.9rem; color: var(--bento-orange-dark); text-decoration: none;">Xem lịch sử viết lại &rarr;</a>
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
        <form method="POST" action="{{ route('tutor.rewrite.process') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label for="style" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                    Chọn phong cách viết lại:
                </label>
                <select id="style" name="style" style="padding: 0.65rem 1rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 1rem; background: #ffffff;">
                    <option value="academic" {{ old('style', session('style', 'academic')) === 'academic' ? 'selected' : '' }}>
                        🏛️ Học thuật (Academic - Cấu trúc phức hợp, chuẩn mực văn viết)
                    </option>
                    <option value="natural" {{ old('style', session('style')) === 'natural' ? 'selected' : '' }}>
                        🌿 Tự nhiên (Natural - Mượt mà như người bản xứ)
                    </option>
                    <option value="det_120" {{ old('style', session('style')) === 'det_120' ? 'selected' : '' }}>
                        🎯 Bứt phá band điểm DET 120+ (Từ vựng C1-C2 sắc bén)
                    </option>
                </select>
                @error('style')
                    <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="content" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                    Nhập câu hoặc đoạn văn bản gốc của bạn (tối đa 3000 ký tự):
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    maxlength="3000"
                    placeholder="Ví dụ: Online learning has many good things but also some bad things for students..."
                    required
                    style="width: 100%; box-sizing: border-box; padding: 0.85rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 1rem; line-height: 1.5; resize: vertical;"
                >{{ old('content') }}</textarea>
                @error('content')
                    <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-size: 0.85rem; color: var(--bento-muted);">Mỗi lần viết lại tiêu tốn 1 lượt sử dụng AI hàng ngày.</span>
                <button type="submit" style="background: var(--bento-orange-dark); color: #ffffff; font-weight: 700; padding: 0.75rem 1.75rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                    Nâng cấp văn phong
                </button>
            </div>
        </form>
    </div>

    @if (session('ai_result'))
        @php $result = session('ai_result'); @endphp
        <div style="background: var(--bento-subtle); border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; margin-top: 2rem;">
            <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--bento-line); padding-bottom: 0.75rem;">
                <h2 style="font-size: 1.4rem; color: #0f172a; margin: 0;">Bản viết lại tối ưu từ AI Mentor</h2>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Câu văn mới được đề xuất:</strong>
                <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 8px; padding: 1.25rem; color: #0f172a; font-size: 1.1rem; line-height: 1.6; font-weight: 600;">
                    {{ $result['rewritten'] ?? 'Không có nội dung viết lại.' }}
                </div>
            </div>

            @if (!empty($result['improvements']) && is_array($result['improvements']))
                <div style="margin-bottom: 1.5rem;">
                    <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Các điểm cải thiện then chốt:</strong>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                        @foreach ($result['improvements'] as $imp)
                            <div style="background: #ffffff; border-left: 4px solid var(--bento-orange-dark); padding: 0.75rem 1rem; border-radius: 4px;">
                                <strong style="color: #15803d; font-size: 1rem;">{{ $imp['aspect'] ?? 'Điểm cộng' }}:</strong>
                                <span style="color: var(--bento-ink);">{{ $imp['detail'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($result['explanation']))
                <div style="background: #ffffff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 4px;">
                    <strong style="color: #1e3a8a; display: block; margin-bottom: 0.25rem;">Vì sao câu này ghi điểm cao hơn trong DET?</strong>
                    <p style="color: var(--bento-ink); margin: 0; font-size: 0.95rem; line-height: 1.5;">{{ $result['explanation'] }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

