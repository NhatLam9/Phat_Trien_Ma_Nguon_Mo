@extends('admin.layout')

@section('title', 'Nhật ký AI')
@section('heading', 'Nhật ký gọi AI')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Lọc nhật ký</h2>
                <p class="admin-muted">
                    Chỉ đọc. Dùng để kiểm tra lỗi chấm Writing/Speaking hoặc lạm dụng lượt gọi AI.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.ai-logs.index') }}" class="admin-filter">
            <div class="admin-filter__field">
                <label for="q">Người dùng</label>
                <input type="search" id="q" name="q" value="{{ $boLoc['q'] }}"
                       placeholder="Họ tên hoặc email">
            </div>

            <div class="admin-filter__field">
                <label for="loai">Loại yêu cầu</label>
                <select id="loai" name="loai">
                    <option value="">Tất cả</option>
                    @foreach ($loaiNhan as $ma => $nhan)
                        <option value="{{ $ma }}" @selected($boLoc['loai'] === $ma)>{{ $nhan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="tu_ngay">Từ ngày</label>
                <input type="date" id="tu_ngay" name="tu_ngay" value="{{ $boLoc['tu_ngay'] }}">
            </div>

            <div class="admin-filter__field">
                <label for="den_ngay">Đến ngày</label>
                <input type="date" id="den_ngay" name="den_ngay" value="{{ $boLoc['den_ngay'] }}">
            </div>

            <div class="admin-filter__actions">
                <button type="submit" class="admin-btn">Lọc</button>
                @if ($coBoLoc)
                    <a href="{{ route('admin.ai-logs.index') }}" class="admin-btn admin-btn--ghost">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách lượt gọi</h2>
                <p class="admin-muted">
                    @if ($ailogs->total() === 0)
                        Chưa có lượt gọi AI nào khớp bộ lọc.
                    @else
                        Hiển thị {{ $ailogs->firstItem() }}–{{ $ailogs->lastItem() }}
                        trong {{ number_format($ailogs->total(), 0, ',', '.') }} lượt.
                    @endif
                </p>
            </div>
        </div>

        @if ($ailogs->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thời gian</th>
                            <th>Người dùng</th>
                            <th>Loại</th>
                            <th>Model</th>
                            <th>Token</th>
                            <th>Nội dung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ailogs as $ailog)
                            <tr>
                                <td>{{ $ailog->id }}</td>
                                <td class="admin-muted">
                                    {{ $ailog->ngaytao?->format('d/m/Y H:i:s') ?? '—' }}
                                </td>
                                <td>
                                    {{ $ailog->nguoidung?->hoten ?? 'Tài khoản đã xóa' }}
                                    <div class="admin-muted">{{ $ailog->nguoidung?->email ?? '—' }}</div>
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge--draft">
                                        {{ $loaiNhan[$ailog->loai] ?? $ailog->loai }}
                                    </span>
                                </td>
                                <td>{{ $ailog->model ?? '—' }}</td>
                                <td class="admin-muted">
                                    {{ $ailog->token_vao ?? 0 }} / {{ $ailog->token_ra ?? 0 }}
                                </td>
                                <td class="admin-table__wide">
                                    <details class="admin-details">
                                        <summary>
                                            {{ \Illuminate\Support\Str::limit(strip_tags((string) $ailog->dauvao), 90) ?: 'Xem nội dung' }}
                                        </summary>
                                        <div class="admin-details__body">
                                            <h3>Đầu vào</h3>
                                            <pre class="admin-code">{{ $ailog->dauvao ?? '—' }}</pre>
                                            <h3>Đầu ra</h3>
                                            <pre class="admin-code">{{ $ailog->daura ?? '—' }}</pre>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $ailogs->links('admin.partials.pagination') }}
        @endif
    </div>
@endsection
