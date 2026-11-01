@extends('layouts.app')

@section('title', 'Lịch sử thi thử - DET Practice')
@section('main_class', 'app-main--mock-test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
    <style>
        .history-page { max-width: 1100px; margin: 0 auto; padding: 32px 20px 56px; }
        .history-heading { margin-bottom: 24px; }
        .history-heading h1 { margin: 6px 0 8px; }
        .history-heading p, .history-note { color: #667085; }
        .history-note { padding: 12px 16px; margin: 18px 0; border-radius: 12px; background: #f3f8ed; }
        .history-table-wrap { overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; }
        .history-table { width: 100%; border-collapse: collapse; min-width: 760px; }
        .history-table th, .history-table td { padding: 14px 16px; border-bottom: 1px solid #eef0f2; text-align: left; vertical-align: middle; }
        .history-table th { color: #475467; font-size: .85rem; background: #f9fafb; }
        .history-table tr:last-child td { border-bottom: 0; }
        .history-status { display: inline-block; padding: 4px 9px; border-radius: 999px; background: #f2f4f7; font-size: .82rem; white-space: nowrap; }
        .history-status--completed { color: #18794e; background: #eaf7ef; }
        .history-status--abandoned { color: #9a3412; background: #fff1e8; }
        .history-score small { display: block; color: #667085; margin-top: 3px; }
        .history-empty { padding: 28px; color: #667085; text-align: center; }
        .history-pagination { margin-top: 20px; }
        .history-link { font-weight: 700; color: #167a36; text-decoration: none; }
        .history-link:hover { text-decoration: underline; }
    </style>
@endpush

@section('content')
    <section class="history-page">
        <header class="history-heading">
            <span class="eyebrow">MOCK TEST · MT07</span>
            <h1>Lịch sử thi thử</h1>
            <p>Xem lại trạng thái, kết quả thô và các lượt thi trước đây của bạn.</p>
        </header>

        <p class="history-note">
            Kết quả bên dưới là điểm thô và tỷ lệ trên đề. Điểm DET 10–160 chỉ hiển thị
            khi lượt thi đã được quy đổi theo công thức được dự án xác nhận.
        </p>

        <div class="history-table-wrap">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Đề thi</th>
                        <th>Bắt đầu</th>
                        <th>Trạng thái</th>
                        <th>Kết quả</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lanThis as $lanThi)
                        @php
                            $statusLabel = match ($lanThi->trangthai) {
                                'completed' => 'Đã hoàn thành',
                                'abandoned' => 'Đã bỏ dở',
                                default => 'Đang làm',
                            };
                            $statusClass = match ($lanThi->trangthai) {
                                'completed' => 'history-status--completed',
                                'abandoned' => 'history-status--abandoned',
                                default => '',
                            };
                            $thoiDiem = $lanThi->ketthuc ?: $lanThi->batdau;
                        @endphp
                        <tr>
                            <td>{{ $lanThi->ten_de_thi ?: 'Đề thi #' . $lanThi->id }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($thoiDiem)->format('d/m/Y H:i') }}</td>
                            <td><span class="history-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td class="history-score">
                                @if ($lanThi->trangthai === 'completed' && $lanThi->ket_qua_tho)
                                    {{ number_format($lanThi->ket_qua_tho['diem_da_cham'], 2) }} /
                                    {{ number_format($lanThi->ket_qua_tho['diem_toi_da'], 2) }} điểm thô
                                    <small>
                                        @if ($lanThi->ket_qua_tho['ty_le_phan_tram'] !== null)
                                            Tỷ lệ: {{ number_format($lanThi->ket_qua_tho['ty_le_phan_tram'], 1) }}%
                                        @else
                                            Đang chờ chấm một số câu
                                        @endif
                                    </small>
                                    <small>
                                        @if (!$lanThi->co_luot_truoc)
                                            Lượt hoàn tất đầu tiên
                                        @elseif ($lanThi->chenh_lech_ty_le === null)
                                            Chưa đủ dữ liệu để so sánh với lượt trước
                                        @elseif ($lanThi->chenh_lech_ty_le > 0)
                                            Tăng {{ number_format($lanThi->chenh_lech_ty_le, 1) }} điểm phần trăm so với lượt trước
                                        @elseif ($lanThi->chenh_lech_ty_le < 0)
                                            Giảm {{ number_format(abs($lanThi->chenh_lech_ty_le), 1) }} điểm phần trăm so với lượt trước
                                        @else
                                            Bằng lượt hoàn tất trước đó
                                        @endif
                                    </small>
                                    @if ($lanThi->tongdiem !== null)
                                        <small>Điểm DET: {{ $lanThi->tongdiem }}</small>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($lanThi->trangthai === 'completed')
                                    <a class="history-link" href="{{ route('mock-test.result', ['id' => $lanThi->id]) }}">
                                        Xem kết quả
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="history-empty" colspan="5">Bạn chưa có lượt thi thử nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="history-pagination">{{ $lanThis->links() }}</div>
    </section>
@endsection
