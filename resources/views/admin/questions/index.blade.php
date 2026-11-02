@extends('admin.layout')

@section('title', 'Ngân hàng câu hỏi')
@section('heading', 'Ngân hàng câu hỏi')

@section('content')
    @include('admin.questions.partials.tabs')

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Duyệt câu hỏi trước khi đưa vào đề</h2>
                <p class="admin-muted">
                    Câu hỏi được tạo từ file JSON, sau đó duyệt theo quy trình
                    Bản nháp → Chờ duyệt → Đã xuất bản → Lưu trữ.
                </p>
            </div>
            <a href="{{ route('admin.questions.import.create') }}" class="admin-btn">
                Nhập câu hỏi
            </a>
        </div>

        <div class="admin-tags">
            <a class="admin-chip {{ $boLoc['trangthai'] === '' ? 'is-active' : '' }}"
               href="{{ route('admin.questions.index', array_filter(array_merge($boLoc, ['trangthai' => '']))) }}">
                Tất cả ({{ array_sum($thongKe) }})
            </a>
            @foreach ($thongKe as $trangthai => $soLuong)
                <a class="admin-chip {{ $boLoc['trangthai'] === $trangthai ? 'is-active' : '' }}"
                   href="{{ route('admin.questions.index', array_filter(array_merge($boLoc, ['trangthai' => $trangthai]))) }}">
                    {{ \App\Models\Cauhoi::TRANGTHAI_NHAN[$trangthai] ?? $trangthai }} ({{ $soLuong }})
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.questions.index') }}" class="admin-filter">
            <div class="admin-filter__field">
                <label for="q">Từ khóa</label>
                <input type="search" id="q" name="q" value="{{ $boLoc['q'] }}"
                       placeholder="Mã, nội dung, giải thích">
            </div>

            <div class="admin-filter__field">
                <label for="loai">Loại câu hỏi</label>
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
                <label for="kynang">Kỹ năng</label>
                <select id="kynang" name="kynang">
                    <option value="">Tất cả</option>
                    @foreach (['reading' => 'Reading', 'listening' => 'Listening', 'writing' => 'Writing', 'speaking' => 'Speaking'] as $ma => $ten)
                        <option value="{{ $ma }}" @selected($boLoc['kynang'] === $ma)>{{ $ten }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field">
                <label for="trangthai">Trạng thái</label>
                <select id="trangthai" name="trangthai">
                    <option value="">Tất cả</option>
                    @foreach (\App\Models\Cauhoi::TRANGTHAI_NHAN as $ma => $ten)
                        <option value="{{ $ma }}" @selected($boLoc['trangthai'] === $ma)>{{ $ten }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filter__field admin-filter__field--narrow">
                <label for="dokho">Độ khó</label>
                <select id="dokho" name="dokho">
                    <option value="">Tất cả</option>
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" @selected((string) $boLoc['dokho'] === (string) $i)>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="admin-filter__actions">
                <button type="submit" class="admin-btn">Lọc</button>
                @if ($coBoLoc)
                    <a href="{{ route('admin.questions.index') }}" class="admin-btn admin-btn--ghost">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <h2>Danh sách câu hỏi</h2>
                <p class="admin-muted">
                    @if ($cauhois->total() === 0)
                        Không có câu hỏi nào khớp bộ lọc.
                    @else
                        Hiển thị {{ $cauhois->firstItem() }}–{{ $cauhois->lastItem() }}
                        trong {{ number_format($cauhois->total(), 0, ',', '.') }} câu hỏi.
                    @endif
                </p>
            </div>
        </div>

        @if ($cauhois->isNotEmpty())
            <form method="POST" action="{{ route('admin.questions.bulk-status') }}" class="admin-bulk">
                @csrf
                <div class="admin-bulk__bar">
                    <label for="bulk-trangthai">Chuyển trạng thái các câu đã chọn sang</label>
                    <select id="bulk-trangthai" name="trangthai">
                        @foreach (\App\Models\Cauhoi::TRANGTHAI_NHAN as $ma => $ten)
                            <option value="{{ $ma }}">{{ $ten }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="admin-btn admin-btn--small">Áp dụng</button>
                    <span class="admin-muted">Câu không đủ điều kiện sẽ bị bỏ qua và báo lại.</span>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th class="admin-table__check"><span class="admin-visually-hidden">Chọn</span></th>
                                <th>Mã</th>
                                <th>Nội dung</th>
                                <th>Loại / Kỹ năng</th>
                                <th>Độ khó</th>
                                <th>Đáp án</th>
                                <th>Câu con</th>
                                <th>Trạng thái</th>
                                <th>Cập nhật</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cauhois as $cauhoi)
                                <tr>
                                    <td class="admin-table__check">
                                        <input type="checkbox" name="ids[]" value="{{ $cauhoi->id }}"
                                               id="chon-{{ $cauhoi->id }}"
                                               aria-label="Chọn câu hỏi {{ $cauhoi->ma }}">
                                    </td>
                                    <td><code>{{ $cauhoi->ma }}</code></td>
                                    <td class="admin-table__wide">
                                        <a href="{{ route('admin.questions.show', $cauhoi) }}">
                                            {{ \Illuminate\Support\Str::limit($cauhoi->noidung, 110) }}
                                        </a>
                                        @if ($cauhoi->danhmuc)
                                            <div class="admin-muted">{{ $cauhoi->danhmuc->ten }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $cauhoi->loai?->ten ?? '—' }}
                                        <div class="admin-muted">{{ $cauhoi->loai?->kynang ?? '—' }}</div>
                                    </td>
                                    <td>{{ $cauhoi->dokho }}</td>
                                    <td>
                                        {{ $cauhoi->luachon_count }}
                                        @if ($cauhoi->luachon_count > 0)
                                            <span class="admin-muted">lựa chọn</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($cauhoi->cauhoi_doc_count > 0 || $cauhoi->cauhoi_nghe_count > 0)
                                            {{ $cauhoi->cauhoi_doc_count }} đọc /
                                            {{ $cauhoi->cauhoi_nghe_count }} nghe
                                        @else
                                            <span class="admin-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="admin-badge admin-badge--{{ $cauhoi->trangthai }}">
                                            {{ $cauhoi->nhanTrangThai() }}
                                        </span>
                                    </td>
                                    <td class="admin-muted">
                                        {{ $cauhoi->ngaycapnhat?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.questions.show', $cauhoi) }}"
                                           class="admin-link">Xem</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            {{ $cauhois->links('admin.partials.pagination') }}
        @else
            <div class="admin-empty">
                <p>Ngân hàng câu hỏi đang trống.</p>
                <p class="admin-muted">
                    Hãy <a href="{{ route('admin.questions.import.create') }}">nhập câu hỏi từ JSON</a>
                    để tạo câu hỏi đầu tiên.
                </p>
            </div>
        @endif
    </div>
@endsection
