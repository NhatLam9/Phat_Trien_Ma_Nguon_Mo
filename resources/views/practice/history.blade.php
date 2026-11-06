@extends('layouts.app')

@section('title', 'Lịch sử luyện tập theo kỹ năng & dạng bài - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--bento-heading);">Lịch sử luyện tập các dạng bài kỹ năng</h1>
            <p style="color: var(--bento-muted); font-size: 14px; margin-top: 4px;">
                Lưu lại chi tiết quá trình làm bài theo từng kỹ năng và dạng bài (Reading, Listening, Writing, Speaking) trong Duolingo English Test.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.index') }}" class="btn btn-primary btn-sm">Luyện tập thêm dạng bài mới</a>
            <a href="{{ route('practice.bookmarks') }}" class="btn btn-outline btn-sm">Câu hỏi đã lưu</a>
        </div>
    </div>
</div>

<!-- THẺ THỐNG KÊ TỔNG QUAN -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: var(--bento-muted); font-weight: 600; text-transform: uppercase;">Tổng lượt làm bài</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--bento-heading); margin-top: 4px;">{{ $totalAttempts }}</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: var(--bento-muted); font-weight: 600; text-transform: uppercase;">Số câu trả lời đúng</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--bento-orange); margin-top: 4px;">{{ $correctAttempts }}</div>
    </div>

    <div class="card" style="margin-bottom: 0; padding: 20px;">
        <div style="font-size: 13px; color: var(--bento-muted); font-weight: 600; text-transform: uppercase;">Tỷ lệ chính xác</div>
        <div style="font-size: 28px; font-weight: 800; color: {{ $accuracy >= 70 ? 'var(--bento-orange)' : ($accuracy >= 50 ? '#f59e0b' : '#ef4444') }}; margin-top: 4px;">
            {{ $accuracy }}%
        </div>
    </div>
</div>

<!-- BỘ LỌC VÀ TÌM KIẾM CHI TIẾT (KỸ NĂNG, KẾT QUẢ, TỪ KHÓA, NGÀY, THÁNG) -->
<div class="card" style="padding: 20px 24px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('practice.history') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 16px;">
            
            <!-- 1. Kỹ năng -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Kỹ năng
                </label>
                <select name="skill" class="form-control" style="font-size: 13px;">
                    <option value="">-- Tất cả kỹ năng --</option>
                    <option value="reading" {{ ($skill ?? '') === 'reading' ? 'selected' : '' }}>Reading (Đọc)</option>
                    <option value="listening" {{ ($skill ?? '') === 'listening' ? 'selected' : '' }}>Listening (Nghe)</option>
                    <option value="writing" {{ ($skill ?? '') === 'writing' ? 'selected' : '' }}>Writing (Viết)</option>
                    <option value="speaking" {{ ($skill ?? '') === 'speaking' ? 'selected' : '' }}>Speaking (Nói)</option>
                </select>
            </div>

            <!-- 2. Kết quả bài làm -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Kết quả bài làm
                </label>
                <select name="status" class="form-control" style="font-size: 13px;">
                    <option value="">-- Tất cả kết quả --</option>
                    <option value="dung" {{ $status === 'dung' ? 'selected' : '' }}>✓ Câu đúng</option>
                    <option value="sai" {{ $status === 'sai' ? 'selected' : '' }}>✗ Câu chưa đúng</option>
                </select>
            </div>

            <!-- 3. Mốc thời gian nhanh -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Mốc thời gian
                </label>
                <select name="period" id="period-select" class="form-control" style="font-size: 13px;">
                    <option value="">-- Tất cả thời gian --</option>
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                    <option value="7_days" {{ $period === '7_days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30_days" {{ $period === '30_days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Tháng trước</option>
                </select>
            </div>

            <!-- 4. Lọc theo Tháng (tháng/năm) -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Lọc theo Tháng
                </label>
                <input type="month" name="month" id="month-input" value="{{ $month ?? '' }}" class="form-control" style="font-size: 13px;">
            </div>

            <!-- 5. Từ ngày -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Từ ngày
                </label>
                <input type="date" name="from_date" id="from-date-input" value="{{ $fromDate ?? '' }}" class="form-control" style="font-size: 13px;">
            </div>

            <!-- 6. Đến ngày -->
            <div>
                <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Đến ngày
                </label>
                <input type="date" name="to_date" id="to-date-input" value="{{ $toDate ?? '' }}" class="form-control" style="font-size: 13px;">
            </div>
        </div>

        <!-- Hàng 2: Từ khóa tìm kiếm + Các nút hành động -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px;">
            <div style="flex: 1; min-width: 240px;">
                <input type="text" name="q" value="{{ $keyword ?? '' }}" placeholder="Tìm theo nội dung câu trả lời hoặc dạng bài..." class="form-control" style="font-size: 13px;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 20px; font-weight: 700;">
                    Lọc dữ liệu
                </button>
                <a href="{{ route('practice.history') }}" class="btn btn-outline" style="height: 40px; padding: 0 16px;">
                    Đặt lại
                </a>
            </div>
        </div>
    </form>
</div>

<!-- BẢNG LỊCH SỬ CHI TIẾT CÁC DẠNG BÀI ĐÃ LÀM -->
@if($attempts->isEmpty())
    <div class="card" style="text-align: center; padding: 48px 20px;">
        <h3 style="font-size: 18px; color: var(--bento-heading); margin-bottom: 6px;">Chưa tìm thấy lịch sử luyện tập</h3>
        <p style="color: var(--bento-muted); font-size: 14px; margin-bottom: 18px;">Hãy thử điều chỉnh lại bộ lọc hoặc bắt đầu một bài luyện tập mới.</p>
        <a href="{{ route('practice.history') }}" class="btn btn-outline" style="margin-right: 8px;">Xem tất cả</a>
        <a href="{{ route('practice.index') }}" class="btn btn-primary">Luyện tập ngay</a>
    </div>
@else
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid var(--bento-line);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: var(--bento-subtle); border-bottom: 1px solid var(--bento-line); color: #475569; font-weight: 700;">
                    <th style="padding: 14px 18px;">Lần thi #</th>
                    <th style="padding: 14px 18px;">Kỹ năng & Dạng bài</th>
                    <th style="padding: 14px 18px;">Nội dung câu hỏi</th>
                    <th style="padding: 14px 18px;">Câu trả lời</th>
                    <th style="padding: 14px 18px;">Kết quả</th>
                    <th style="padding: 14px 18px;">Điểm</th>
                    <th style="padding: 14px 18px;">Ngày làm</th>
                    <th style="padding: 14px 18px; text-align: right;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $att)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td style="padding: 14px 18px; font-weight: 700; color: var(--bento-muted);">
                            #{{ $att->id }}
                        </td>

                        <td style="padding: 14px 18px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="badge {{ !empty($att->loai_kynang) ? 'badge-' . $att->loai_kynang : 'badge-user' }}" style="font-size: 11px; text-transform: uppercase;">
                                        {{ $att->loai_kynang ?? 'DET' }}
                                    </span>
                                    <strong style="color: var(--bento-heading); font-size: 13px;">
                                        {{ $att->loai_ten ?? 'Câu hỏi DET' }}
                                    </strong>
                                </div>
                                <span style="font-size: 11px; color: var(--bento-muted);">
                                    Độ khó: {{ $att->cauhoi_dokho ?? 5 }}/10
                                </span>
                            </div>
                        </td>

                        <td style="padding: 14px 18px; color: var(--bento-heading); max-width: 240px;">
                            {{ Str::limit($att->cauhoi_noidung ?? ('Câu hỏi #' . $att->cauhoi_id), 60) }}
                        </td>

                        <td style="padding: 14px 18px; color: #475569; max-width: 180px;">
                            {{ Str::limit($att->noidung ?? ($att->luachon_id ? ('Lựa chọn #' . $att->luachon_id) : '-'), 40) }}
                        </td>

                        <td style="padding: 14px 18px;">
                            @if($att->ladung === 1)
                                <span class="badge badge-speaking" style="font-weight: 700;">✓ Đúng</span>
                            @elseif($att->trangthai_ai === 'pending')
                                <span class="badge badge-listening">⏳ Chờ AI</span>
                            @elseif($att->ladung === 0)
                                <span class="badge badge-writing" style="background:#fee2e2;color:#991b1b; font-weight: 700;">✗ Sai</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: var(--bento-muted);">— Chưa chấm</span>
                            @endif
                        </td>

                        <td style="padding: 14px 18px; font-weight: 800; color: var(--bento-heading);">
                            {{ $att->diem ?? 0 }}
                        </td>

                        <td style="padding: 14px 18px; color: var(--bento-muted); font-size: 13px;">
                            {{ date('d/m/Y H:i', strtotime($att->ngaylam)) }}
                        </td>

                        <td style="padding: 14px 18px; text-align: right;">
                            <a href="{{ route('practice.result', $att->id) }}" class="btn btn-outline btn-sm" style="font-size: 12px; font-weight: 600;">
                                Xem lại
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(method_exists($attempts, 'links'))
        <div style="margin-top: 24px;">
            {{ $attempts->links('partials.pagination') }}
        </div>
    @endif
@endif
@endsection
