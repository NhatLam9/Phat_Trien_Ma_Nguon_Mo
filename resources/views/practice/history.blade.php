@extends('layouts.app')

@section('title', 'Lịch sử luyện tập - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #1e293b;">⏱️ Lịch sử luyện tập cá nhân</h1>
            <p style="color: #64748b; font-size: 14px; margin-top: 4px;">
                Theo dõi tiến độ, tỷ lệ chính xác và xem lại các bài làm trong quá trình ôn luyện Duolingo English Test.
            </p>
        </div>
        <a href="{{ route('practice.index') }}" class="btn btn-primary btn-sm">Luyện tập thêm</a>
    </div>
</div>

<!-- THẺ THỐNG KÊ TỔNG QUAN -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng lượt làm bài</div>
        <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-top: 4px;">{{ $totalAttempts }}</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase;">Số câu trả lời đúng</div>
        <div style="font-size: 28px; font-weight: 800; color: #00b86b; margin-top: 4px;">{{ $correctAttempts }}</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tỷ lệ chính xác</div>
        <div style="font-size: 28px; font-weight: 800; color: {{ $accuracy >= 70 ? '#00b86b' : ($accuracy >= 50 ? '#f59e0b' : '#ef4444') }}; margin-top: 4px;">
            {{ $accuracy }}%
        </div>
    </div>
</div>

<!-- BỘ LỌC VÀ TÌM KIẾM (LT08 & LT09) -->
<div class="card" style="padding: 16px 20px;">
    <form method="GET" action="{{ route('practice.history') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Kết quả bài làm</label>
            <select name="status" class="form-control" style="font-size: 13px;">
                <option value="">-- Tất cả kết quả --</option>
                <option value="dung" {{ $status === 'dung' ? 'selected' : '' }}>✓ Câu đúng</option>
                <option value="sai" {{ $status === 'sai' ? 'selected' : '' }}>✗ Câu chưa đúng</option>
            </select>
        </div>

        <div style="flex: 2; min-width: 220px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Tìm kiếm từ khóa (LT09)</label>
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Tìm theo nội dung trả lời..." class="form-control" style="font-size: 13px;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 18px;">Lọc</button>
            <a href="{{ route('practice.history') }}" class="btn btn-outline" style="height: 40px; padding: 0 14px;">Đặt lại</a>
        </div>
    </form>
</div>

<!-- BẢNG LỊCH SỬ CHI TIẾT -->
@if($attempts->isEmpty())
    <div class="card" style="text-align: center; padding: 48px 20px;">
        <div style="font-size: 40px; margin-bottom: 12px;">📋</div>
        <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">Chưa có lịch sử làm bài nào</h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 18px;">Hãy bắt đầu luyện tập các câu hỏi đầu tiên để ghi nhận tiến độ học tập của bạn.</p>
        <a href="{{ route('practice.index') }}" class="btn btn-primary">Luyện tập ngay</a>
    </div>
@else
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600;">
                    <th style="padding: 14px 20px;">Lần thi #</th>
                    <th style="padding: 14px 20px;">Mã câu</th>
                    <th style="padding: 14px 20px;">Nội dung trả lời</th>
                    <th style="padding: 14px 20px;">Kết quả</th>
                    <th style="padding: 14px 20px;">Điểm</th>
                    <th style="padding: 14px 20px;">Thời gian</th>
                    <th style="padding: 14px 20px;">Ngày làm</th>
                    <th style="padding: 14px 20px; text-align: right;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $att)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; font-weight: 700; color: #64748b;">
                            #{{ $att->id }}
                        </td>
                        <td style="padding: 14px 20px; font-weight: 600; color: #00b86b;">
                            Câu {{ $att->cauhoi_id }}
                        </td>
                        <td style="padding: 14px 20px; color: #1e293b; max-width: 250px;">
                            {{ Str::limit($att->noidung ?? ($att->luachon_id ? ('Lựa chọn #' . $att->luachon_id) : '-'), 60) }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($att->ladung)
                                <span class="badge badge-speaking">✓ Đúng</span>
                            @elseif($att->trangthai_ai === 'pending')
                                <span class="badge badge-listening">⏳ Chờ AI</span>
                            @else
                                <span class="badge badge-writing" style="background:#fee2e2;color:#991b1b;">✗ Sai</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700;">
                            {{ $att->diem ?? 0 }}
                        </td>
                        <td style="padding: 14px 20px; color: #64748b; font-size: 13px;">
                            {{ $att->thoigianlam ?? 0 }}s
                        </td>
                        <td style="padding: 14px 20px; color: #64748b; font-size: 13px;">
                            {{ date('d/m/Y H:i', strtotime($att->ngaylam)) }}
                        </td>
                        <td style="padding: 14px 20px; text-align: right;">
                            <a href="{{ route('practice.result', $att->id) }}" class="btn btn-outline btn-sm">
                                Xem lại
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $attempts->links() }}
    </div>
@endif
@endsection

