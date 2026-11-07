@extends('admin.layout')

@section('title', 'Chỉnh sửa đề thi')
@section('heading', 'Chỉnh sửa đề thi')

@php
    $nhanTrangThai = [
        'draft' => 'Bản nháp',
        'published' => 'Đã xuất bản',
        'hidden' => 'Đã ẩn',
    ];
    $nhanKyNang = [
        'reading' => 'Reading',
        'listening' => 'Listening',
        'writing' => 'Writing',
        'speaking' => 'Speaking',
    ];
    $tongDiem = (float) $dethi->dethicauhoi->sum('diem');
@endphp

@section('content')
    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <p class="admin-muted" style="margin-bottom: 0.35rem;">
                    <a href="{{ route('admin.exams.index') }}">← Danh sách đề thi</a>
                    · <a href="{{ route('admin.exams.show', $dethi) }}">Xem chi tiết</a>
                </p>
                <h2>{{ $dethi->ten }}</h2>
                <p class="admin-muted">
                    Mã đề #{{ $dethi->id }} · {{ $dethi->dethicauhoi->count() }} câu hỏi ·
                    tổng {{ rtrim(rtrim(number_format($tongDiem, 2, ',', '.'), '0'), ',') }} điểm
                </p>
            </div>
            <span class="admin-badge admin-badge--{{ $dethi->trangthai === 'published' ? 'published' : ($dethi->trangthai === 'hidden' ? 'archived' : 'draft') }}">
                {{ $nhanTrangThai[$dethi->trangthai] ?? $dethi->trangthai }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.exams.update', $dethi) }}" class="admin-form">
            @csrf
            @method('PATCH')

            <div class="admin-form__row">
                <label for="ten">Tên đề thi <span style="color: #b91c1c;">*</span></label>
                <input type="text" id="ten" name="ten" value="{{ old('ten', $dethi->ten) }}"
                       maxlength="150" required>
            </div>

            <div class="admin-form__row">
                <label for="mota">Mô tả</label>
                <textarea id="mota" name="mota" rows="3" maxlength="2000">{{ old('mota', $dethi->mota) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0 18px;">
                <div class="admin-form__row">
                    <label for="chedo">Chế độ thi <span style="color: #b91c1c;">*</span></label>
                    <select id="chedo" name="chedo" required>
                        <option value="full" @selected(old('chedo', $dethi->chedo) === 'full')>Toàn bài (nhiều kỹ năng)</option>
                        <option value="section" @selected(old('chedo', $dethi->chedo) === 'section')>Theo từng kỹ năng</option>
                    </select>
                </div>

                <div class="admin-form__row">
                    <label for="kynang">Kỹ năng</label>
                    <select id="kynang" name="kynang">
                        <option value="">Tổng hợp / không giới hạn</option>
                        @foreach ($nhanKyNang as $ma => $nhan)
                            <option value="{{ $ma }}" @selected(old('kynang', $dethi->kynang) === $ma)>{{ $nhan }}</option>
                        @endforeach
                    </select>
                    <span class="admin-help">Bắt buộc khi chọn chế độ “Theo từng kỹ năng”.</span>
                </div>

                <div class="admin-form__row">
                    <label for="kieu">Kiểu đề <span style="color: #b91c1c;">*</span></label>
                    <select id="kieu" name="kieu" required>
                        <option value="fixed" @selected(old('kieu', $dethi->kieu) === 'fixed')>Cố định</option>
                        <option value="adaptive" @selected(old('kieu', $dethi->kieu) === 'adaptive')>Thích ứng</option>
                    </select>
                </div>

                <div class="admin-form__row">
                    <label for="thoigian_phut">Thời gian làm bài (phút) <span style="color: #b91c1c;">*</span></label>
                    <input type="number" id="thoigian_phut" name="thoigian_phut"
                           value="{{ old('thoigian_phut', max(1, (int) round($dethi->thoigian / 60))) }}"
                           min="1" max="600" required>
                </div>

                <div class="admin-form__row">
                    <label for="trangthai">Trạng thái <span style="color: #b91c1c;">*</span></label>
                    <select id="trangthai" name="trangthai" required>
                        @foreach ($nhanTrangThai as $ma => $nhan)
                            <option value="{{ $ma }}" @selected(old('trangthai', $dethi->trangthai) === $ma)>{{ $nhan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form__actions">
                <button type="submit" class="admin-btn">Lưu thông tin đề</button>
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Cấu trúc đề hiện tại</h2>
                <p class="admin-muted">
                    Điểm từng câu lấy theo điểm của câu hỏi trong ngân hàng. Tổng điểm tự tính lại
                    sau khi thêm hoặc bớt câu.
                </p>
            </div>
        </div>

        @if ($dethi->dethicauhoi->isEmpty())
            <div class="admin-empty">
                <p>Đề thi chưa có câu hỏi nào. Hãy chọn câu hỏi ở phần bên dưới.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thứ tự</th>
                            <th>Mã câu</th>
                            <th>Nội dung</th>
                            <th>Dạng bài / Kỹ năng</th>
                            <th>Điểm</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dethi->dethicauhoi as $dong)
                            <tr>
                                <td>{{ $dong->thutu }}</td>
                                <td>
                                    @if ($dong->cauhoi)
                                        <a href="{{ route('admin.questions.show', $dong->cauhoi) }}">
                                            <code>{{ $dong->cauhoi->ma }}</code>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="admin-table__wide">
                                    {{ $dong->cauhoi ? \Illuminate\Support\Str::limit($dong->cauhoi->noidung, 110) : 'Câu hỏi đã bị xoá' }}
                                </td>
                                <td>
                                    {{ $dong->cauhoi?->loai?->ten ?? '—' }}
                                    <div class="admin-muted">{{ $dong->cauhoi?->loai?->kynang ?? '—' }}</div>
                                </td>
                                <td>{{ rtrim(rtrim(number_format((float) $dong->diem, 2, ',', '.'), '0'), ',') }}</td>
                                <td>
                                    <div class="admin-row-actions">
                                        <form method="POST"
                                              action="{{ route('admin.exams.questions.move', [$dethi, $dong->cauhoi_id]) }}"
                                              class="admin-inline-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="huong" value="up">
                                            <button type="submit" class="admin-btn admin-btn--small admin-btn--ghost"
                                                    title="Đưa câu hỏi lên trước"
                                                    @disabled($loop->first)>↑</button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.exams.questions.move', [$dethi, $dong->cauhoi_id]) }}"
                                              class="admin-inline-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="huong" value="down">
                                            <button type="submit" class="admin-btn admin-btn--small admin-btn--ghost"
                                                    title="Đưa câu hỏi xuống sau"
                                                    @disabled($loop->last)>↓</button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.exams.questions.remove', [$dethi, $dong->cauhoi_id]) }}"
                                              class="admin-inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--small">
                                                Bỏ
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

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Thêm câu hỏi từ ngân hàng</h2>
                <p class="admin-muted">
                    Chỉ hiển thị các câu chưa có trong đề. Có thể lọc theo từ khóa, kỹ năng,
                    dạng bài và chủ đề trước khi chọn.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.exams.edit', $dethi) }}" class="admin-filter">
            <div class="admin-filter__field">
                <label for="q">Từ khóa</label>
                <input type="search" id="q" name="q" value="{{ $boLoc['q'] }}"
                       placeholder="Mã, nội dung, giải thích">
            </div>

            <div class="admin-filter__field">
                <label for="kynang">Kỹ năng</label>
                <select id="kynang" name="kynang">
                    <option value="">Tất cả</option>
                    @foreach ($nhanKyNang as $ma => $nhan)
                        <option value="{{ $ma }}" @selected($boLoc['kynang'] === $ma)>{{ $nhan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="loai">Dạng bài</label>
                <select id="loai" name="loai">
                    <option value="">Tất cả</option>
                    @foreach ($loais as $loai)
                        <option value="{{ $loai->id }}" @selected((string) $boLoc['loai'] === (string) $loai->id)>
                            {{ $loai->ten }} ({{ $loai->kynang }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="danhmuc">Chủ đề</label>
                <select id="danhmuc" name="danhmuc">
                    <option value="">Tất cả</option>
                    @foreach ($danhmucs as $danhmuc)
                        <option value="{{ $danhmuc->id }}" @selected((string) $boLoc['danhmuc'] === (string) $danhmuc->id)>
                            {{ $danhmuc->ten }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__actions">
                <button type="submit" class="admin-btn">Lọc</button>
                @if ($coBoLoc)
                    <a href="{{ route('admin.exams.edit', $dethi) }}" class="admin-btn admin-btn--ghost">Xóa lọc</a>
                @endif
            </div>
        </form>

        @if ($cauhois->isEmpty())
            <div class="admin-empty">
                <p>Không còn câu hỏi nào phù hợp để thêm vào đề.</p>
                <p class="admin-muted">
                    Hãy kiểm tra bộ lọc hoặc <a href="{{ route('admin.questions.index') }}">ngân hàng câu hỏi</a>.
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('admin.exams.questions.add', $dethi) }}">
                @csrf

                <div class="admin-bulk__bar">
                    <span class="admin-muted">
                        Hiển thị {{ $cauhois->firstItem() }}–{{ $cauhois->lastItem() }}
                        trong {{ number_format($cauhois->total(), 0, ',', '.') }} câu hỏi có thể thêm.
                    </span>
                    <button type="submit" class="admin-btn admin-btn--small">Thêm câu đã chọn</button>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th class="admin-table__check"><span class="admin-visually-hidden">Chọn</span></th>
                                <th>Mã</th>
                                <th>Nội dung</th>
                                <th>Dạng bài / Kỹ năng</th>
                                <th>Chủ đề</th>
                                <th>Độ khó</th>
                                <th>Điểm</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cauhois as $cauhoi)
                                <tr>
                                    <td class="admin-table__check">
                                        <input type="checkbox" name="cauhoi_ids[]" value="{{ $cauhoi->id }}"
                                               id="chon-{{ $cauhoi->id }}"
                                               aria-label="Chọn câu hỏi {{ $cauhoi->ma }}">
                                    </td>
                                    <td><code>{{ $cauhoi->ma }}</code></td>
                                    <td class="admin-table__wide">
                                        <a href="{{ route('admin.questions.show', $cauhoi) }}">
                                            {{ \Illuminate\Support\Str::limit($cauhoi->noidung, 110) }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $cauhoi->loai?->ten ?? '—' }}
                                        <div class="admin-muted">{{ $cauhoi->loai?->kynang ?? '—' }}</div>
                                    </td>
                                    <td>{{ $cauhoi->danhmuc?->ten ?? '—' }}</td>
                                    <td>{{ $cauhoi->dokho }}</td>
                                    <td>{{ rtrim(rtrim(number_format((float) $cauhoi->diem, 2, ',', '.'), '0'), ',') }}</td>
                                    <td>
                                        <span class="admin-badge admin-badge--{{ $cauhoi->trangthai }}">
                                            {{ $cauhoi->nhanTrangThai() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="admin-form__actions" style="margin-top: 14px;">
                    <button type="submit" class="admin-btn">Thêm câu đã chọn</button>
                </div>
            </form>

            {{ $cauhois->links('admin.partials.pagination') }}
        @endif
    </div>
@endsection
