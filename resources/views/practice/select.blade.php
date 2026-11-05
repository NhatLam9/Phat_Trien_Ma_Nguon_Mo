@extends('layouts.app')

@section('title', ($selectedType ? $selectedType->ten . ' - ' : '') . 'Luyện tập theo dạng bài & kỹ năng - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('practice.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
        ← Quay lại danh mục 4 kỹ năng
    </a>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px; margin-top: 10px;">
        <div>
            @if($selectedType)
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <span class="badge {{ $skillMeta[$selectedType->kynang]['badge'] ?? 'badge-user' }}" style="font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        {{ $skillMeta[$selectedType->kynang]['name'] ?? ucfirst($selectedType->kynang) }}
                    </span>
                    <span style="color: #94a3b8;">/</span>
                    <span style="font-size: 14px; font-weight: 700; color: #00b86b;">Dạng bài chuyên sâu</span>
                </div>
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0;">
                    🎯 {{ $selectedType->ten }}
                </h1>
                <p style="color: #64748b; font-size: 14px; margin-top: 6px;">
                    {{ $selectedType->mota ?? 'Rèn luyện các câu hỏi chuẩn định dạng Duolingo English Test có sẵn trong cơ sở dữ liệu.' }}
                </p>
            @else
                <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
                    🎯 Chọn câu hỏi luyện tập theo kỹ năng & độ khó (1 - 10)
                </h1>
                <p style="color: #64748b; font-size: 14px; margin-top: 4px;">
                    Chọn câu hỏi luyện tập theo từng kỹ năng và mức độ để rèn luyện liên tiếp và nộp bài bất cứ lúc nào.
                </p>
            @endif
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.history') }}" class="btn btn-outline btn-sm">
                ⏱️ Lịch sử luyện tập
            </a>
            <a href="{{ route('practice.bookmarks') }}" class="btn btn-outline btn-sm">
                ⭐ Câu hỏi đã lưu
            </a>
        </div>
    </div>
</div>

<!-- THANH TAB LỌC KỸ NĂNG (GẠCH CHÂN XANH LÁ) -->
<div class="skill-filter-container" style="margin-bottom: 24px;">
    <div class="skill-filter-tabs">
        <a href="{{ route('practice.select', ['skill' => 'all']) }}" 
           class="skill-tab-btn {{ ($selectedSkill === 'all' || empty($selectedSkill)) && !$selectedType ? 'active' : '' }}">
            <span>✨ Tất cả kỹ năng</span>
        </a>
        <a href="{{ route('practice.select', ['skill' => 'reading']) }}" 
           class="skill-tab-btn {{ $selectedSkill === 'reading' && !$selectedType ? 'active' : '' }}">
            <span>📖 Reading (Đọc)</span>
        </a>
        <a href="{{ route('practice.select', ['skill' => 'listening']) }}" 
           class="skill-tab-btn {{ $selectedSkill === 'listening' && !$selectedType ? 'active' : '' }}">
            <span>🎧 Listening (Nghe)</span>
        </a>
        <a href="{{ route('practice.select', ['skill' => 'writing']) }}" 
           class="skill-tab-btn {{ $selectedSkill === 'writing' && !$selectedType ? 'active' : '' }}">
            <span>✍️ Writing (Viết)</span>
        </a>
        <a href="{{ route('practice.select', ['skill' => 'speaking']) }}" 
           class="skill-tab-btn {{ $selectedSkill === 'speaking' && !$selectedType ? 'active' : '' }}">
            <span>🎙️ Speaking (Nói)</span>
        </a>
    </div>
</div>

@if($selectedType)
    <!-- GIAO DIỆN CHỌN CẤP ĐỘ ĐỘ KHÓ CHO DẠNG BÀI ĐÃ CHỌN (VD: INTERACTIVE READING) -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">
                    Chọn cấp độ luyện tập cho <span style="color: #00b86b;">{{ $selectedType->ten }}</span>
                </h2>
            </div>

            <!-- GÓI TẤT CẢ ĐỘ KHÓ: LÀM TOÀN BỘ CÂU CỦA DẠNG BÀI -->
            <a href="{{ route('practice.session', ['loai_id' => $selectedType->id]) }}" 
               class="btn btn-primary" 
               style="font-weight: 800; padding: 12px 24px; font-size: 15px; box-shadow: 0 4px 12px rgba(0, 184, 107, 0.25);">
                 Luyện tập TẤT CẢ độ khó 
            </a>
        </div>

        <div style="font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 14px; letter-spacing: 0.5px;">
            Hoặc chọn luyện tập theo từng cấp độ cụ thể (1 - 10):
        </div>

        @if($typeDifficulties->isEmpty())
            <div style="text-align: center; padding: 36px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b; font-size: 14px; margin-bottom: 14px;">
                    Hiện chưa có câu hỏi nào được gán cấp độ cho dạng bài này trong CSDL.
                </p>
                <a href="{{ route('practice.session', ['loai_id' => $selectedType->id]) }}" class="btn btn-outline btn-sm">
                    Làm thử câu hỏi mặc định 
                </a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                @foreach($typeDifficulties as $diff)
                    <div class="card" style="margin-bottom: 0; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 20px;
                                    {{ $diff->dokho <= 3 ? 'background: #dcfce7; color: #166534;' : ($diff->dokho <= 7 ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #991b1b;') }}">
                                    Cấp độ {{ $diff->dokho }}/10 • {{ $diff->mucdo }}
                                </span>
                                <span style="font-size: 13px; font-weight: 700; color: #00b86b; background: #e6f9f0; padding: 2px 8px; border-radius: 6px;">
                                    {{ $diff->count }} câu có sẵn
                                </span>
                            </div>

                            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                                {{ $selectedType->ten }} (Cấp độ {{ $diff->dokho }})
                            </h3>
                        </div>

                        <div style="border-top: 1px solid #f1f5f9; padding-top: 12px;">
                            <a href="{{ route('practice.session', ['loai_id' => $selectedType->id, 'dokho' => $diff->dokho]) }}" 
                               class="btn btn-outline" 
                               style="width: 100%; justify-content: center; font-weight: 700; color: #00b86b; border-color: #00b86b;">
                                Bắt đầu cấp độ {{ $diff->dokho }} 
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- BỘ LỌC TÌM KIẾM NHANH THEO TỪ KHÓA HOẶC ĐỘ KHÓ -->
<div class="card" style="padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('practice.select') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        @if($loaiId)
            <input type="hidden" name="loai_id" value="{{ $loaiId }}">
        @endif
        @if($selectedSkill && $selectedSkill !== 'all')
            <input type="hidden" name="skill" value="{{ $selectedSkill }}">
        @endif

        <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Kỹ năng</label>
            <select name="skill" class="form-control" style="font-size: 13px; height: 38px;" onchange="this.form.submit()">
                <option value="all" {{ ($selectedSkill === 'all' || empty($selectedSkill)) ? 'selected' : '' }}>-- Tất cả kỹ năng --</option>
                <option value="reading" {{ $selectedSkill === 'reading' ? 'selected' : '' }}>📖 Reading (Đọc)</option>
                <option value="listening" {{ $selectedSkill === 'listening' ? 'selected' : '' }}>🎧 Listening (Nghe)</option>
                <option value="writing" {{ $selectedSkill === 'writing' ? 'selected' : '' }}>✍️ Writing (Viết)</option>
                <option value="speaking" {{ $selectedSkill === 'speaking' ? 'selected' : '' }}>🎙️ Speaking (Nói)</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Độ khó (1 - 10)</label>
            <select name="dokho" class="form-control" style="font-size: 13px; height: 38px;" onchange="this.form.submit()">
                <option value="">-- Tất cả độ khó --</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ (string)$dokho === (string)$i ? 'selected' : '' }}>
                        Cấp độ {{ $i }} {{ $i <= 3 ? '(Dễ)' : ($i <= 7 ? '(Trung bình)' : '(Khó)') }}
                    </option>
                @endfor
            </select>
        </div>

        <div style="flex: 2; min-width: 220px;">
            <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Tìm kiếm từ khóa câu hỏi</label>
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Nhập từ khóa nội dung câu hỏi..." class="form-control" style="font-size: 13px; height: 38px;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 18px; font-weight: 700;">Tìm kiếm</button>
            <a href="{{ route('practice.select') }}" class="btn btn-outline" style="height: 38px; padding: 0 12px;">Đặt lại</a>
        </div>
    </form>
</div>

<!-- DANH SÁCH DẠNG BÀI THEO TIÊU ĐỀ KỸ NĂNG -->
<div style="margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">
        Danh sách dạng bài luyện tập theo kỹ năng:
    </h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; margin-bottom: 36px;">
    @foreach($typeCards as $card)
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0; border: 1.5px solid #e2e8f0; border-radius: 14px; transition: transform 0.2s, box-shadow 0.2s;">
            <div>
                <!-- Header Card: Badge Kỹ năng + Nút lưu dạng bài -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span class="badge {{ $card->badge }}" style="font-weight: 700; font-size: 11px; text-transform: uppercase;">
                        {{ $card->skill_name }}
                    </span>
                    @php $isTypeSaved = in_array((int)$card->id, $savedTypeIds ?? []); @endphp
                    <button type="button" 
                            class="btn-save-type btn btn-sm" 
                            id="btn-save-type-{{ $card->id }}" 
                            onclick="toggleTypeBookmark({{ $card->id }})"
                            title="{{ $isTypeSaved ? 'Bỏ lưu dạng bài này' : 'Lưu dạng bài này vào danh sách quan tâm' }}"
                            style="padding: 3px 9px; font-size: 12px; font-weight: 700; border-radius: 6px; {{ $isTypeSaved ? 'background: #fef3c7; color: #b45309; border: 1px solid #fcd34d;' : 'background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1;' }}">
                        <span id="star-type-{{ $card->id }}">{{ $isTypeSaved ? '⭐' : '☆' }}</span>
                        <span id="label-type-{{ $card->id }}" style="font-size: 11px;">{{ $isTypeSaved ? 'Đã lưu' : 'Lưu dạng' }}</span>
                    </button>
                </div>

                <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 6px;">
                    {{ $card->ten }}
                </h3>

                <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
                    {{ $card->mota ?: 'Rèn luyện phản xạ và kỹ năng theo đúng cấu trúc đề thi Duolingo English Test.' }}
                </p>

                <!-- Các cấp độ có sẵn trong CSDL -->
                @if($card->difficulties->isNotEmpty())
                    <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 10px 12px; border-radius: 8px; margin-bottom: 16px;">
                        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 6px;">
                            Các cấp độ có câu hỏi:
                        </span>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            @foreach($card->difficulties as $d)
                                <a href="{{ route('practice.session', ['loai_id' => $card->id, 'dokho' => $d->dokho]) }}" 
                                   style="font-size: 12px; font-weight: 700; text-decoration: none; padding: 3px 8px; border-radius: 6px;
                                   {{ $d->dokho <= 3 ? 'background: #dcfce7; color: #166534;' : ($d->dokho <= 7 ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #991b1b;') }}">
                                    Cấp {{ $d->dokho }} ({{ $d->count }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Nút hành động -->
            <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; display: flex; gap: 8px;">
                <a href="{{ route('practice.session', ['loai_id' => $card->id, 'dokho' => $dokho]) }}" 
                   class="btn btn-primary btn-sm" 
                   style="flex: 1.3; justify-content: center; font-weight: 700;">
                    Luyện tập ngay 
                </a>
            </div>
        </div>
    @endforeach
</div>

<div id="type-toast" style="position: fixed; bottom: 24px; right: 24px; background: #1e293b; color: #ffffff; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; display: none;"></div>

@push('scripts')
<script>
async function toggleTypeBookmark(loaiId) {
    const btn = document.getElementById('btn-save-type-' + loaiId);
    const star = document.getElementById('star-type-' + loaiId);
    const label = document.getElementById('label-type-' + loaiId);

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
            if (data.is_bookmarked) {
                if (btn) {
                    btn.style.background = '#fef3c7';
                    btn.style.color = '#b45309';
                    btn.style.borderColor = '#fcd34d';
                }
                if (star) star.innerText = '⭐';
                if (label) label.innerText = 'Đã lưu';
                showTypeToast('⭐ ' + data.message);
            } else {
                if (btn) {
                    btn.style.background = '#f8fafc';
                    btn.style.color = '#64748b';
                    btn.style.borderColor = '#cbd5e1';
                }
                if (star) star.innerText = '☆';
                if (label) label.innerText = 'Lưu dạng';
                showTypeToast('✓ ' + data.message);
            }
        }
    } catch (e) {
        console.error(e);
    }
}

function showTypeToast(msg) {
    const t = document.getElementById('type-toast');
    if (t) {
        t.innerText = msg;
        t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2600);
    }
}
</script>
@endpush
@endsection
