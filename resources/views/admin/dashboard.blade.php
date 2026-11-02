@extends('admin.layout')

@section('title', 'Tổng quan')
@section('heading', 'Tổng quan hệ thống')

@section('content')
    <section class="admin-grid">
        <article class="admin-card">
            <p class="admin-card__label">Người dùng</p>
            <p class="admin-card__value">{{ number_format($thongke['nguoidung'], 0, ',', '.') }}</p>
        </article>

        <article class="admin-card">
            <p class="admin-card__label">Câu hỏi</p>
            <p class="admin-card__value">{{ number_format($thongke['cauhoi'], 0, ',', '.') }}</p>
            <p class="admin-card__hint">
                Đã xuất bản: {{ number_format($thongke['cauhoi_published'], 0, ',', '.') }}
            </p>
            <p class="admin-card__hint">
                Chờ duyệt: {{ number_format($thongke['cauhoi_review'], 0, ',', '.') }} ·
                Bản nháp: {{ number_format($thongke['cauhoi_draft'], 0, ',', '.') }}
            </p>
            <p class="admin-card__hint">
                <a class="admin-link" href="{{ route('admin.questions.index') }}">Mở ngân hàng câu hỏi</a>
            </p>
        </article>

        <article class="admin-card">
            <p class="admin-card__label">Đề thi</p>
            <p class="admin-card__value">{{ number_format($thongke['dethi'], 0, ',', '.') }}</p>
        </article>

        <article class="admin-card">
            <p class="admin-card__label">Lượt thi</p>
            <p class="admin-card__value">{{ number_format($thongke['lanthi'], 0, ',', '.') }}</p>
        </article>

        <article class="admin-card">
            <p class="admin-card__label">Lượt gọi AI</p>
            <p class="admin-card__value">{{ number_format($thongke['ailog'], 0, ',', '.') }}</p>
            <p class="admin-card__hint">Hôm nay: {{ number_format($thongke['ailog_homnay'], 0, ',', '.') }}</p>
        </article>
    </section>

    <section class="admin-panel">
        <h2>Việc cần làm</h2>
        <p class="admin-muted">
            Ngân hàng câu hỏi là nơi duyệt nội dung trước khi đưa vào đề thi.
            Câu hỏi mới import luôn ở trạng thái bản nháp.
        </p>

        <div class="admin-quick">
            <a class="admin-quick__item" href="{{ route('admin.questions.index', ['trangthai' => 'review']) }}">
                <strong>{{ number_format($thongke['cauhoi_review'], 0, ',', '.') }}</strong>
                <span>Câu đang chờ duyệt</span>
            </a>
            <a class="admin-quick__item" href="{{ route('admin.questions.index', ['trangthai' => 'draft']) }}">
                <strong>{{ number_format($thongke['cauhoi_draft'], 0, ',', '.') }}</strong>
                <span>Câu còn là bản nháp</span>
            </a>
            <a class="admin-quick__item" href="{{ route('admin.questions.import.create') }}">
                <strong>JSON</strong>
                <span>Nhập câu hỏi mới</span>
            </a>
        </div>
    </section>
@endsection
