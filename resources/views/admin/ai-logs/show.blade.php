@extends('admin.layout')

@section('title', 'Chi tiết nhật ký AI #' . $log->id)
@section('heading', 'Chi tiết nhật ký gọi AI #' . $log->id)

@section('content')
<div style="margin-bottom: 16px;">
    <a href="{{ route('admin.ai-logs.index') }}" style="color: var(--admin-muted); text-decoration: none; font-size: 14px;">&larr; Quay lại danh sách nhật ký</a>
</div>

<div style="background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); padding: 24px; box-shadow: var(--admin-shadow); margin-bottom: 24px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 20px;">
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Người thực hiện</span>
            <strong style="font-size: 15px;">{{ $log->nguoidung?->hoten ?? 'N/A' }}</strong>
            <div style="font-size: 13px; color: var(--admin-muted);">{{ $log->nguoidung?->email ?? '' }}</div>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Phân loại yêu cầu</span>
            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: #e2e8f0; color: #334155; margin-top: 4px;">
                {{ $loaiNhan[$log->loai] ?? $log->loai }}
            </span>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Model AI sử dụng</span>
            <strong style="font-size: 14px; font-family: monospace;">{{ $log->model }}</strong>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Trạng thái lần gọi</span>
            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-top: 4px; background: {{ $log->trangthai === 'failed' ? '#fee2e2' : '#dcfce7' }}; color: {{ $log->trangthai === 'failed' ? '#991b1b' : '#166534' }};">
                {{ $log->trangthai === 'failed' ? 'Thất bại' : 'Thành công' }}
            </span>
            @if ($log->http_code)
                <span style="font-size: 12px; color: var(--admin-muted); margin-left: 6px;">HTTP {{ $log->http_code }}</span>
            @endif
        </div>
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Tokens tiêu thụ</span>
            <span style="font-size: 14px;">Vào: <strong>{{ $log->token_vao ?? 0 }}</strong> · Ra: <strong>{{ $log->token_ra ?? 0 }}</strong></span>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--admin-muted); display: block; text-transform: uppercase;">Thời điểm ghi nhận</span>
            <span style="font-size: 14px;">{{ $log->ngaytao ? $log->ngaytao->format('H:i:s d/m/Y') : 'N/A' }}</span>
        </div>
    </div>

    <!-- Dữ liệu đầu vào -->
    <div style="margin-bottom: 24px;">
        <h3 style="font-size: 15px; margin-bottom: 8px; color: var(--admin-text);">Dữ liệu đầu vào (Prompt / Student Submission):</h3>
        <pre style="background: #f8fafc; border: 1px solid var(--admin-border); border-radius: 8px; padding: 16px; font-family: monospace; font-size: 13px; white-space: pre-wrap; line-height: 1.5; color: #1e293b; max-height: 400px; overflow-y: auto;">{{ $log->dauvao ?: '(Trống)' }}</pre>
    </div>

    <!-- Dữ liệu đầu ra -->
    <div>
        <h3 style="font-size: 15px; margin-bottom: 8px; color: var(--admin-text);">Kết quả phản hồi từ AI (Output Response):</h3>
        <pre style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; font-family: monospace; font-size: 13px; white-space: pre-wrap; line-height: 1.5; color: #064e3b; max-height: 400px; overflow-y: auto;">{{ $log->daura ?: '(Trống)' }}</pre>
    </div>
</div>
@endsection

