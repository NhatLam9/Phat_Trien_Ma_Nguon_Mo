@extends('admin.layout')

@section('title', 'Chi tiết câu hỏi')
@section('heading', 'Chi tiết câu hỏi')

@section('content')
    <p class="admin-breadcrumb">
        <a href="{{ route('admin.questions.index') }}">← Ngân hàng câu hỏi</a>
        <span>/</span>
        <code>{{ $cauhoi->ma }}</code>
    </p>

    <div class="admin-split">
        <div>
            <div class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <h2>{{ $cauhoi->loai?->ten ?? 'Chưa rõ loại' }}</h2>
                        <p class="admin-muted">
                            Mã <code>{{ $cauhoi->ma }}</code> ·
                            {{ $cauhoi->loai?->kynang ?? '—' }} ·
                            độ khó {{ $cauhoi->dokho }}/10
                        </p>
                    </div>
                    <span class="admin-badge admin-badge--{{ $cauhoi->trangthai }}">
                        {{ $cauhoi->nhanTrangThai() }}
                    </span>
                </div>

                <dl class="admin-definition">
                    <dt>Danh mục</dt>
                    <dd>{{ $cauhoi->danhmuc?->ten ?? '—' }}</dd>
                    <dt>Thời gian</dt>
                    <dd>{{ $cauhoi->thoigian ? $cauhoi->thoigian.' giây' : '—' }}</dd>
                    <dt>Điểm</dt>
                    <dd>{{ rtrim(rtrim((string) $cauhoi->diem, '0'), '.') }}</dd>
                    <dt>Người tạo</dt>
                    <dd>{{ $cauhoi->nguoiTao?->hoten ?? '—' }}</dd>
                    <dt>Ngày tạo</dt>
                    <dd>{{ $cauhoi->ngaytao?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Cập nhật</dt>
                    <dd>{{ $cauhoi->ngaycapnhat?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>

                <h3>Nội dung câu hỏi</h3>
                <div class="admin-prose">{!! nl2br(e($cauhoi->noidung)) !!}</div>

                @if ($cauhoi->giaithich)
                    <h3>Giải thích đáp án</h3>
                    <div class="admin-prose admin-prose--muted">{!! nl2br(e($cauhoi->giaithich)) !!}</div>
                @endif

                @if ($cauhoi->anh)
                    <h3>Ảnh minh họa</h3>
                    <figure class="admin-media">
                        <img src="{{ asset($cauhoi->anh) }}" alt="Ảnh minh họa câu hỏi {{ $cauhoi->ma }}">
                        <figcaption><code>{{ $cauhoi->anh }}</code></figcaption>
                    </figure>
                @endif

                @if ($cauhoi->audio)
                    <h3>Audio</h3>
                    <div class="admin-media">
                        <audio controls preload="none" src="{{ asset($cauhoi->audio) }}"></audio>
                        <p class="admin-muted"><code>{{ $cauhoi->audio }}</code></p>
                    </div>
                @endif
            </div>

            @if ($cauhoi->luachon->isNotEmpty())
                <div class="admin-panel">
                    <h2>Lựa chọn trả lời</h2>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Thứ tự</th>
                                    <th>Nội dung</th>
                                    <th>Đáp án</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cauhoi->luachon as $luachon)
                                    <tr>
                                        <td>{{ $luachon->thutu }}</td>
                                        <td>{{ $luachon->noidung }}</td>
                                        <td>
                                            @if ($luachon->ladung)
                                                <span class="admin-badge admin-badge--published">Đúng</span>
                                            @else
                                                <span class="admin-muted">Sai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($cauhoi->cauhoiDoc->isNotEmpty())
                <div class="admin-panel">
                    <h2>Câu hỏi con (Reading)</h2>
                    @foreach ($cauhoi->cauhoiDoc as $con)
                        <div class="admin-subcard">
                            <h3>#{{ $con->thutu }} · {{ $con->loai }}</h3>
                            <div class="admin-prose">{!! nl2br(e($con->noidung)) !!}</div>

                            @if ($con->luachonDoc->isNotEmpty())
                                <ul class="admin-option-list">
                                    @foreach ($con->luachonDoc as $luaChonCon)
                                        <li class="{{ $luaChonCon->ladung ? 'is-correct' : '' }}">
                                            {{ $luaChonCon->noidung }}
                                            @if ($luaChonCon->ladung)<span class="admin-muted">(đúng)</span>@endif
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif ($con->dapandung)
                                <p class="admin-muted">Đáp án đúng: <strong>{{ $con->dapandung }}</strong></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($cauhoi->cauhoiNghe->isNotEmpty())
                <div class="admin-panel">
                    <h2>Câu hỏi con (Listening)</h2>
                    @foreach ($cauhoi->cauhoiNghe as $con)
                        <div class="admin-subcard">
                            <h3>#{{ $con->thutu }} · {{ $con->loai }}</h3>
                            <div class="admin-prose">{!! nl2br(e($con->noidung)) !!}</div>

                            @if ($con->luachonNghe->isNotEmpty())
                                <ul class="admin-option-list">
                                    @foreach ($con->luachonNghe as $luaChonCon)
                                        <li class="{{ $luaChonCon->ladung ? 'is-correct' : '' }}">
                                            {{ $luaChonCon->noidung }}
                                            @if ($luaChonCon->ladung)<span class="admin-muted">(đúng)</span>@endif
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif ($con->dapandung)
                                <p class="admin-muted">Đáp án đúng: <strong>{{ $con->dapandung }}</strong></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <aside>
            <div class="admin-panel">
                <h2>Duyệt câu hỏi</h2>

                @if ($loiXuatBan !== [] && $cauhoi->trangthai !== \App\Models\Cauhoi::TRANGTHAI_PUBLISHED)
                    <div class="admin-alert admin-alert--warning">
                        <strong>Chưa thể xuất bản:</strong>
                        <ul>
                            @foreach ($loiXuatBan as $loi)
                                <li>{{ $loi }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($trangThaiChoPhep === [])
                    <p class="admin-muted">Không có bước chuyển trạng thái nào từ trạng thái hiện tại.</p>
                @else
                    <form method="POST" action="{{ route('admin.questions.status', $cauhoi) }}" class="admin-form">
                        @csrf
                        @method('PATCH')

                        <div class="admin-form__row">
                            <label for="trangthai">Chuyển sang</label>
                            <select id="trangthai" name="trangthai">
                                @foreach ($trangThaiChoPhep as $trangthai)
                                    <option value="{{ $trangthai }}">
                                        {{ \App\Models\Cauhoi::TRANGTHAI_NHAN[$trangthai] ?? $trangthai }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="admin-form__actions">
                            <button type="submit" class="admin-btn">Cập nhật trạng thái</button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="admin-panel">
                <h2>Đề thi đang dùng</h2>

                @if ($cauhoi->dethicauhoi->isEmpty())
                    <p class="admin-muted">Câu hỏi chưa nằm trong đề thi nào.</p>
                @else
                    <ul class="admin-list">
                        @foreach ($cauhoi->dethicauhoi as $chiTiet)
                            <li>
                                {{ $chiTiet->dethi?->ten ?? 'Đề đã xóa' }}
                                <span class="admin-muted">
                                    (câu {{ $chiTiet->thutu }} · {{ $chiTiet->dethi?->trangthai }})
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if (in_array($cauhoi->trangthai, [\App\Models\Cauhoi::TRANGTHAI_DRAFT, \App\Models\Cauhoi::TRANGTHAI_ARCHIVED], true))
                <div class="admin-panel">
                    <h2>Xóa câu hỏi</h2>
                    <p class="admin-muted">
                        Chỉ xóa được câu hỏi nháp/lưu trữ và chưa nằm trong đề thi.
                    </p>

                    <form method="POST" action="{{ route('admin.questions.destroy', $cauhoi) }}" class="admin-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="admin-btn admin-btn--danger">Xóa vĩnh viễn</button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
@endsection
