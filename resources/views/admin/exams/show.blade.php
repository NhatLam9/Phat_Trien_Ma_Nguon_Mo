@extends('admin.layout')

@section('title', 'Chi tiết đề thi')
@section('heading', 'Chi tiết đề thi')

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
                <p class="admin-muted" style="margin-bottom: 0.35rem;">
                    <a href="{{ route('admin.exams.index') }}">← Danh sách đề thi</a>
                </p>
                <h2>{{ $dethi->ten }}</h2>
                <p class="admin-muted">{{ $dethi->mota ?: 'Đề thi chưa có mô tả.' }}</p>
            </div>
            <span class="admin-badge admin-badge--{{ $dethi->trangthai === 'published' ? 'published' : ($dethi->trangthai === 'hidden' ? 'archived' : 'draft') }}">
                {{ $nhanTrangThai[$dethi->trangthai] ?? $dethi->trangthai }}
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Mã đề</th>
                        <td>{{ $dethi->id }}</td>
                    </tr>
                    <tr>
                        <th>Kỹ năng</th>
                        <td>{{ $dethi->kynang ? ($nhanKyNang[$dethi->kynang] ?? $dethi->kynang) : 'Tổng hợp (nhiều kỹ năng)' }}</td>
                    </tr>
                    <tr>
                        <th>Chế độ / Kiểu</th>
                        <td>
                            {{ $dethi->chedo === 'section' ? 'Theo kỹ năng' : 'Toàn bài' }}
                            · {{ $dethi->kieu === 'adaptive' ? 'Thích ứng' : 'Cố định' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Thời gian làm bài</th>
                        <td>{{ $dethi->thoigian }} giây ({{ round($dethi->thoigian / 60) }} phút)</td>
                    </tr>
                    <tr>
                        <th>Tổng điểm</th>
                        <td>{{ rtrim(rtrim(number_format((float) $dethi->tongdiem, 2, ',', '.'), '0'), ',') }}</td>
                    </tr>
                    <tr>
                        <th>Số câu hỏi</th>
                        <td>{{ $dethi->dethicauhoi->count() }}</td>
                    </tr>
                    <tr>
                        <th>Người tạo</th>
                        <td>{{ $dethi->nguoiTao?->hoten ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo / cập nhật</th>
                        <td>
                            {{ $dethi->ngaytao?->format('d/m/Y H:i') ?? '—' }}
                            · {{ $dethi->ngaycapnhat?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Cấu trúc đề</h2>
                <p class="admin-muted">Thứ tự câu hỏi và điểm từng câu trong đề thi.</p>
            </div>
        </div>

        @if ($dethi->dethicauhoi->isEmpty())
            <div class="admin-empty">
                <p>Đề thi chưa có câu hỏi nào.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thứ tự</th>
                            <th>Mã câu</th>
                            <th>Nội dung</th>
                            <th>Dạng bài</th>
                            <th>Kỹ năng</th>
                            <th>Chủ đề</th>
                            <th>Điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dethi->dethicauhoi as $dong)
                            <tr>
                                <td>{{ $dong->thutu }}</td>
                                <td>
                                    @if ($dong->cauhoi)
                                        <a href="{{ route('admin.questions.show', $dong->cauhoi) }}">
                                            <code>{{ $dong->cauhoi->ma }}</code>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="admin-table__wide">
                                    {{ $dong->cauhoi ? \Illuminate\Support\Str::limit($dong->cauhoi->noidung, 110) : 'Câu hỏi đã bị xoá' }}
                                </td>
                                <td>{{ $dong->cauhoi?->loai?->ten ?? '—' }}</td>
                                <td>{{ $dong->cauhoi?->loai?->kynang ?? '—' }}</td>
                                <td>{{ $dong->cauhoi?->danhmuc?->ten ?? '—' }}</td>
                                <td>{{ rtrim(rtrim(number_format((float) $dong->diem, 2, ',', '.'), '0'), ',') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
