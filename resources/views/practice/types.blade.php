@extends('layouts.app')

@section('title', 'Luyện tập - AmberDET')

@section('content')
@php
    $skills = [
        'reading' => [
            'label' => 'Đọc',
            'name' => 'Reading',
            'description' => 'Luyện từ vựng, ngữ pháp và đọc hiểu.',
        ],
        'listening' => [
            'label' => 'Nghe',
            'name' => 'Listening',
            'description' => 'Luyện nghe hiểu và nhận diện thông tin.',
        ],
        'writing' => [
            'label' => 'Viết',
            'name' => 'Writing',
            'description' => 'Luyện mô tả hình ảnh và viết theo chủ đề.',
        ],
        'speaking' => [
            'label' => 'Nói',
            'name' => 'Speaking',
            'description' => 'Luyện phát âm và diễn đạt qua bài nói.',
        ],
    ];

    $currentSkill = $activeSkill ?? 'all';

    if ($currentSkill !== 'all' && !array_key_exists($currentSkill, $skills)) {
        $currentSkill = 'all';
    }

    $visibleTypes = $currentSkill === 'all'
        ? $types
        : ($groupedTypes[$currentSkill] ?? collect());
@endphp

<div class="practice-catalog">
    <header class="pc-heading">
        <span class="pc-eyebrow">KHÔNG GIAN LUYỆN TẬP</span>
        <h1>Luyện tập từng kỹ năng</h1>
        <p>Chọn dạng bài để bắt đầu. Xem kết quả và lời giải sau mỗi lượt luyện tập.</p>
    </header>

    <nav class="pc-filters" aria-label="Lọc theo kỹ năng">
        <a href="{{ route('practice.index') }}"
           class="pc-filter {{ $currentSkill === 'all' ? 'is-active' : '' }}"
           @if ($currentSkill === 'all') aria-current="page" @endif>
            Tất cả
            <span>{{ $types->count() }}</span>
        </a>

        @foreach ($skills as $key => $skill)
            <a href="{{ route('practice.index', ['skill' => $key]) }}"
               class="pc-filter {{ $currentSkill === $key ? 'is-active' : '' }}"
               @if ($currentSkill === $key) aria-current="page" @endif>
                {{ $skill['label'] }}
                <span>{{ ($groupedTypes[$key] ?? collect())->count() }}</span>
            </a>
        @endforeach
    </nav>

    @foreach ($skills as $key => $skill)
        @php
            $skillTypes = $groupedTypes[$key] ?? collect();
        @endphp

        @if (
            $skillTypes->isNotEmpty()
            && ($currentSkill === 'all' || $currentSkill === $key)
        )
            <section class="pc-section" aria-labelledby="pc-{{ $key }}">
                <div class="pc-section-heading">
                    <h2 id="pc-{{ $key }}">
                        {{ $skill['label'] }}
                        <span>{{ $skill['name'] }}</span>
                    </h2>
                    <p>{{ $skill['description'] }}</p>
                </div>

                <div class="pc-grid">
                    @foreach ($skillTypes as $type)
                        <article class="pc-card">
                            <div class="pc-card-meta">
                                <span class="pc-badge">{{ $skill['label'] }}</span>

                                @if (!empty($type->thoigian))
                                    <span>{{ $type->thoigian }} giây</span>
                                @endif
                            </div>

                            <h3>{{ $type->ten }}</h3>

                            <p class="pc-description">
                                {{ $type->mota ?: 'Làm quen và luyện tập dạng câu hỏi này.' }}
                            </p>

                            <div class="pc-card-footer">
                                <span>
                                    {{ ($type->cachcham ?? 'auto') === 'ai'
                                        ? 'Chấm bằng AI'
                                        : 'Chấm tự động' }}
                                </span>

                                <a href="{{ route('practice.select', ['loai_id' => $type->id]) }}"
                                   class="pc-start"
                                   aria-label="Luyện tập {{ $type->ten }}">
                                    Luyện tập
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($visibleTypes->isEmpty())
        <div class="pc-empty">
            <h2>Chưa có dạng bài</h2>
            <p>Nội dung luyện tập cho kỹ năng này đang được cập nhật.</p>
            <a href="{{ route('practice.index') }}" class="pc-start">
                Xem tất cả
            </a>
        </div>
    @endif
</div>
@endsection