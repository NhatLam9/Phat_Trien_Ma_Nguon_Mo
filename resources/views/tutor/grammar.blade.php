@extends('layouts.app')

@section('title', 'Sửa lỗi ngữ pháp AI - DET Practice')

@section('content')
<div class="tutor-tool-container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <a href="{{ route('tutor.index') }}" style="color: var(--bento-muted); text-decoration: none; font-size: 0.95rem;">&larr; Quay lại AI Tutor</a>
            <h1 style="font-size: 1.8rem; color: var(--bento-heading); margin-top: 0.25rem;">Kiểm tra & Sửa lỗi ngữ pháp DET</h1>
        </div>
        <a href="{{ route('tutor.history', ['loai' => 'grammar']) }}" style="font-size: 0.9rem; color: var(--bento-orange); text-decoration: none;">Xem lịch sử sửa &rarr;</a>
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
        <form method="POST" action="{{ route('tutor.grammar.process') }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="content" style="display: block; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                    Nhập câu hoặc đoạn văn tiếng Anh của bạn (tối đa 3000 ký tự):
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    maxlength="3000"
                    placeholder="Ví dụ: Yesterday I go to school with my friend and we study very hardly..."
                    required
                    style="width: 100%; box-sizing: border-box; padding: 0.85rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 1rem; line-height: 1.5; resize: vertical;"
                >{{ old('content') }}</textarea>
                @error('content')
                    <p style="color: #dc2626; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-size: 0.85rem; color: var(--bento-muted);">Mỗi lần kiểm tra tiêu tốn 1 lượt sử dụng AI hàng ngày.</span>
                <button type="submit" style="background: var(--bento-orange); color: #ffffff; font-weight: 700; padding: 0.75rem 1.75rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                    Phân tích & Sửa lỗi
                </button>
            </div>
        </form>
    </div>

    @if (session('ai_result'))
        @php $result = session('ai_result'); @endphp
        <div style="background: var(--bento-subtle); border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.75rem; margin-top: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid var(--bento-line); padding-bottom: 0.75rem;">
                <h2 style="font-size: 1.4rem; color: #0f172a; margin: 0;">Kết quả sửa lỗi ngữ pháp</h2>
                <span style="background: {{ ($result['error_count'] ?? 0) === 0 ? 'var(--bento-soft); color: #166534' : '#fee2e2; color: #991b1b' }}; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700;">
                    Phát hiện: {{ $result['error_count'] ?? count($result['errors'] ?? []) }} lỗi
                </span>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Bản sửa hoàn chỉnh:</strong>
                <div style="background: #ffffff; border: 1px solid #94a3b8; border-radius: 8px; padding: 1rem; color: #0f172a; font-size: 1.05rem; line-height: 1.6;">
                    {{ $result['corrected'] ?? 'Không có nội dung sửa đổi.' }}
                </div>
            </div>

            @if (!empty($result['errors']) && is_array($result['errors']))
                <div style="margin-bottom: 1.5rem;">
                    <strong style="display: block; color: var(--bento-ink); margin-bottom: 0.5rem; font-size: 1rem;">Chi tiết các lỗi được phát hiện:</strong>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach ($result['errors'] as $err)
                            <div style="background: #ffffff; border-left: 4px solid #ef4444; padding: 0.85rem 1rem; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                                    <span style="text-decoration: line-through; color: #dc2626; font-weight: 600;">{{ $err['original'] ?? '' }}</span>
                                    <span style="color: var(--bento-muted);">&rarr;</span>
                                    <span style="color: var(--bento-orange-dark); font-weight: 700;">{{ $err['replacement'] ?? '' }}</span>
                                </div>
                                <div style="color: #475569; font-size: 0.9rem;">
                                    {{ $err['explanation'] ?? '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($result['overall_comment']))
                <div style="background: #ffffff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 4px;">
                    <strong style="color: #1e3a8a; display: block; margin-bottom: 0.25rem;">Nhận xét của AI Tutor:</strong>
                    <p style="color: var(--bento-ink); margin: 0; font-size: 0.95rem; line-height: 1.5;">{{ $result['overall_comment'] }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

