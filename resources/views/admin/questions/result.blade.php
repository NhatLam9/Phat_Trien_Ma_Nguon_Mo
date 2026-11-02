@extends('admin.layout')

@section('title', 'Kết quả nhập câu hỏi')
@section('heading', 'Kết quả nhập câu hỏi')

@section('content')
    @include('admin.questions.partials.tabs')

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Nhập hoàn tất</h2>
                <p class="admin-muted">File: <strong>{{ $file_name }}</strong></p>
            </div>
            <a href="{{ route('admin.questions.import.create') }}" class="admin-btn admin-btn--ghost">
                Nhập file khác
            </a>
        </div>

        <div class="admin-stats">
            <div class="admin-stat">
                <span>Câu hỏi mới</span>
                <strong>{{ $result['stats']['questions_created'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu hỏi cập nhật</span>
                <strong>{{ $result['stats']['questions_updated'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Lựa chọn đã ghi</span>
                <strong>{{ $result['stats']['options'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu con đọc</span>
                <strong>{{ $result['stats']['reading_children'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu con nghe</span>
                <strong>{{ $result['stats']['listening_children'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Danh mục mới</span>
                <strong>{{ $result['stats']['categories_created'] }}</strong>
            </div>
        </div>
    </div>

    @if ($result['exam'])
        <div class="admin-panel">
            <h2>Đề thi đã nhập</h2>
            <dl class="admin-definition">
                <dt>ID</dt>
                <dd>{{ $result['exam']['id'] }}</dd>
                <dt>Tên</dt>
                <dd>{{ $result['exam']['ten'] }}</dd>
                <dt>Số câu</dt>
                <dd>{{ $result['exam']['questions'] }}</dd>
            </dl>
        </div>
    @endif

    <div class="admin-panel">
        <p class="admin-muted">
            Câu hỏi mặc định được nhập ở trạng thái <code>draft</code> nếu file không ghi rõ trạng thái.
            Hãy kiểm tra và publish khi dữ liệu đã sẵn sàng.
        </p>

        <div class="admin-form__actions">
            <a href="{{ route('admin.questions.index') }}" class="admin-btn">Duyệt câu hỏi vừa nhập</a>
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost">Về tổng quan</a>
        </div>
    </div>
@endsection
