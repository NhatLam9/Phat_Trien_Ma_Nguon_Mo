@extends('layouts.app')

@section('title', 'Lịch sử thi thử - DET Practice')
@section('main_class', 'app-main--mock-test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
    <style>
        .history-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 56px;
        }

        .history-heading {
            margin-bottom: 24px;
        }

        .history-heading h1 {
            margin: 6px 0 8px;
        }

        .history-heading p,
        .history-note {
            color: #667085;
        }

        .history-note {
            padding: 12px 16px;
            margin: 18px 0;
            border-radius: 12px;
            background: #f3f8ed;
        }

        .history-table-wrap {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
        }

        .history-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .history-table th,
        .history-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eef0f2;
            text-align: left;
            vertical-align: middle;
        }

        .history-table th {
            color: #475467;
            font-size: .85rem;
            background: #f9fafb;
        }

        .history-table tr:last-child td {
            border-bottom: 0;
        }

        .history-status {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            background: #f2f4f7;
            font-size: .82rem;
            white-space: nowrap;
        }

        .history-status--completed {
            color: #18794e;
            background: #eaf7ef;
        }

        .history-status--abandoned {
            color: #9a3412;
            background: #fff1e8;
        }

        .history-score small {
            display: block;
            margin-top: 3px;
            color: #667085;
        }

        .history-empty {
            padding: 28px;
            color: #667085;
            text-align: center;
        }

        .history-pagination {
            margin-top: 20px;
        }

        .history-link {
            color: #167a36;
            font-weight: 700;
            text-decoration: none;
        }

        .history-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <section class="history-page">
        <header class="history-heading">
            <span class="eyebrow">LỊCH SỬ THI THỬ</span>
            <h1>Lịch sử thi thử</h1>
            <p>Xem lại trạng thái, điểm bài thi và các lượt thi trước đây.</p>
        </header>

        <p class="history-note">
            Điểm thi thử được quy đổi theo công thức của dự án trên thang 10–160.
            Điểm có thể thay đổi khi các câu chờ chấm được cập nhật.
        </p>

        <div class="history-table-wrap">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Đề thi</th>
                        <th>Thời điểm</th>
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
                            <td>
                                {{ $lanThi->ten_de_thi ?: 'Đề thi #' . $lanThi->dethi_id }}
                            </td>

                            <td>
                                {{ $thoiDiem
                                    ? \Illuminate\Support\Carbon::parse($thoiDiem)->format('d/m/Y H:i')
                                    : '—' }}
                            </td>

                            <td>
                                <span class="history-status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="history-score">
                                @if ($lanThi->trangthai === 'completed')
                                    <strong>
                                        {{ $lanThi->tongdiem !== null
                                            ? $lanThi->tongdiem . ' / 160'
                                            : 'Chưa có đủ điểm' }}
                                    </strong>

                                    <small>
                                        Literacy: {{ $lanThi->literacy ?? '—' }} ·
                                        Comprehension: {{ $lanThi->comprehension ?? '—' }}
                                    </small>

                                    <small>
                                        Conversation: {{ $lanThi->conversation ?? '—' }} ·
                                        Production: {{ $lanThi->production ?? '—' }}
                                    </small>

                                    <small>
                                        @if ($lanThi->luot_truoc_id === null)
                                            Chưa có lượt hoàn tất trước đó
                                        @elseif ($lanThi->tongdiem === null || $lanThi->diem_luot_truoc === null)
                                            Chưa đủ điểm để so sánh với lượt trước
                                        @else
                                            @php
                                                $chenhLech = (int) $lanThi->tongdiem
                                                    - (int) $lanThi->diem_luot_truoc;
                                            @endphp

                                            @if ($chenhLech > 0)
                                                Tăng {{ $chenhLech }} điểm so với lượt trước
                                            @elseif ($chenhLech < 0)
                                                Giảm {{ abs($chenhLech) }} điểm so với lượt trước
                                            @else
                                                Bằng điểm lượt hoàn tất trước đó
                                            @endif
                                        @endif
                                    </small>
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if ($lanThi->trangthai === 'completed')
                                    <a class="history-link"
                                       href="{{ route('mock-test.result', ['id' => $lanThi->id]) }}">
                                        Xem kết quả
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="history-empty" colspan="5">
                                Bạn chưa có lượt thi thử nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

 @if ($lanThis->hasPages())
    @php
        $currentPage = $lanThis->currentPage();
        $lastPage = $lanThis->lastPage();

        $pageNumbers = collect([
            1,
            $currentPage - 2,
            $currentPage - 1,
            $currentPage,
            $currentPage + 1,
            $currentPage + 2,
            $lastPage,
        ])
            ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
            ->unique()
            ->sort()
            ->values();

        $previousNumber = null;

        $pageUrl = fn ($page) => $lanThis->appends(
            request()->except('page')
        )->url($page);
    @endphp

    <nav class="history-pagination bento-pagination"
         aria-label="Phân trang lịch sử thi thử">

        <div class="bp-summary" aria-live="polite">
            <strong>Trang {{ $currentPage }} / {{ $lastPage }}</strong>
            <span>
                Hiển thị {{ $lanThis->firstItem() }}–{{ $lanThis->lastItem() }}
                trong {{ $lanThis->total() }} lượt thi
            </span>
        </div>

        <div class="bp-controls">
            @if ($lanThis->onFirstPage())
                <span class="bp-button is-disabled" aria-disabled="true">
                    Trước
                </span>
            @else
                <a class="bp-button"
                   href="{{ $pageUrl($currentPage - 1) }}"
                   rel="prev">
                    Trước
                </a>
            @endif

            @foreach ($pageNumbers as $page)
                @if ($previousNumber !== null && $page - $previousNumber > 1)
                    <span class="bp-gap" aria-hidden="true">…</span>
                @endif

                @if ($page === $currentPage)
                    <span class="bp-button bp-number is-current"
                          aria-current="page"
                          aria-label="Trang {{ $page }}">
                        {{ $page }}
                    </span>
                @else
                    <a class="bp-button bp-number"
                       href="{{ $pageUrl($page) }}"
                       aria-label="Đến trang {{ $page }}">
                        {{ $page }}
                    </a>
                @endif

                @php $previousNumber = $page; @endphp
            @endforeach

            @if ($lanThis->hasMorePages())
                <a class="bp-button"
                   href="{{ $pageUrl($currentPage + 1) }}"
                   rel="next">
                    Sau
                </a>
            @else
                <span class="bp-button is-disabled" aria-disabled="true">
                    Sau
                </span>
            @endif
        </div>
    </nav>
@endif
    </section>
@endsection