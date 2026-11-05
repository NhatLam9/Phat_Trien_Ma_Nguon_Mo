@extends('admin.layout')

@section('title', 'Nhập câu hỏi')
@section('heading', 'Nhập câu hỏi từ JSON')

@section('content')
    @include('admin.questions.partials.tabs')

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
                <a href="{{ asset('downloads/import-cau-hoi-mau.json') }}"
                   class="admin-btn admin-btn--ghost"
                   download>
                    Tải file JSON kiểm thử (8 dạng)
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
            <li>Mặc định câu hỏi và đề được nhập ở trạng thái <code>draft</code> để duyệt trước khi publish.</li>
            <li><code>ma</code> là khóa ổn định: nhập lại cùng <code>ma</code> sẽ cập nhật câu hỏi cũ thay vì tạo trùng.</li>
        </ul>
    </div>

    <div class="admin-panel">
        <h2>Lịch sử nhập gần đây</h2>

        @if ($batches->isEmpty())
            <p class="admin-muted">Chưa có lần nhập nào.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thời điểm</th>
                            <th>File</th>
                            <th>Người nhập</th>
                            <th>Câu trong file</th>
                            <th>Tạo mới</th>
                            <th>Cập nhật</th>
                            <th>Cảnh báo</th>
                            <th>Kết quả</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td>{{ $batch->id }}</td>
                                <td>{{ $batch->ngaytao?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td><code>{{ $batch->ten_file }}</code></td>
                                <td>{{ $batch->nguoiTao?->hoten ?? '—' }}</td>
                                <td>{{ $batch->tong_cau }}</td>
                                <td>{{ $batch->tao_moi }}</td>
                                <td>{{ $batch->cap_nhat }}</td>
                                <td>{{ $batch->so_canhbao }}</td>
                                <td>
                                    @if ($batch->laThanhCong())
                                        <span class="admin-badge admin-badge--published">Thành công</span>
                                    @else
                                        <span class="admin-badge admin-badge--review" title="{{ $batch->ghichu }}">
                                            Thất bại
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
