@extends('admin.layout')

@section('title', 'Chủ đề')
@section('heading', 'Quản lý chủ đề')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Thêm chủ đề</h2>
                <p class="admin-muted">
                    Chủ đề giúp lọc câu hỏi trong ngân hàng và chọn câu khi luyện tập.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="admin-form">
            @csrf

            <div class="admin-form__row">
                <label for="ten">Tên chủ đề</label>
                <input type="text" id="ten" name="ten" value="{{ old('ten') }}"
                       maxlength="100" required placeholder="Ví dụ: Daily Life">
            </div>

            <div class="admin-form__row">
                <label for="mota">Mô tả</label>
                <textarea id="mota" name="mota" rows="3" maxlength="1000"
                          placeholder="Mô tả ngắn về chủ đề (không bắt buộc)">{{ old('mota') }}</textarea>
            </div>

            <div class="admin-form__actions">
                <button type="submit" class="admin-btn">Thêm chủ đề</button>
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách chủ đề</h2>
                <p class="admin-muted">
                    @if ($danhmucs->isEmpty())
                        Chưa có chủ đề nào.
                    @else
                        Tổng {{ number_format($danhmucs->count(), 0, ',', '.') }} chủ đề.
                    @endif
                </p>
            </div>
        </div>

        @if ($danhmucs->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tên chủ đề</th>
                            <th>Mô tả</th>
                            <th>Số câu hỏi</th>
                            <th>Cập nhật</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($danhmucs as $danhmuc)
                            <tr>
                                <td><strong>{{ $danhmuc->ten }}</strong></td>
                                <td class="admin-table__wide">
                                    {{ \Illuminate\Support\Str::limit($danhmuc->mota ?? '—', 140) }}
                                </td>
                                <td>{{ $danhmuc->cauhoi_count }}</td>
                                <td class="admin-muted">
                                    {{ $danhmuc->ngaycapnhat?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td>
                                    <div class="admin-row-actions">
                                        <a href="{{ route('admin.categories.edit', $danhmuc) }}"
                                           class="admin-link">Sửa</a>

                                        <form method="POST"
                                              action="{{ route('admin.categories.destroy', $danhmuc) }}"
                                              class="admin-inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--small">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
