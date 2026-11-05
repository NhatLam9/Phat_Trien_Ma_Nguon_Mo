@extends('layouts.app')

@section('title', 'Lịch sử tương tác AI - DET Practice')

@section('content')
@php
    $nhanLoai = [
        'grammar' => 'Ngữ pháp',
        'translate' => 'Dịch thuật',
        'rewrite' => 'Viết lại',
        'writing' => 'Chấm Writing',
        'speaking' => 'Chấm Speaking',
    ];
@endphp
<div class="tutor-history-container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <a href="{{ route('tutor.index') }}" style="color: #6b7280; text-decoration: none; font-size: 0.95rem;">&larr; Quay lại AI Tutor</a>
            <h1 style="font-size: 1.8rem; color: #111827; margin-top: 0.25rem;">Lịch sử tương tác AI Tutor & Chấm bài</h1>
        </div>
    </div>

    <!-- Bộ lọc loại yêu cầu -->
    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
        <span style="font-weight: 700; color: #374151; margin-right: 0.5rem;">Lọc theo tính năng:</span>
        <a href="{{ route('tutor.history') }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ empty($loaiFilter) ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Tất cả</a>
        <a href="{{ route('tutor.history', ['loai' => 'grammar']) }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ $loaiFilter === 'grammar' ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Ngữ pháp (Grammar)</a>
        <a href="{{ route('tutor.history', ['loai' => 'translate']) }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ $loaiFilter === 'translate' ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Dịch thuật (Translate)</a>
        <a href="{{ route('tutor.history', ['loai' => 'rewrite']) }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ $loaiFilter === 'rewrite' ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Viết lại (Rewrite)</a>
        <a href="{{ route('tutor.history', ['loai' => 'writing']) }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ $loaiFilter === 'writing' ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Chấm Writing</a>
        <a href="{{ route('tutor.history', ['loai' => 'speaking']) }}" style="padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ $loaiFilter === 'speaking' ? 'background: #2563eb; color: #fff; font-weight: 700;' : 'background: #f1f5f9; color: #475569;' }}">Chấm Speaking</a>
    </div>

    @if ($logs->isEmpty())
        <div style="background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 3rem 1rem; text-align: center; color: #64748b;">
            <p style="font-size: 1.1rem; margin-bottom: 1rem;">Bạn chưa có lịch sử tương tác nào trong mục này.</p>
            <a href="{{ route('tutor.index') }}" style="background: #2563eb; color: #fff; padding: 0.65rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 700;">
                Bắt đầu học cùng AI Tutor &rarr;
            </a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2rem;">
            @foreach ($logs as $log)
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; background: #e2e8f0; color: #334155; padding: 0.25rem 0.65rem; border-radius: 4px;">
                            {{ $nhanLoai[$log->loai] ?? $log->loai }}
                        </span>
                        <span style="font-size: 0.85rem; color: #94a3b8;">
                            {{ $log->ngaytao ? $log->ngaytao->format('H:i:s d/m/Y') : '' }} · Model: {{ $log->model }}
                        </span>
                    </div>

                    <div style="margin-bottom: 0.75rem;">
                        <strong style="font-size: 0.9rem; color: #475569; display: block; margin-bottom: 0.25rem;">Nội dung của bạn:</strong>
                        <div style="background: #f8fafc; border-left: 3px solid #94a3b8; padding: 0.65rem 0.85rem; color: #1e293b; font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap;">{{ Str::limit($log->dauvao, 300) }}</div>
                    </div>

                    <div>
                        <strong style="font-size: 0.9rem; color: #475569; display: block; margin-bottom: 0.25rem;">Kết quả từ AI:</strong>
                        <div style="background: #f0fdf4; border-left: 3px solid #22c55e; padding: 0.75rem 0.9rem; color: #064e3b; font-size: 0.95rem; line-height: 1.5;">
                            @include('tutor.partials.ai-result', ['log' => $log])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

