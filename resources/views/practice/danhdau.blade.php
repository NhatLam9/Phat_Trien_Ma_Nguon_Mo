@extends('layouts.app')

@section('title', 'Câu hỏi đã lưu & Dạng bài quan tâm - DET Practice')

@push('styles')
<style>
    .bookmark-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 0 50px;
    }
    .skill-tab-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        font-weight: 700;
        font-size: 14px;
        color: #64748b;
        text-decoration: none;
        border-bottom: 2.5px solid transparent;
        transition: all 0.2s ease;
    }
    .skill-tab-item:hover {
        color: #00b86b;
    }
    .skill-tab-item.active {
        color: #00b86b;
        border-bottom-color: #00b86b;
        background: #f0fdf4;
        border-radius: 8px 8px 0 0;
    }
    .stat-badge-chip {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 10px;
        background: #e2e8f0;
        color: #475569;
        font-weight: 800;
    }
    .skill-tab-item.active .stat-badge-chip {
        background: #00b86b;
        color: #ffffff;
    }
    .view-mode-tab {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        color: #475569;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .view-mode-tab:hover {
        border-color: #00b86b;
        color: #00b86b;
    }
    .view-mode-tab.active {
        background: #00b86b;
        color: #ffffff;
        border-color: #00b86b;
        box-shadow: 0 2px 6px rgba(0, 184, 107, 0.25);
    }
    .bm-row {
        transition: background-color 0.2s ease;
    }
    .bm-row:hover {
        background-color: #f8fafc;
    }
    .toast-msg {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #1e293b;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        display: none;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="bookmark-container">

    {{-- HEADER TRANG --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span style="font-size: 26px;">⭐</span>
                <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin: 0;">
                    Câu hỏi đã lưu & Dạng bài quan tâm
                </h1>
            </div>
            <p style="color: #64748b; font-size: 14px; margin: 0;">
                Tập hợp các câu hỏi bạn đánh dấu xem lại và các dạng bài cần ưu tiên luyện tập để nâng cao band điểm Duolingo English Test (DET).
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if($stats['total'] > 0)
                <a href="{{ route('practice.bookmarks.practice', ['skill' => $selectedSkill]) }}" class="btn btn-primary" style="font-weight: 700;">
                     Luyện tập các câu đã lưu
                </a>
            @endif
            <a href="{{ route('practice.select') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                Danh sách dạng bài
            </a>
        </div>
    </div>

    {{-- 4 THẺ THỐNG KÊ KỸ NĂNG DET --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 24px;">
        @foreach($skillMeta as $sKey => $sMeta)
            @php $count = $stats[$sKey] ?? 0; @endphp
            <div class="card" style="margin-bottom: 0; padding: 16px 18px; border-radius: 12px; border: 1.5px solid {{ $sMeta['border'] }}; background: {{ $sMeta['bg'] }};">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 24px;">{{ $sMeta['icon'] }}</span>
                        <div>
                            <span style="font-size: 12px; font-weight: 700; color: {{ $sMeta['color'] }}; text-transform: uppercase; display: block;">
                                {{ $sMeta['short'] }}
                            </span>
                            <span style="font-size: 13px; font-weight: 700; color: #1e293b;">
                                {{ ucfirst($sKey) }}
                            </span>
                        </div>
                    </div>
                    <div style="font-size: 24px; font-weight: 800; color: {{ $sMeta['color'] }};">
                        {{ $count }}
                        <span style="font-size: 12px; font-weight: 600; color: #64748b;">câu</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TAB CHUYỂN ĐỔI: CÂU HỎI ĐÃ LƯU VS DẠNG BÀI ĐÃ LƯU --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.bookmarks', array_merge(request()->query(), ['tab' => 'questions'])) }}" 
               class="view-mode-tab {{ $activeTab === 'questions' ? 'active' : '' }}">
                <span> Câu hỏi đã lưu</span>
                <span class="stat-badge-chip">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('practice.bookmarks', array_merge(request()->query(), ['tab' => 'types'])) }}" 
               class="view-mode-tab {{ $activeTab === 'types' ? 'active' : '' }}">
                <span> Dạng bài quan tâm</span>
                <span class="stat-badge-chip">{{ $savedTypes->count() }}</span>
            </a>
        </div>
    </div>

    {{-- TAB 1: CÂU HỎI ĐÃ LƯU --}}
    @if($activeTab === 'questions')

        {{-- BỘ LỌC KỸ NĂNG THEO TAB --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px 12px 0 0; border-bottom: none; display: flex; overflow-x: auto; padding: 6px 12px 0;">
            <a href="{{ route('practice.bookmarks', array_merge(request()->except(['page']), ['skill' => 'all'])) }}" 
               class="skill-tab-item {{ $selectedSkill === 'all' ? 'active' : '' }}">
                <span>✨ Tất cả</span>
                <span class="stat-badge-chip">{{ $stats['total'] }}</span>
            </a>
            @foreach($skillMeta as $sKey => $sMeta)
                <a href="{{ route('practice.bookmarks', array_merge(request()->except(['page']), ['skill' => $sKey])) }}" 
                   class="skill-tab-item {{ $selectedSkill === $sKey ? 'active' : '' }}">
                    <span>{{ $sMeta['icon'] }} {{ $sMeta['short'] }}</span>
                    <span class="stat-badge-chip">{{ $stats[$sKey] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        {{-- THANH CÔNG CỤ TÌM KIẾM & BỘ LỌC CHI TIẾT --}}
        <div class="card" style="margin-top: 0; border-radius: 0 0 12px 12px; padding: 18px 20px; margin-bottom: 22px; border-top: 1px solid #f1f5f9;">
            <form method="GET" action="{{ route('practice.bookmarks') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto; gap: 12px; align-items: flex-end;">
                <input type="hidden" name="tab" value="questions">
                <input type="hidden" name="skill" value="{{ $selectedSkill }}">

                {{-- Lọc theo Dạng bài --}}
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                        Dạng bài luyện tập:
                    </label>
                    <select name="loai_id" class="form-control" style="font-size: 14px; padding: 9px 12px;">
                        <option value="">-- Tất cả dạng bài --</option>
                        @foreach($types as $t)
                            @if($selectedSkill === 'all' || $t->kynang === $selectedSkill)
                                <option value="{{ $t->id }}" {{ $selectedLoai == $t->id ? 'selected' : '' }}>
                                    [{{ ucfirst($t->kynang) }}] {{ $t->ten }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Lọc theo Độ khó --}}
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                        Cấp độ độ khó:
                    </label>
                    <select name="dokho" class="form-control" style="font-size: 14px; padding: 9px 12px;">
                        <option value="">-- Tất cả cấp độ (1 - 10) --</option>
                        @for($d = 1; $d <= 10; $d++)
                            <option value="{{ $d }}" {{ $selectedDokho == $d ? 'selected' : '' }}>
                                Cấp độ {{ $d }} ({{ $d <= 3 ? 'Dễ' : ($d <= 7 ? 'Vừa' : 'Khó') }})
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tìm kiếm từ khóa --}}
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                        Từ khóa tìm kiếm câu hỏi:
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ $keyword }}" 
                           placeholder="Nhập nội dung câu hỏi..." 
                           class="form-control" 
                           style="font-size: 14px; padding: 9px 12px;">
                </div>

                {{-- Nút bấm Lọc & Reset --}}
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 18px; font-weight: 700;">
                         Lọc
                    </button>
                    @if($selectedLoai || $selectedDokho || $keyword || $selectedSkill !== 'all')
                        <a href="{{ route('practice.bookmarks', ['tab' => 'questions']) }}" class="btn btn-outline btn-sm" style="padding: 10px 14px;" title="Xóa bộ lọc">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- DANH SÁCH BẢNG CÂU HỎI ĐÃ LƯU --}}
        @if($bookmarks->isEmpty())
            <div class="card" style="text-align: center; padding: 50px 20px; border-radius: 14px;">
                <div style="font-size: 48px; margin-bottom: 12px;">🌟</div>
                <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">
                    @if($selectedSkill !== 'all' || $selectedLoai || $selectedDokho || $keyword)
                        Không tìm thấy câu hỏi đã lưu phù hợp với bộ lọc
                    @else
                        Bạn chưa lưu câu hỏi nào
                    @endif
                </h3>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Khi đang làm bài luyện tập hoặc xem kết quả, bạn có thể nhấn vào nút <strong>"☆ Lưu câu hỏi"</strong> để lưu lại các câu cần ôn luyện.
                </p>
                <div style="display: flex; justify-content: center; gap: 10px;">
                    @if($selectedSkill !== 'all' || $selectedLoai || $selectedDokho || $keyword)
                        <a href="{{ route('practice.bookmarks', ['tab' => 'questions']) }}" class="btn btn-outline">
                            Xóa bộ lọc tìm kiếm
                        </a>
                    @else
                        <a href="{{ route('practice.select') }}" class="btn btn-primary">
                            Bắt đầu luyện tập ngay 
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="card" style="padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; color: #475569; font-weight: 700; font-size: 13px; text-transform: uppercase;">
                                <th style="padding: 14px 18px;">Mã & Độ khó</th>
                                <th style="padding: 14px 18px;">Kỹ năng & Dạng bài</th>
                                <th style="padding: 14px 18px;">Nội dung câu hỏi</th>
                                <th style="padding: 14px 18px;">Ngày lưu</th>
                                <th style="padding: 14px 18px; text-align: right;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookmarks as $bm)
                                @php
                                    $kynang = $bm->kynang ?? 'reading';
                                    $sm = $skillMeta[$kynang] ?? $skillMeta['reading'];
                                    $dokho = $bm->dokho ?? 5;
                                    $dokhoBadgeBg = $dokho <= 3 ? '#dcfce7' : ($dokho <= 7 ? '#fef9c3' : '#fee2e2');
                                    $dokhoColor = $dokho <= 3 ? '#166534' : ($dokho <= 7 ? '#854d0e' : '#991b1b');
                                    $dokhoLabel = $dokho <= 3 ? 'Dễ' : ($dokho <= 7 ? 'Vừa' : 'Khó');
                                @endphp
                                <tr class="bm-row" id="bm-row-{{ $bm->cauhoi_id }}" style="border-bottom: 1px solid #f1f5f9;">
                                    
                                    {{-- Cột 1: Mã câu & Độ khó --}}
                                    <td style="padding: 14px 18px; white-space: nowrap;">
                                        <div style="font-weight: 800; color: #0f172a; font-size: 15px; margin-bottom: 4px;">
                                            #{{ $bm->cauhoi_id }}
                                        </div>
                                        <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: {{ $dokhoBadgeBg }}; color: {{ $dokhoColor }};">
                                            Cấp độ {{ $dokho }}/10 • {{ $dokhoLabel }}
                                        </span>
                                    </td>

                                    {{-- Cột 2: Kỹ năng & Dạng bài --}}
                                    <td style="padding: 14px 18px; white-space: nowrap;">
                                        <span class="badge {{ $sm['badge'] }}" style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <span>{{ $sm['icon'] }}</span>
                                            <span>{{ $sm['short'] }}</span>
                                        </span>
                                        <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 2px;">
                                            {{ $bm->loai_ten ?? 'Tổng hợp' }}
                                        </div>
                                    </td>

                                    {{-- Cột 3: Nội dung câu hỏi --}}
                                    <td style="padding: 14px 18px; color: #334155; max-width: 380px;">
                                        <div style="line-height: 1.5; font-size: 14px; font-weight: 600; color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $bm->cauhoi->noidung ?? ('Câu hỏi ID #' . $bm->cauhoi_id) }}
                                        </div>
                                        <div style="margin-top: 4px; display: flex; gap: 8px; align-items: center; font-size: 12px; color: #64748b;">
                                            @if(!empty($bm->cauhoi->audio))
                                                <span style="color: #7c3aed; font-weight: 700;">🎧 Kèm âm thanh</span>
                                            @endif
                                            @if(!empty($bm->cauhoi->anh))
                                                <span style="color: #0284c7; font-weight: 700;">🖼️ Kèm hình ảnh</span>
                                            @endif
                                            <span>Điểm: {{ $bm->cauhoi->diem ?? 1 }}đ</span>
                                        </div>
                                    </td>

                                    {{-- Cột 4: Ngày lưu --}}
                                    <td style="padding: 14px 18px; color: #64748b; font-size: 13px; white-space: nowrap;">
                                        {{ date('d/m/Y H:i', strtotime($bm->ngaytao)) }}
                                    </td>

                                    {{-- Cột 5: Hành động --}}
                                    <td style="padding: 14px 18px; text-align: right; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                                            <a href="{{ route('practice.show', $bm->cauhoi_id) }}" 
                                               class="btn btn-primary btn-sm" 
                                               style="font-size: 13px; font-weight: 700; padding: 6px 12px;">
                                                Luyện câu này
                                            </a>

                                            @if(!empty($bm->cauhoi->loai_id))
                                                <a href="{{ route('practice.session', ['loai_id' => $bm->cauhoi->loai_id]) }}" 
                                                   class="btn btn-outline btn-sm" 
                                                   style="font-size: 13px; font-weight: 600; padding: 6px 10px;"
                                                   title="Luyện tập tất cả câu thuộc dạng bài này">
                                                    🎯 Dạng này
                                                </a>
                                            @endif

                                            <button type="button" 
                                                    onclick="removeBookmark({{ $bm->cauhoi_id }})" 
                                                    class="btn btn-outline btn-sm" 
                                                    style="color: #ef4444; border-color: #fca5a5; font-size: 12px; padding: 6px 10px;" 
                                                    title="Bỏ lưu câu hỏi này">
                                                ✕
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PHÂN TRANG --}}
            <div style="display: flex; justify-content: center; margin-top: 10px;">
                {{ $bookmarks->links('partials.pagination') }}
            </div>
        @endif

    {{-- TAB 2: DẠNG BÀI ĐÃ LƯU --}}
    @else
        <div style="margin-bottom: 20px;">
            <p style="color: #64748b; font-size: 14px;">
                Danh sách các dạng bài luyện tập Duolingo English Test bạn đã đánh dấu để tập trung rèn luyện:
            </p>
        </div>

        @if($savedTypes->isEmpty())
            <div class="card" style="text-align: center; padding: 50px 20px; border-radius: 14px;">
                <div style="font-size: 48px; margin-bottom: 12px;">📌</div>
                <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">
                    Bạn chưa lưu dạng bài nào
                </h3>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Tại trang chọn dạng bài luyện tập, bạn có thể nhấn nút <strong>"⭐ Lưu dạng bài"</strong> trên mỗi thẻ để lưu các dạng bài trọng tâm vào đây.
                </p>
                <a href="{{ route('practice.select') }}" class="btn btn-primary">
                    Khám phá các dạng bài luyện tập →
                </a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                @foreach($savedTypes as $st)
                    @php
                        $stMeta = $skillMeta[$st->kynang] ?? $skillMeta['reading'];
                    @endphp
                    <div class="card" id="saved-type-card-{{ $st->id }}" style="border-radius: 14px; border: 1.5px solid #e2e8f0; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span class="badge {{ $stMeta['badge'] }}" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    {{ $stMeta['icon'] }} {{ $stMeta['name'] }}
                                </span>
                                <button type="button" 
                                        onclick="removeTypeBookmark({{ $st->id }})" 
                                        class="btn btn-outline btn-sm" 
                                        style="color: #ef4444; border-color: #fca5a5; font-size: 11px; padding: 3px 8px;"
                                        title="Bỏ lưu dạng bài này">
                                    ✕ Bỏ lưu
                                </button>
                            </div>

                            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">
                                {{ $st->ten }}
                            </h3>

                            <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0 0 16px 0;">
                                {{ $st->mota ?: 'Rèn luyện các câu hỏi chuẩn định dạng Duolingo English Test.' }}
                            </p>
                        </div>

                        <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; color: #64748b; font-weight: 600;">
                                📚 {{ $st->question_count }} câu trong ngân hàng
                            </span>

                            <a href="{{ route('practice.session', ['loai_id' => $st->id]) }}" class="btn btn-primary btn-sm" style="font-weight: 700;">
                                Bắt đầu làm ngay 
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="toast-msg"></div>

@push('scripts')
<script>
function showToast(message) {
    const toast = document.getElementById('toast-notification');
    if (toast) {
        toast.innerText = message;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 2800);
    }
}

async function removeBookmark(qid) {
    if (!confirm('Bạn có chắc muốn bỏ lưu câu hỏi #' + qid + ' khỏi danh sách?')) {
        return;
    }

    try {
        const response = await fetch('/practice/question/' + qid + '/bookmark', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success) {
            const row = document.getElementById('bm-row-' + qid);
            if (row) {
                row.style.opacity = '0';
                row.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    row.remove();
                }, 300);
            }
            showToast('✓ ' + data.message);
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    } catch (e) {
        console.error(e);
        // Fallback gửi form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/practice/question/' + qid + '/bookmark';
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

async function removeTypeBookmark(loaiId) {
    if (!confirm('Bạn có chắc muốn bỏ lưu dạng bài này?')) {
        return;
    }

    try {
        const response = await fetch('/practice/type/' + loaiId + '/bookmark', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success) {
            const card = document.getElementById('saved-type-card-' + loaiId);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                }, 300);
            }
            showToast('✓ ' + data.message);
        }
    } catch (e) {
        console.error(e);
    }
}
</script>
@endpush
@endsection
