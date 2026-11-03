@extends('layouts.app')

@section('title', 'Danh sách dạng bài luyện tập - DET Practice')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; color: #1e293b;">📚 Danh mục dạng bài luyện tập DET</h1>
            <p style="color: #64748b; font-size: 15px; margin-top: 4px;">
                Chọn dạng bài bạn muốn rèn luyện để nâng cao phản xạ và cải thiện từng kỹ năng cho bài thi Duolingo English Test.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('practice.bookmarks') }}" class="btn btn-outline btn-sm">⭐ Câu hỏi đã lưu</a>
            <a href="{{ route('practice.history') }}" class="btn btn-outline btn-sm">⏱️ Lịch sử luyện tập</a>
        </div>
    </div>
</div>

@php
    $skillNames = [
        'reading' => ['name' => 'Reading (Kỹ năng Đọc)', 'icon' => '📖', 'badge' => 'badge-reading', 'desc' => 'Rèn luyện khả năng nhận diện từ vựng, ngữ pháp và đọc hiểu đoạn văn tương tác.'],
        'listening' => ['name' => 'Listening (Kỹ năng Nghe)', 'icon' => '🎧', 'badge' => 'badge-listening', 'desc' => 'Nghe hiểu câu thoại thực tế, nhận diện từ vựng phát âm chuẩn và đàm thoại.'],
        'writing' => ['name' => 'Writing (Kỹ năng Viết)', 'icon' => '✍️', 'badge' => 'badge-writing', 'desc' => 'Miêu tả hình ảnh và viết bài luận tự do theo chủ đề học thuật hoặc đời sống.'],
        'speaking' => ['name' => 'Speaking (Kỹ năng Nói)', 'icon' => '🎙️', 'badge' => 'badge-speaking', 'desc' => 'Đọc thành tiếng, nói mô tả tranh và trả lời câu hỏi trực tiếp qua microphone.'],
    ];
    $currentSkill = $activeSkill ?? 'all';
@endphp

<!-- BỘ LỌC TABS THEO KỸ NĂNG (THEO PHONG CÁCH THANH ĐIỀU HƯỚNG GẠCH CHÂN XANH LÁ) -->
<div class="skill-filter-container">
    <div class="skill-filter-tabs">
        <button type="button" class="skill-tab-btn {{ $currentSkill === 'all' ? 'active' : '' }}" onclick="filterSkill('all', this)">
            <span>✨ Tất cả kỹ năng</span>
            <span class="skill-tab-badge">{{ $types->count() }}</span>
        </button>

        <button type="button" class="skill-tab-btn {{ $currentSkill === 'reading' ? 'active' : '' }}" onclick="filterSkill('reading', this)">
            <span>📖 Reading (Đọc)</span>
            <span class="skill-tab-badge">{{ $groupedTypes['reading']->count() }}</span>
        </button>

        <button type="button" class="skill-tab-btn {{ $currentSkill === 'listening' ? 'active' : '' }}" onclick="filterSkill('listening', this)">
            <span>🎧 Listening (Nghe)</span>
            <span class="skill-tab-badge">{{ $groupedTypes['listening']->count() }}</span>
        </button>

        <button type="button" class="skill-tab-btn {{ $currentSkill === 'writing' ? 'active' : '' }}" onclick="filterSkill('writing', this)">
            <span>✍️ Writing (Viết)</span>
            <span class="skill-tab-badge">{{ $groupedTypes['writing']->count() }}</span>
        </button>

        <button type="button" class="skill-tab-btn {{ $currentSkill === 'speaking' ? 'active' : '' }}" onclick="filterSkill('speaking', this)">
            <span>🎙️ Speaking (Nói)</span>
            <span class="skill-tab-badge">{{ $groupedTypes['speaking']->count() }}</span>
        </button>
    </div>
</div>

<!-- CÁC KHỐI DẠNG BÀI THEO KỸ NĂNG -->
<div id="skills-wrapper">
    @foreach($groupedTypes as $skill => $skillTypes)
        @if($skillTypes->isNotEmpty())
            <div class="skill-group-section" id="skill-section-{{ $skill }}" data-skill="{{ $skill }}" style="margin-bottom: 36px; {{ ($currentSkill !== 'all' && $currentSkill !== $skill) ? 'display: none;' : '' }}">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <span style="font-size: 24px;">{{ $skillNames[$skill]['icon'] ?? '📝' }}</span>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b;">
                            {{ $skillNames[$skill]['name'] ?? ucfirst($skill) }}
                        </h2>
                        <p style="color: #64748b; font-size: 13px;">{{ $skillNames[$skill]['desc'] ?? '' }}</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                    @foreach($skillTypes as $type)
                        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <span class="badge {{ $skillNames[$skill]['badge'] ?? 'badge-user' }}">{{ $type->kynang }}</span>
                                    @if(!empty($type->thoigian))
                                        <span style="font-size: 12px; color: #64748b;">⏳ {{ $type->thoigian }} giây</span>
                                    @endif
                                </div>
                                <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                                    {{ $type->ten }}
                                </h3>
                                <p style="color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
                                    {{ $type->mota ?? 'Luyện tập câu hỏi định dạng chuẩn theo format đề thi Duolingo English Test.' }}
                                </p>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                                <span style="font-size: 12px; color: #94a3b8;">
                                    Chấm: {{ ($type->cachcham ?? 'auto') === 'ai' ? '🤖 AI chấm' : '⚡ Tự động' }}
                                </span>
                                <a href="{{ route('practice.select', ['loai_id' => $type->id]) }}" class="btn btn-primary btn-sm">
                                    Luyện tập ngay →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    <!-- THÔNG BÁO NẾU KỸ NĂNG CHƯA CÓ BÀI TẬP -->
    <div id="no-skill-notice" class="card" style="display: none; text-align: center; padding: 40px 20px;">
        <div style="font-size: 36px; margin-bottom: 10px;">🔍</div>
        <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 6px;">Chưa có dạng bài luyện tập cho kỹ năng này</h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 16px;">Các câu hỏi cho kỹ năng đang được cập nhật thêm vào ngân hàng đề thi.</p>
        <button type="button" class="btn btn-outline" onclick="filterSkill('all')">Xem tất cả các dạng bài</button>
    </div>
</div>

<script>
    // Hàm lọc dạng bài theo kỹ năng được chọn
    function filterSkill(skill, btn) {
        // 1. Cập nhật giao diện tab đang active
        const allTabs = document.querySelectorAll('.skill-tab-btn');
        allTabs.forEach(tab => tab.classList.remove('active'));

        if (!btn) {
            btn = document.querySelector(`.skill-tab-btn[onclick*="'${skill}'"]`);
        }
        if (btn) {
            btn.classList.add('active');
        }

        // 2. Ẩn/hiện các phần kỹ năng tương ứng
        const sections = document.querySelectorAll('.skill-group-section');
        let visibleCount = 0;

        sections.forEach(sec => {
            const secSkill = sec.getAttribute('data-skill');
            if (skill === 'all' || secSkill === skill) {
                sec.style.display = 'block';
                visibleCount++;
            } else {
                sec.style.display = 'none';
            }
        });

        // 3. Hiển thị thông báo rỗng nếu kỹ năng đó chưa có dữ liệu
        const notice = document.getElementById('no-skill-notice');
        if (notice) {
            notice.style.display = (visibleCount === 0) ? 'block' : 'none';
        }

        // 4. Đồng bộ tham số skill lên URL mà không reload trang
        const url = new URL(window.location);
        if (skill === 'all') {
            url.searchParams.delete('skill');
        } else {
            url.searchParams.set('skill', skill);
        }
        window.history.replaceState({}, '', url);
    }

    // Tự động kích hoạt tab dựa theo URL khi người dùng vào trang
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const skillParam = urlParams.get('skill');
        if (skillParam && ['reading', 'listening', 'writing', 'speaking', 'all'].includes(skillParam)) {
            filterSkill(skillParam);
        }
    });
</script>
@endsection
