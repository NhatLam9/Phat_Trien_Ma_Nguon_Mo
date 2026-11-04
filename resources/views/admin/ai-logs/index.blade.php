@extends('admin.layout')

@section('title', 'Nhật ký AI')
@section('heading', 'Nhật ký gọi dịch vụ AI (ailog)')

@section('content')
<div style="background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); padding: 20px; box-shadow: var(--admin-shadow); margin-bottom: 24px;">
    <!-- Form lọc No-JS (GET) -->
    <form method="GET" action="{{ route('admin.ailogs.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--admin-muted); margin-bottom: 6px;">Tìm học viên (Tên hoặc Email)</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nhập tên hoặc email..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="width: 160px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--admin-muted); margin-bottom: 6px;">Loại yêu cầu</label>
            <select name="loai" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px; background: #fff;">
                <option value="">Tất cả loại</option>
                @foreach ($loaiOptions as $opt)
                    <option value="{{ $opt }}" {{ $loai === $opt ? 'selected' : '' }}>{{ strtoupper($opt) }}</option>
                @endforeach
            </select>
        </div>

        <div style="width: 150px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--admin-muted); margin-bottom: 6px;">Trạng thái</label>
            <select name="trangthai" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px; background: #fff;">
                <option value="">Tất cả trạng thái</option>
                @foreach ($trangthaiOptions as $value => $label)
                    <option value="{{ $value }}" {{ $trangthai === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div style="width: 150px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--admin-muted); margin-bottom: 6px;">Từ ngày</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="width: 150px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--admin-muted); margin-bottom: 6px;">Đến ngày</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px;">
        </div>

        <div>
            <button type="submit" style="background: var(--admin-primary); color: #fff; border: none; padding: 9px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                Lọc nhật ký
            </button>
            <a href="{{ route('admin.ailogs.index') }}" style="display: inline-block; padding: 9px 14px; margin-left: 6px; color: var(--admin-muted); text-decoration: none; font-size: 14px;">
                Đặt lại
            </a>
        </div>
    </form>
</div>

<!-- Bảng hiển thị nhật ký -->
<div style="background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); overflow: hidden; box-shadow: var(--admin-shadow);">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid var(--admin-border); color: var(--admin-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                <th style="padding: 12px 16px;">Mã</th>
                <th style="padding: 12px 16px;">Người dùng</th>
                <th style="padding: 12px 16px;">Loại</th>
                <th style="padding: 12px 16px;">Trạng thái</th>
                <th style="padding: 12px 16px;">Model</th>
                <th style="padding: 12px 16px;">Tokens (Vào / Ra)</th>
                <th style="padding: 12px 16px;">Thời gian</th>
                <th style="padding: 12px 16px; text-align: right;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr style="border-bottom: 1px solid var(--admin-border);">
                    <td style="padding: 12px 16px; font-weight: 600;">#{{ $log->id }}</td>
                    <td style="padding: 12px 16px;">
                        <strong>{{ $log->nguoidung?->hoten ?? 'N/A' }}</strong><br>
                        <span style="font-size: 12px; color: var(--admin-muted);">{{ $log->nguoidung?->email ?? '' }}</span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #e2e8f0; color: #334155;">
                            {{ $log->loai }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: {{ $log->trangthai === 'failed' ? '#fee2e2' : '#dcfce7' }}; color: {{ $log->trangthai === 'failed' ? '#991b1b' : '#166534' }};">
                            {{ $log->trangthai === 'failed' ? 'Thất bại' : 'Thành công' }}
                        </span>
                        @if ($log->http_code)
                            <span style="display: block; font-size: 11px; color: var(--admin-muted); margin-top: 3px;">HTTP {{ $log->http_code }}</span>
                        @endif
                    </td>
                    <td style="padding: 12px 16px; font-family: monospace; font-size: 12px;">{{ $log->model }}</td>
                    <td style="padding: 12px 16px; font-size: 13px;">
                        {{ $log->token_vao ?? 0 }} / {{ $log->token_ra ?? 0 }}
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; color: var(--admin-muted);">
                        {{ $log->ngaytao ? $log->ngaytao->format('H:i:s d/m/Y') : '' }}
                    </td>
                    <td style="padding: 12px 16px; text-align: right;">
                        <a href="{{ route('admin.ailogs.show', $log->id) }}" style="color: #2563eb; text-decoration: none; font-weight: 600; font-size: 13px;">
                            Xem chi tiết &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 32px 16px; text-align: center; color: var(--admin-muted);">
                        Không tìm thấy nhật ký AI nào phù hợp với bộ lọc.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection

