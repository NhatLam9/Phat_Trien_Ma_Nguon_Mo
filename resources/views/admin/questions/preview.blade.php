@extends('admin.layout')

@section('title', 'Xem trước import')
@section('heading', 'Xem trước dữ liệu import')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Kiểm tra trước khi ghi database</h2>
                <p class="admin-muted">
                    File: <strong>{{ $file_name }}</strong>
                </p>
            </div>
            <a href="{{ route('admin.questions.import.create') }}" class="admin-btn admin-btn--ghost">
                Chọn file khác
            </a>
        </div>

        @if (session('error'))
            <div class="admin-alert admin-alert--error">{{ session('error') }}</div>
        @endif

        <div class="admin-stats">
            <div class="admin-stat">
                <span>Câu hỏi</span>
                <strong>{{ $summary['questions'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Lựa chọn</span>
                <strong>{{ $summary['options'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu con đọc</span>
                <strong>{{ $summary['reading_children'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu con nghe</span>
                <strong>{{ $summary['listening_children'] }}</strong>
            </div>
            <div class="admin-stat">
                <span>Câu trong đề</span>
                <strong>{{ $summary['exam_questions'] }}</strong>
            </div>
        </div>

        @if (! empty($summary['by_type']))
            <div class="admin-tags">
                @foreach ($summary['by_type'] as $type => $count)
                    <span class="admin-tag">{{ $type }}: {{ $count }}</span>
                @endforeach
            </div>
        @endif
    </div>

    @if (! empty($errors))
        <div class="admin-panel">
            <div class="admin-alert admin-alert--error">
                <strong>Không thể import vì file còn lỗi:</strong>
                <ul>
                    @foreach ($errors as $field => $messages)
                        @foreach ((array) $messages as $message)
                            <li><code>{{ $field }}</code>: {{ $message }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="admin-panel">
            <div class="admin-alert admin-alert--success">
                Dữ liệu hợp lệ. Kiểm tra lại danh sách bên dưới rồi bấm xác nhận.
            </div>

            <form method="POST" action="{{ route('admin.questions.import.store') }}" class="admin-form">
                @csrf
                <div class="admin-form__actions">
                    <button type="submit" class="admin-btn">Xác nhận import</button>
                    <a href="{{ route('admin.questions.import.create') }}" class="admin-btn admin-btn--ghost">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    @endif

    @if (! empty($warnings))
        <div class="admin-panel">
            <div class="admin-alert admin-alert--warning">
                <strong>Cảnh báo:</strong>
                <ul>
                    @foreach ($warnings as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (! empty($questions))
        <div class="admin-panel">
            <h2>Danh sách câu hỏi</h2>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã</th>
                            <th>Loại</th>
                            <th>Nội dung</th>
                            <th>Độ khó</th>
                            <th>Lựa chọn</th>
                            <th>Câu con</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $question['ma'] }}</code></td>
                                <td>{{ $question['loai_ma'] }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($question['noidung'], 160) }}</td>
                                <td>{{ $question['dokho'] }}</td>
                                <td>{{ count($question['luachon']) }}</td>
                                <td>
                                    {{ count($question['cauhoi_doc']) }} đọc /
                                    {{ count($question['cauhoi_nghe']) }} nghe
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($exam)
        <div class="admin-panel">
            <h2>Đề thi</h2>
            <dl class="admin-definition">
                <dt>Tên</dt>
                <dd>{{ $exam['ten'] }}</dd>
                <dt>Kiểu</dt>
                <dd>{{ $exam['kieu'] }}</dd>
                <dt>Chế độ</dt>
                <dd>{{ $exam['chedo'] }}</dd>
                <dt>Thời gian</dt>
                <dd>{{ $exam['thoigian'] }} giây</dd>
                <dt>Tổng điểm</dt>
                <dd>{{ $exam['tongdiem'] }}</dd>
                <dt>Số câu</dt>
                <dd>{{ count($exam['questions']) }}</dd>
            </dl>
        </div>
    @endif
@endsection
