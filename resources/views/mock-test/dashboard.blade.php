@extends('layouts.app')

@section('title', 'Thống kê học tập - DET Practice')
@section('main_class', 'app-main--mock-test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mock-test.css') }}">
    <style>
        .study-dashboard { max-width: 1120px; margin: 0 auto; padding: 32px 20px 56px; }
        .study-dashboard__heading { margin-bottom: 24px; }
        .study-dashboard__heading h1 { margin: 6px 0 8px; }
        .study-dashboard__heading p, .study-muted { color: #667085; }
        .study-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 22px 0; }
        .study-card, .study-panel { border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; }
        .study-card { padding: 18px; }
        .study-card span { display: block; color: #667085; font-size: .9rem; }
        .study-card strong { display: block; margin-top: 8px; font-size: 1.6rem; color: #344054; }
        .study-dashboard__columns { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(260px, 1fr); gap: 18px; margin-top: 18px; }
        .study-panel { padding: 20px; }
        .study-panel h2 { margin: 0 0 16px; font-size: 1.15rem; }
        .study-table-wrap { overflow-x: auto; }
        .study-table { width: 100%; border-collapse: collapse; min-width: 480px; }
        .study-table th, .study-table td { padding: 11px 10px; border-bottom: 1px solid #eef0f2; text-align: left; }
        .study-table th { color: #667085; font-size: .85rem; }
        .study-table tr:last-child td { border-bottom: 0; }
        .study-progress { height: 7px; min-width: 80px; margin-top: 5px; overflow: hidden; border-radius: 99px; background: #e9ecef; }
        .study-progress span { display: block; height: 100%; border-radius: inherit; background: #58cc02; }
        .study-list { margin: 0; padding-left: 20px; }
        .study-list li { margin: 0 0 12px; }
        .study-link { color: #167a36; font-weight: 700; text-decoration: none; }
        .study-link:hover { text-decoration: underline; }
        .study-chart { width: 100%; height: auto; }
        .study-chart text { fill: #667085; font: 11px sans-serif; }
        .study-empty { padding: 18px 0; color: #667085; }
        .study-links { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
        @media (max-width: 800px) {
            .study-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .study-dashboard__columns { grid-template-columns: 1fr; }
        }
        @media (max-width: 460px) {
            .study-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <main class="study-dashboard">
        <header class="study-dashboard__heading">
            <span class="eyebrow">DASHBOARD · DB01–DB05</span>
            <h1>Thống kê học tập</h1>
            <p>Theo dõi thời gian luyện tập, tiến độ từng dạng bài và kết quả thi thử.</p>
        </header>

        <section class="study-grid" aria-label="Tổng quan học tập">
            <article class="study-card">
                <span>Câu đã luyện</span>
                <strong>{{ number_format($tongCauLuyen) }}</strong>
            </article>
            <article class="study-card">
                <span>Tỷ lệ đúng</span>
                <strong>{{ $tyLeDung === null ? '—' : number_format($tyLeDung, 1) . '%' }}</strong>
                <span>{{ number_format($tongCauDung) }} đúng trên {{ number_format($tongCauDaCham) }} câu đã chấm</span>
            </article>
            <article class="study-card">
                <span>Thời gian luyện tập</span>
                <strong>{{ intdiv($tongThoiGian, 60) }} phút</strong>
                <span>{{ $tongThoiGian }} giây tích lũy</span>
            </article>
            <article class="study-card">
                <span>Chuỗi ngày học</span>
                <strong>{{ $chuoiHienTai }} ngày</strong>
                <span>Dài nhất: {{ $chuoiDaiNhat }} ngày</span>
            </article>
        </section>

        <div class="study-dashboard__columns">
            <section class="study-panel">
                <h2>Tiến độ theo dạng bài</h2>
                @if ($tienDoTheoDang->isEmpty())
                    <p class="study-empty">Chưa có dữ liệu luyện tập để thống kê.</p>
                @else
                    <div class="study-table-wrap">
                        <table class="study-table">
                            <thead>
                                <tr>
                                    <th>Dạng bài</th>
                                    <th>Đã luyện</th>
                                    <th>Đúng / đã chấm</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tienDoTheoDang as $dangBai)
                                    <tr>
                                        <td>{{ $dangBai->ten }}</td>
                                        <td>{{ number_format($dangBai->tongso) }}</td>
                                        <td>{{ number_format($dangBai->sodung) }} / {{ number_format($dangBai->dacham) }}</td>
                                        <td>
                                            {{ $dangBai->ty_le_dung === null ? '—' : number_format($dangBai->ty_le_dung, 1) . '%' }}
                                            @if ($dangBai->ty_le_dung !== null)
                                                <div class="study-progress" aria-hidden="true">
                                                    <span style="width: {{ min(100, max(0, $dangBai->ty_le_dung)) }}%"></span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="study-panel">
                <h2>Dạng bài nên củng cố</h2>
                @if ($goiYLuyenTap->isEmpty())
                    <p class="study-empty">Chưa có dạng bài nào dưới 70% hoặc chưa đủ dữ liệu để gợi ý.</p>
                @else
                    <ul class="study-list">
                        @foreach ($goiYLuyenTap as $dangBai)
                            <li>
                                <strong>{{ $dangBai->ten }}</strong><br>
                                <span class="study-muted">{{ number_format($dangBai->ty_le_dung, 1) }}% đúng · {{ $dangBai->dacham }} câu đã chấm</span>
                            </li>
                        @endforeach
                    </ul>
                    <a class="study-link" href="{{ route('practice.index') }}">Mở khu luyện tập →</a>
                @endif
            </section>
        </div>

        <section class="study-panel" style="margin-top: 18px">
            <h2>Tỷ lệ kết quả thi thử</h2>
            <p class="study-muted">Điểm thô trên đề thi; chưa quy đổi sang thang DET 10–160. Các lượt được xếp theo thời gian.</p>
            @if (empty($chartDots))
                <p class="study-empty">Chưa có lượt thi hoàn tất đủ dữ liệu chấm để vẽ biểu đồ.</p>
            @else
                <svg class="study-chart" viewBox="0 0 600 240" role="img" aria-label="Biểu đồ tỷ lệ điểm thô của các lượt thi thử gần nhất">
                    <line x1="50" y1="50" x2="550" y2="50" stroke="#e5e7eb" />
                    <line x1="50" y1="85" x2="550" y2="85" stroke="#eef0f2" />
                    <line x1="50" y1="120" x2="550" y2="120" stroke="#eef0f2" />
                    <line x1="50" y1="155" x2="550" y2="155" stroke="#eef0f2" />
                    <line x1="50" y1="190" x2="550" y2="190" stroke="#e5e7eb" />
                    <text x="8" y="54">100%</text>
                    <text x="15" y="89">75%</text>
                    <text x="15" y="124">50%</text>
                    <text x="15" y="159">25%</text>
                    <text x="24" y="194">0%</text>
                    @if (count($chartDots) > 1)
                        <polyline points="{{ $chartPolyline }}" fill="none" stroke="#58cc02" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    @endif
                    @foreach ($chartDots as $dot)
                        <circle cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="5" fill="#58cc02">
                            <title>Lượt #{{ $dot['id'] }} · {{ $dot['ngay'] }} · {{ number_format($dot['ty_le'], 1) }}%</title>
                        </circle>
                        <text x="{{ $dot['x'] }}" y="218" text-anchor="middle">#{{ $loop->iteration }}</text>
                    @endforeach
                </svg>
            @endif
        </section>

        <nav class="study-links" aria-label="Điều hướng Mock Test">
            <a class="study-link" href="{{ route('mock-test.index') }}">Danh sách đề thi →</a>
            <a class="study-link" href="{{ route('mock-test.history') }}">Lịch sử thi →</a>
        </nav>
    </main>
@endsection
