@extends('admin.layout')

@section('title', 'Người dùng')
@section('heading', 'Quản lý người dùng')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Tìm kiếm và lọc</h2>
                <p class="admin-muted">
                    Khóa tài khoản để chặn đăng nhập, đổi vai trò để cấp hoặc thu quyền quản trị.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="admin-filter">
            <div class="admin-filter__field">
                <label for="q">Từ khóa</label>
                <input type="search" id="q" name="q" value="{{ $boLoc['q'] }}"
                       placeholder="Họ tên hoặc email">
            </div>

            <div class="admin-filter__field">
                <label for="vaitro">Vai trò</label>
                <select id="vaitro" name="vaitro">
                    <option value="">Tất cả</option>
                    <option value="admin" @selected($boLoc['vaitro'] === 'admin')>Quản trị</option>
                    <option value="user" @selected($boLoc['vaitro'] === 'user')>Học viên</option>
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="trangthai">Trạng thái</label>
                <select id="trangthai" name="trangthai">
                    <option value="">Tất cả</option>
                    <option value="active" @selected($boLoc['trangthai'] === 'active')>Hoạt động</option>
                    <option value="locked" @selected($boLoc['trangthai'] === 'locked')>Bị khóa</option>
                </select>
            </div>

            <div class="admin-filter__actions">
                <button type="submit" class="admin-btn">Lọc</button>
                @if ($coBoLoc)
                    <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách người dùng</h2>
                <p class="admin-muted">
                    @if ($nguoidungs->total() === 0)
                        Không có người dùng nào khớp bộ lọc.
                    @else
                        Hiển thị {{ $nguoidungs->firstItem() }}–{{ $nguoidungs->lastItem() }}
                        trong {{ number_format($nguoidungs->total(), 0, ',', '.') }} tài khoản.
                    @endif
                </p>
            </div>
        </div>

        @if ($nguoidungs->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nguoidungs as $nguoidung)
                            <tr>
                                <td>{{ $nguoidung->id }}</td>
                                <td>
                                    {{ $nguoidung->hoten }}
                                    @if ($nguoidung->is(auth()->user()))
                                        <span class="admin-badge admin-badge--review">Bạn</span>
                                    @endif
                                </td>
                                <td class="admin-table__wide">{{ $nguoidung->email }}</td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('admin.users.role', $nguoidung) }}"
                                          class="admin-inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="vaitro" aria-label="Vai trò của {{ $nguoidung->email }}">
                                            <option value="user" @selected($nguoidung->vaitro === 'user')>Học viên</option>
                                            <option value="admin" @selected($nguoidung->vaitro === 'admin')>Quản trị</option>
                                        </select>
                                        <button type="submit" class="admin-btn admin-btn--small">Đổi</button>
                                    </form>
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge--{{ $nguoidung->trangthai === 'active' ? 'published' : 'archived' }}">
                                        {{ $nguoidung->trangthai === 'active' ? 'Hoạt động' : 'Bị khóa' }}
                                    </span>
                                </td>
                                <td class="admin-muted">
                                    {{ $nguoidung->ngaytao?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('admin.users.status', $nguoidung) }}"
                                          class="admin-inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="trangthai"
                                               value="{{ $nguoidung->trangthai === 'active' ? 'locked' : 'active' }}">
                                        <button type="submit"
                                                class="admin-btn admin-btn--small {{ $nguoidung->trangthai === 'active' ? 'admin-btn--danger' : 'admin-btn--ghost' }}">
                                            {{ $nguoidung->trangthai === 'active' ? 'Khóa' : 'Mở khóa' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $nguoidungs->links('admin.partials.pagination') }}
        @endif
    </div>
@endsection
