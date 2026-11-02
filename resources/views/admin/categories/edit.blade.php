@extends('admin.layout')

@section('title', 'Sửa chủ đề')
@section('heading', 'Sửa chủ đề')

@section('content')
    <p class="admin-breadcrumb">
        <a href="{{ route('admin.categories.index') }}">← Chủ đề</a>
        <span>/</span>
        <span>{{ $danhmuc->ten }}</span>
    </p>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>{{ $danhmuc->ten }}</h2>
                <p class="admin-muted">
                    Chủ đề đang được dùng cho {{ $soCauHoi }} câu hỏi.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.categories.update', $danhmuc) }}" class="admin-form">
            @csrf
            @method('PATCH')

            <div class="admin-form__row">
                <label for="ten">Tên chủ đề</label>
                <input type="text" id="ten" name="ten"
                       value="{{ old('ten', $danhmuc->ten) }}"
                       maxlength="100" required>
            </div>

            <div class="admin-form__row">
                <label for="mota">Mô tả</label>
                <textarea id="mota" name="mota" rows="4" maxlength="1000">{{ old('mota', $danhmuc->mota) }}</textarea>
            </div>

            <div class="admin-form__actions">
                <button type="submit" class="admin-btn">Lưu thay đổi</button>
                <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--ghost">Hủy</a>
            </div>
        </form>
    </div>

    @if ($soCauHoi === 0)
        <div class="admin-panel">
            <h2>Xóa chủ đề</h2>
            <p class="admin-muted">
                Chủ đề chưa có câu hỏi nên có thể xóa vĩnh viễn.
            </p>

            <form method="POST" action="{{ route('admin.categories.destroy', $danhmuc) }}" class="admin-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-btn admin-btn--danger">Xóa chủ đề</button>
            </form>
        </div>
    @endif
@endsection
