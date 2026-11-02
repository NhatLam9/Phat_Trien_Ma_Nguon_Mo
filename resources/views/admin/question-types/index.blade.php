@extends('admin.layout')

@section('title', 'Dạng bài')
@section('heading', 'Quản lý dạng bài')

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách dạng bài</h2>
                <p class="admin-muted">
                    Dạng bài là dữ liệu nền cho import JSON: file import tham chiếu tới cột
                    <code>ma</code>. Mã không sửa được; chỉ sửa tên hiển thị, cách chấm,
                    thời gian mặc định và bật/tắt.
                </p>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên hiển thị</th>
                        <th>Kỹ năng</th>
                        <th>Cách chấm</th>
                        <th>Thời gian (giây)</th>
                        <th>Số câu</th>
                        <th>Hoạt động</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loais as $loai)
                        <tr>
                            <td><code>{{ $loai->ma }}</code></td>
                            <td>
                                <input type="text"
                                       form="loai-{{ $loai->id }}"
                                       name="ten"
                                       value="{{ $loai->ten }}"
                                       maxlength="100"
                                       required
                                       aria-label="Tên dạng bài {{ $loai->ma }}">
                            </td>
                            <td>{{ ucfirst($loai->kynang) }}</td>
                            <td>
                                <select form="loai-{{ $loai->id }}"
                                        name="cachcham"
                                        aria-label="Cách chấm {{ $loai->ma }}">
                                    @foreach (['auto' => 'Tự động', 'ai' => 'AI', 'manual' => 'Thủ công'] as $ma => $nhan)
                                        <option value="{{ $ma }}" @selected($loai->cachcham === $ma)>{{ $nhan }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number"
                                       form="loai-{{ $loai->id }}"
                                       name="thoigian"
                                       value="{{ $loai->thoigian }}"
                                       min="1"
                                       max="7200"
                                       class="admin-input--narrow"
                                       aria-label="Thời gian mặc định {{ $loai->ma }}">
                            </td>
                            <td>{{ $loai->cauhoi_count }}</td>
                            <td>
                                <input type="checkbox"
                                       form="loai-{{ $loai->id }}"
                                       name="is_active"
                                       value="1"
                                       @checked($loai->is_active)
                                       aria-label="Bật dạng bài {{ $loai->ma }}">
                            </td>
                            <td>
                                <button type="submit"
                                        form="loai-{{ $loai->id }}"
                                        class="admin-btn admin-btn--small">
                                    Lưu
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach ($loais as $loai)
            <form id="loai-{{ $loai->id }}"
                  method="POST"
                  action="{{ route('admin.question-types.update', $loai) }}">
                @csrf
                @method('PATCH')
            </form>
        @endforeach
    </div>
@endsection
