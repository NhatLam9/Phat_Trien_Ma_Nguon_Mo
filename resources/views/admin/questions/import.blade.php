@extends('admin.layout')

@section('title', 'Import câu hỏi')
@section('heading', 'Import câu hỏi từ file JSON')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Chọn file dữ liệu</h2>
                <p class="admin-muted">
                    File JSON có thể chứa câu hỏi cha, câu hỏi con, lựa chọn và đề thi.
                    Hệ thống sẽ kiểm tra trước, sau đó mới ghi vào database.
                </p>
            </div>
        </div>

        @if (session('error'))
            <div class="admin-alert admin-alert--error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <strong>File chưa hợp lệ:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.questions.import.preview') }}"
              enctype="multipart/form-data"
              class="admin-form">
            @csrf

            <div class="admin-form__row">
                <label for="file">File JSON</label>
                <input type="file"
                       id="file"
                       name="file"
                       accept=".json,.txt,application/json"
                       required>
                <p class="admin-help">
                    Tối đa 10 MB. Ưu tiên file <code>.json</code>.
                </p>
            </div>

            <div class="admin-form__actions">
                <button type="submit" class="admin-btn">Xem trước dữ liệu</button>
                <a href="{{ route('admin.questions.import.template') }}"
                   class="admin-btn admin-btn--ghost">
                    Tải file mẫu
                </a>
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <h2>Cấu trúc tối thiểu</h2>
        <p class="admin-muted">
            Mỗi câu hỏi cần có <code>loai</code>, <code>noidung</code>.
            Các trường còn lại có thể bỏ trống và sẽ dùng giá trị mặc định.
        </p>

        <pre class="admin-code"><code>{
  "questions": [
    {
      "ma": "reading-001",
      "loai": "read_and_select",
      "noidung": "Choose the correct word: ...",
      "dokho": 3,
      "trangthai": "draft",
      "luachon": [
        {"noidung": "visit", "ladung": true},
        {"noidung": "visits", "ladung": false}
      ]
    }
  ],
  "exam": {
    "ten": "Đề import mẫu",
    "kieu": "fixed",
    "thoigian": 3600,
    "trangthai": "draft",
    "questions": ["reading-001"]
  }
}</code></pre>

        <h3>Quy tắc chính</h3>
        <ul class="admin-list">
            <li><code>loai</code> phải là mã dạng bài đã có trong bảng <code>loaicauhoi</code>.</li>
            <li>Câu trắc nghiệm cần ít nhất 2 lựa chọn và đúng 1 lựa chọn có <code>ladung: true</code>.</li>
            <li>Câu hỏi con đọc dùng <code>cauhoi_doc</code>; câu hỏi con nghe dùng <code>cauhoi_nghe</code>.</li>
            <li>Câu con không có lựa chọn phải có <code>dapandung</code>.</li>
            <li>Đường dẫn media là đường dẫn tương đối trong <code>public/</code>, ví dụ <code>images/questions/photo-2.svg</code>.</li>
            <li>Mặc định câu hỏi và đề được import ở trạng thái <code>draft</code> để duyệt trước khi publish.</li>
        </ul>
    </div>
@endsection
