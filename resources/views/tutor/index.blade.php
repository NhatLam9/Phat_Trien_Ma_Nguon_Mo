@extends('layouts.app')

@section('title', 'AI Tutor - DET Practice')

@section('content')
<div class="tutor-container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <h1 style="font-size: 2.2rem; color: var(--bento-ink); margin-bottom: 0.5rem;">Trợ lý học tập AI Tutor</h1>
        <p style="color: var(--bento-muted); font-size: 1.1rem;">Nâng cao năng lực ngữ pháp, từ vựng và chuẩn bị bứt phá band điểm bài thi Duolingo English Test (DET).</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Card 1: Grammar Checker -->
        <div style="background: #ffffff; border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div>
                <div style="width: 48px; height: 48px; background: var(--bento-soft); color: var(--bento-orange-dark); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">
                    Aa
                </div>
                <h2 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--bento-heading);">Sửa ngữ pháp chuyên sâu</h2>
                <p style="color: var(--bento-muted); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    Tự động tìm lỗi sai thì, mạo từ, cấu trúc câu, đưa ra bản sửa hoàn chỉnh và giải thích chi tiết bằng tiếng Việt.
                </p>
            </div>
            <a href="{{ route('tutor.grammar') }}" style="display: block; text-align: center; background: var(--bento-orange); color: #fff; padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 700;">
                Mở công cụ sửa ngữ pháp &rarr;
            </a>
        </div>

        <!-- Card 2: Translator -->
        <div style="background: #ffffff; border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div>
                <div style="width: 48px; height: 48px; background: #fef3c7; color: var(--bento-orange-dark); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">
                    🌐
                </div>
                <h2 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--bento-heading);">Dịch thuật 2 chiều thông minh</h2>
                <p style="color: var(--bento-muted); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    Dịch chuẩn xác ngữ cảnh Anh - Việt và Việt - Anh, bóc tách từ vựng học thuật quan trọng cho bài thi DET.
                </p>
            </div>
            <a href="{{ route('tutor.translate') }}" style="display: block; text-align: center; background: var(--bento-orange-dark); color: #fff; padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 700;">
                Mở công cụ dịch thuật &rarr;
            </a>
        </div>

        <!-- Card 3: Rewriter -->
        <div style="background: #ffffff; border: 2px solid var(--bento-line); border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div>
                <div style="width: 48px; height: 48px; background: var(--bento-soft); color: var(--bento-orange-dark); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">
                    ✨
                </div>
                <h2 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--bento-heading);">Viết lại câu nâng band điểm</h2>
                <p style="color: var(--bento-muted); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    Biến câu văn đơn giản thành câu học thuật (Academic), tự nhiên (Natural) hoặc chuẩn mực band điểm DET 120+.
                </p>
            </div>
            <a href="{{ route('tutor.rewrite') }}" style="display: block; text-align: center; background: var(--bento-orange-dark); color: #fff; padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 700;">
                Mở công cụ viết lại câu &rarr;
            </a>
        </div>
    </div>

    <!-- Banner lịch sử -->
    <div style="background: var(--bento-subtle); border: 1px dashed var(--bento-line); border-radius: 10px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <strong style="color: var(--bento-ink);">Lịch sử hỏi đáp AI Tutor:</strong>
            <span style="color: var(--bento-muted); font-size: 0.95rem; margin-left: 0.5rem;">Xem lại tất cả các đoạn văn, bản dịch và lời khuyên AI đã cung cấp cho bạn.</span>
        </div>
        <a href="{{ route('tutor.history') }}" style="background: #ffffff; border: 1px solid #94a3b8; color: var(--bento-ink); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            Xem lịch sử tương tác &rarr;
        </a>
    </div>
</div>
@endsection

