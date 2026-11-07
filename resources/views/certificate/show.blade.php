@extends('layouts.app')

@php($tieude = $tenDe ? 'Chứng nhận hoàn thành – '.$tenDe : 'Chứng nhận hoàn thành bài thi thử')
@section('title', $tieude.' | DET Practice')

@section('content')
    <main class="certificate-page">
        <style>
            .certificate-page { max-width: 900px; margin: 32px auto; padding: 0 18px; color: #20304a; }
            .certificate-card { position: relative; padding: 44px; border: 8px double var(--bento-orange); border-radius: 18px; background: #fff; text-align: center; }
            .certificate-kicker { color: #16833a; font-size: .8rem; font-weight: 800; letter-spacing: .14em; }
            .certificate-card h1 { margin: 12px 0; font-size: 2rem; }
            .certificate-code { margin: 18px 0; color: #667085; font-family: monospace; }
            .certificate-score { margin: 24px auto; font-size: 2.4rem; font-weight: 800; color: #16833a; }
            .certificate-score small { display: block; margin-top: 4px; font-size: .9rem; font-weight: 500; color: #667085; }
            .certificate-skills { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin: 24px 0; }
            .certificate-skill { padding: 14px 8px; border: 1px solid var(--bento-line); border-radius: 10px; }
            .certificate-skill span { display: block; color: #667085; font-size: .85rem; }
            .certificate-skill strong { display: block; margin-top: 6px; font-size: 1.2rem; }
            .certificate-note { max-width: 650px; margin: 20px auto; color: #667085; font-size: .9rem; }
            .certificate-actions { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
            .certificate-button { display: inline-block; padding: 10px 16px; border: 0; border-radius: 8px; background: var(--bento-orange); color: #17310b; font-weight: 700; text-decoration: none; cursor: pointer; }
            @media (max-width: 600px) { .certificate-card { padding: 28px 16px; } .certificate-skills { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media print { .certificate-page { margin: 0; max-width: none; } .certificate-card { min-height: 85vh; display: flex; flex-direction: column; justify-content: center; } .certificate-actions, .certificate-back { display: none !important; } }
        </style>

        <article class="certificate-card">
            <span class="certificate-kicker">DET PRACTICE · MOCK TEST</span>
            <h1>{{ $tieude }}</h1>

            <p class="certificate-code">Mã chứng chỉ: {{ $chungChi->ma }}</p>

            @if ($chungChi->tongdiem > 0)
                <div class="certificate-score">
                    {{ $chungChi->tongdiem }} / 160
                    <small>Điểm DET đã được lưu cho lượt thi</small>
                </div>

                <section class="certificate-skills" aria-label="Điểm kỹ năng">
                    <div class="certificate-skill"><span>Literacy</span><strong>{{ $chungChi->literacy }}</strong></div>
                    <div class="certificate-skill"><span>Comprehension</span><strong>{{ $chungChi->comprehension }}</strong></div>
                    <div class="certificate-skill"><span>Conversation</span><strong>{{ $chungChi->conversation }}</strong></div>
                    <div class="certificate-skill"><span>Production</span><strong>{{ $chungChi->production }}</strong></div>
                </section>
            @endif

            <p><strong>Ngày cấp:</strong> {{ $chungChi->ngaycap?->format('d/m/Y') ?? 'Chưa ghi nhận' }}</p>
            <p class="certificate-note">Chứng nhận nội bộ do DET Practice cấp cho lượt thi thử này.</p>

            <div class="certificate-actions">
                <button class="certificate-button" type="button" onclick="window.print()">In / Lưu PDF</button>
                <a class="certificate-button certificate-back" href="{{ route('mock-test.result', ['id' => $chungChi->lanthi_id]) }}">Quay lại kết quả</a>
            </div>
        </article>
    </main>
@endsection
