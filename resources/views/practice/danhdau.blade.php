@extends('layouts.app')

@section('title', 'Câu hỏi đã đánh dấu - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #1e293b;">⭐ Danh sách câu hỏi đã đánh dấu</h1>
            <p style="color: #64748b; font-size: 14px; margin-top: 4px;">
                Tập hợp các câu hỏi bạn cảm thấy khó hoặc còn yếu để chủ động ôn luyện lại và khắc phục lỗi sai.
            </p>
        </div>
        <a href="{{ route('practice.index') }}" class="btn btn-outline btn-sm">← Danh sách dạng bài</a>
    </div>
</div>

@if($bookmarks->isEmpty())
    <div class="card" style="text-align: center; padding: 50px 20px;">
        <div style="font-size: 48px; margin-bottom: 12px;">🌟</div>
        <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">Bạn chưa đánh dấu câu hỏi nào</h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
            Khi đang làm bài luyện tập, bạn có thể nhấn vào nút <strong>"☆ Đánh dấu câu yếu"</strong> để lưu câu hỏi vào đây.
        </p>
        <a href="{{ route('practice.index') }}" class="btn btn-primary">Bắt đầu luyện tập ngay</a>
    </div>
@else
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600;">
                    <th style="padding: 14px 20px;">Mã câu</th>
                    <th style="padding: 14px 20px;">Nội dung câu hỏi</th>
                    <th style="padding: 14px 20px;">Ngày lưu</th>
                    <th style="padding: 14px 20px; text-align: right;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookmarks as $bm)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; font-weight: 700; color: #00b86b;">
                            #{{ $bm->cauhoi_id }}
                        </td>
                        <td style="padding: 14px 20px; color: #1e293b;">
                            <strong>{{ Str::limit($bm->cauhoi->noidung ?? ('Câu hỏi ID #' . $bm->cauhoi_id), 90) }}</strong>
                        </td>
                        <td style="padding: 14px 20px; color: #64748b; font-size: 13px;">
                            {{ date('d/m/Y H:i', strtotime($bm->ngaytao)) }}
                        </td>
                        <td style="padding: 14px 20px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px; align-items: center;">
                                <a href="{{ route('practice.show', $bm->cauhoi_id) }}" class="btn btn-primary btn-sm">
                                    Luyện tập lại
                                </a>

                                <form method="POST" action="{{ route('practice.bookmark.toggle', $bm->cauhoi_id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: #fca5a5;" title="Bỏ đánh dấu">
                                        ✕ Bỏ lưu
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $bookmarks->links() }}
    </div>
@endif
@endsection

