@extends('admin.layout')

@section('title', 'Đề thi')
@section('heading', 'Quản lý đề thi')

@php
    $nhanTrangThai = [
        'draft' => 'Bản nháp',
        'published' => 'Đã xuất bản',
        'hidden' => 'Đã ẩn',
    ];
    $nhanKyNang = [
        'reading' => 'Reading',
        'listening' => 'Listening',
        'writing' => 'Writing',
        'speaking' => 'Speaking',
    ];
@endphp

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Tìm kiếm và lọc đề thi</h2>
                <p class="admin-muted">
                    Đề thi có thể được tạo trực tiếp bằng giao diện hoặc nhập từ JSON / dữ liệu mẫu.
                    Trang này giúp rà soát cấu trúc đề trước khi mở cho học viên làm bài.
                </p>
            </div>
            <div class="admin-row-actions">
                <a href="{{ route('admin.exams.create') }}" class="admin-btn">
                    Tạo đề thi
                </a>
                <a href="{{ route('admin.questions.import.create') }}" class="admin-btn admin-btn--ghost">
                    Nhập đề từ JSON
                </a>
            </div>
        </div>

        <div class="admin-tags">
            <a class="admin-chip {{ $boLoc['trangthai'] === '' ? 'is-active' : '' }}"
               href="{{ route('admin.exams.index', array_filter(array_merge($boLoc, ['trangthai' => '']))) }}">
                Tất cả ({{ $thongKe['tong'] }})
            </a>
            @foreach ($nhanTrangThai as $ma => $nhan)
                <a class="admin-chip {{ $boLoc['trangthai'] === $ma ? 'is-active' : '' }}"
                   href="{{ route('admin.exams.index', array_filter(array_merge($boLoc, ['trangthai' => $ma]))) }}">
                    {{ $nhan }} ({{ $thongKe[$ma] }})
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.exams.index') }}" class="admin-filter">
            <div class="admin-filter__field">
                <label for="q">Từ khóa</label>
                <input type="search" id="q" name="q" value="{{ $boLoc['q'] }}"
                       placeholder="Tên hoặc mô tả đề thi">
            </div>

            <div class="admin-filter__field">
                <label for="kynang">Kỹ năng</label>
                <select id="kynang" name="kynang">
                    <option value="">Tất cả</option>
                    @foreach ($nhanKyNang as $ma => $nhan)
                        <option value="{{ $ma }}" @selected($boLoc['kynang'] === $ma)>{{ $nhan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="trangthai">Trạng thái</label>
                <select id="trangthai" name="trangthai">
                    <option value="">Tất cả</option>
                    @foreach ($nhanTrangThai as $ma => $nhan)
                        <option value="{{ $ma }}" @selected($boLoc['trangthai'] === $ma)>{{ $nhan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__actions">
                <button type="submit" class="admin-btn">Lọc</button>
                @if ($coBoLoc)
                    <a href="{{ route('admin.exams.index') }}" class="admin-btn admin-btn--ghost">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách đề thi</h2>
                <p class="admin-muted">
                    @if ($dethis->total() === 0)
                        Không có đề thi nào khớp bộ lọc.
                    @else
                        Hiển thị {{ $dethis->firstItem() }}–{{ $dethis->lastItem() }}
                        trong {{ number_format($dethis->total(), 0, ',', '.') }} đề thi.
                    @endif
                </p>
            </div>
        </div>

        @if ($dethis->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đề thi</th>
                            <th>Kỹ năng</th>
                            <th>Chế độ / Kiểu</th>
                            <th>Thời gian</th>
                            <th>Số câu</th>
                            <th>Tổng điểm</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Cập nhật</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dethis as $dethi)
                            <tr>
                                <td>{{ $dethi->id }}</td>
                                <td class="admin-table__wide">
                                    <a href="{{ route('admin.exams.show', $dethi) }}">
                                        {{ \Illuminate\Support\Str::limit($dethi->ten, 90) }}
                                    </a>
                                    @if ($dethi->mota)
                                        <div class="admin-muted">{{ \Illuminate\Support\Str::limit($dethi->mota, 110) }}</div>
                                    @endif
                                </td>
                                <td>{{ $dethi->kynang ? ($nhanKyNang[$dethi->kynang] ?? $dethi->kynang) : 'Tổng hợp' }}</td>
                                <td>
                                    {{ $dethi->chedo === 'section' ? 'Theo kỹ năng' : 'Toàn bài' }}
                                    <div class="admin-muted">{{ $dethi->kieu === 'adaptive' ? 'Thích ứng' : 'Cố định' }}</div>
                                </td>
                                <td>{{ $dethi->thoigian }} giây</td>
                                <td>{{ $dethi->dethicauhoi_count }}</td>
                                <td>{{ rtrim(rtrim(number_format((float) $dethi->tongdiem, 2, ',', '.'), '0'), ',') }}</td>
                                <td>
                                    <span class="admin-badge admin-badge--{{ $dethi->trangthai === 'published' ? 'published' : ($dethi->trangthai === 'hidden' ? 'archived' : 'draft') }}">
                                        {{ $nhanTrangThai[$dethi->trangthai] ?? $dethi->trangthai }}
                                    </span>
                                </td>
                                <td class="admin-muted">{{ $dethi->nguoiTao?->hoten ?? '—' }}</td>
                                <td class="admin-muted">
                                    {{ $dethi->ngaycapnhat?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td>
                                    <div class="admin-row-actions">
                                        <a href="{{ route('admin.exams.show', $dethi) }}" class="admin-btn admin-btn--small admin-btn--ghost">
                                            Xem
                                        </a>
                                        <a href="{{ route('admin.exams.edit', $dethi) }}" class="admin-btn admin-btn--small">
                                            Sửa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $dethis->links('admin.partials.pagination') }}
        @else
            <div class="admin-empty">
                <p>Chưa có đề thi nào. Hãy tạo đề bằng giao diện hoặc nhập từ file JSON để bắt đầu.</p>
                <p class="admin-row-actions" style="justify-content: center;">
                    <a href="{{ route('admin.exams.create') }}">Tạo đề thi</a>
                    <a href="{{ route('admin.questions.import.create') }}">Nhập câu hỏi từ JSON</a>
                </p>
            </div>
        @endif
    </div>
@endsection
