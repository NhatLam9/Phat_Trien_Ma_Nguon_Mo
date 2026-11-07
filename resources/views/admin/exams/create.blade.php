@extends('admin.layout')

@section('title', 'Tạo đề thi')
@section('heading', 'Tạo đề thi')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <p class="admin-muted" style="margin-bottom: 0.35rem;">
                    <a href="{{ route('admin.exams.index') }}">← Danh sách đề thi</a>
                </p>
                <h2>Tạo đề thi mới</h2>
                <p class="admin-muted">
                    Khai báo thông tin chung trước. Sau khi tạo, hệ thống chuyển sang trang
                    chỉnh sửa để bạn chọn câu hỏi từ ngân hàng và sắp xếp thứ tự.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.exams.store') }}" class="admin-form">
            @csrf

            <div class="admin-form__row">
                <label for="ten">Tên đề thi <span style="color: #b91c1c;">*</span></label>
                <input type="text" id="ten" name="ten" value="{{ old('ten') }}"
                       maxlength="150" required placeholder="Ví dụ: Đề luyện Reading - Cấp độ 5">
            </div>

            <div class="admin-form__row">
                <label for="mota">Mô tả</label>
                <textarea id="mota" name="mota" rows="3" maxlength="2000"
                          placeholder="Mục tiêu, phạm vi hoặc ghi chú cho đề thi (không bắt buộc)">{{ old('mota') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0 18px;">
                <div class="admin-form__row">
                    <label for="chedo">Chế độ thi <span style="color: #b91c1c;">*</span></label>
                    <select id="chedo" name="chedo" required>
                        <option value="full" @selected(old('chedo', 'full') === 'full')>Toàn bài (nhiều kỹ năng)</option>
                        <option value="section" @selected(old('chedo') === 'section')>Theo từng kỹ năng</option>
                    </select>
                </div>

                <div class="admin-form__row">
                    <label for="kynang">Kỹ năng</label>
                    <select id="kynang" name="kynang">
                        <option value="">Tổng hợp / không giới hạn</option>
                        @foreach (['reading' => 'Reading', 'listening' => 'Listening', 'writing' => 'Writing', 'speaking' => 'Speaking'] as $ma => $nhan)
                            <option value="{{ $ma }}" @selected(old('kynang') === $ma)>{{ $nhan }}</option>
                        @endforeach
                    </select>
                    <span class="admin-help">Bắt buộc khi chọn chế độ “Theo từng kỹ năng”.</span>
                </div>

                <div class="admin-form__row">
                    <label for="kieu">Kiểu đề <span style="color: #b91c1c;">*</span></label>
                    <select id="kieu" name="kieu" required>
                        <option value="fixed" @selected(old('kieu', 'fixed') === 'fixed')>Cố định</option>
                        <option value="adaptive" @selected(old('kieu') === 'adaptive')>Thích ứng</option>
                    </select>
                </div>

                <div class="admin-form__row">
                    <label for="thoigian_phut">Thời gian làm bài (phút) <span style="color: #b91c1c;">*</span></label>
                    <input type="number" id="thoigian_phut" name="thoigian_phut"
                           value="{{ old('thoigian_phut', 60) }}" min="1" max="600" required>
                </div>

                <div class="admin-form__row">
                    <label for="trangthai">Trạng thái <span style="color: #b91c1c;">*</span></label>
                    <select id="trangthai" name="trangthai" required>
                        <option value="draft" @selected(old('trangthai', 'draft') === 'draft')>Bản nháp</option>
                        <option value="published" @selected(old('trangthai') === 'published')>Đã xuất bản</option>
                        <option value="hidden" @selected(old('trangthai') === 'hidden')>Đã ẩn</option>
                    </select>
                </div>
            </div>

            <div class="admin-form__actions">
                <button type="submit" class="admin-btn">Tạo đề và thêm câu hỏi</button>
                <a href="{{ route('admin.exams.index') }}" class="admin-btn admin-btn--ghost">Hủy</a>
            </div>
        </form>
    </div>
@endsection
