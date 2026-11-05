@php
    $duLieu = json_decode((string) $log->daura, true);
    $laJson = is_array($duLieu);

    $nhanKyNang = [
        'grammar' => 'Ngữ pháp',
        'vocabulary' => 'Từ vựng',
        'coherence' => 'Mạch lạc',
        'task_completion' => 'Hoàn thành yêu cầu',
        'fluency' => 'Độ trôi chảy',
        'relevance' => 'Liên quan chủ đề',
    ];

    $hopStyle = 'background: #ffffff; border: 1px solid #d1fae5; border-radius: 8px; padding: 0.7rem 0.9rem;';
    $nhanStyle = 'display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #047857; margin-bottom: 0.3rem;';
    $chuStyle = 'margin: 0; color: #1e293b; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;';
    $mucStyle = 'margin: 0.9rem 0 0;';
    $danhSachStyle = 'margin: 0.35rem 0 0; padding-left: 1.2rem; color: #1e293b; font-size: 0.93rem; line-height: 1.6;';
@endphp

@if (! $laJson)
    <div style="{{ $hopStyle }} {{ $chuStyle }}">{{ \Illuminate\Support\Str::limit((string) $log->daura, 500) }}</div>
@else
    @switch($log->loai)
        @case('grammar')
            @if (! empty($duLieu['corrected']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Đoạn văn đã được sửa</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['corrected'] }}</p>
                </div>
            @endif

            <div style="{{ $mucStyle }}">
                <span style="{{ $nhanStyle }}">
                    Số lỗi phát hiện: {{ isset($duLieu['error_count']) ? (int) $duLieu['error_count'] : count($duLieu['errors'] ?? []) }}
                </span>
            </div>

            @if (! empty($duLieu['errors']) && is_array($duLieu['errors']))
                <ul style="{{ $danhSachStyle }}">
                    @foreach ($duLieu['errors'] as $loi)
                        @if (is_array($loi))
                            <li style="margin-bottom: 0.45rem;">
                                @if (! empty($loi['original']))
                                    <span style="color: #b91c1c; text-decoration: line-through;">{{ $loi['original'] }}</span>
                                @endif
                                @if (! empty($loi['replacement']))
                                    <span style="color: #047857; font-weight: 700;"> → {{ $loi['replacement'] }}</span>
                                @endif
                                @if (! empty($loi['explanation']))
                                    <div style="color: #475569; font-size: 0.9rem;">{{ $loi['explanation'] }}</div>
                                @endif
                            </li>
                        @elseif (is_scalar($loi))
                            <li>{{ $loi }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif

            @if (! empty($duLieu['overall_comment']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Nhận xét tổng quan</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['overall_comment'] }}</p>
                </div>
            @endif
            @break

        @case('translate')
            @if (! empty($duLieu['translation']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Bản dịch</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['translation'] }}</p>
                </div>
            @endif

            @if (! empty($duLieu['key_vocabulary']) && is_array($duLieu['key_vocabulary']))
                <div style="{{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Từ vựng cần nhớ</span>
                    <ul style="{{ $danhSachStyle }}">
                        @foreach ($duLieu['key_vocabulary'] as $tu)
                            @if (is_array($tu))
                                <li style="margin-bottom: 0.4rem;">
                                    <strong>{{ $tu['word'] ?? '' }}</strong>
                                    @if (! empty($tu['meaning']))
                                        – {{ $tu['meaning'] }}
                                    @endif
                                    @if (! empty($tu['example']))
                                        <div style="color: #475569; font-size: 0.9rem; font-style: italic;">{{ $tu['example'] }}</div>
                                    @endif
                                </li>
                            @elseif (is_scalar($tu))
                                <li>{{ $tu }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! empty($duLieu['grammar_notes']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Ghi chú ngữ pháp</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['grammar_notes'] }}</p>
                </div>
            @endif
            @break

        @case('rewrite')
            @if (! empty($duLieu['rewritten']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Bản viết lại gợi ý</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['rewritten'] }}</p>
                </div>
            @endif

            @if (! empty($duLieu['improvements']) && is_array($duLieu['improvements']))
                <div style="{{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Điểm đã cải thiện</span>
                    <ul style="{{ $danhSachStyle }}">
                        @foreach ($duLieu['improvements'] as $caiThien)
                            @if (is_array($caiThien))
                                <li style="margin-bottom: 0.4rem;">
                                    @if (! empty($caiThien['aspect']))
                                        <strong>{{ $caiThien['aspect'] }}:</strong>
                                    @endif
                                    {{ $caiThien['detail'] ?? '' }}
                                </li>
                            @elseif (is_scalar($caiThien))
                                <li>{{ $caiThien }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! empty($duLieu['explanation']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Vì sao cách viết này tốt hơn</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['explanation'] }}</p>
                </div>
            @endif
            @break

        @case('writing')
        @case('speaking')
            <div style="{{ $mucStyle }} display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="{{ $nhanStyle }} margin-bottom: 0;">{{ $log->loai === 'speaking' ? 'Điểm Speaking ước tính' : 'Điểm Writing ước tính' }}</span>
                <strong style="font-size: 1.9rem; color: #047857; line-height: 1;">
                    {{ isset($duLieu['score']) ? (int) $duLieu['score'] : '—' }}
                </strong>
                <span style="color: #64748b; font-size: 0.9rem;">/ 160</span>
            </div>

            @if (! empty($duLieu['feedback']))
                <div style="{{ $hopStyle }} {{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Nhận xét của giám khảo AI</span>
                    <p style="{{ $chuStyle }}">{{ $duLieu['feedback'] }}</p>
                </div>
            @endif

            @if (! empty($duLieu['subscores']) && is_array($duLieu['subscores']))
                <div style="{{ $mucStyle }}">
                    <span style="{{ $nhanStyle }}">Điểm thành phần</span>
                    <ul style="{{ $danhSachStyle }}">
                        @foreach ($duLieu['subscores'] as $tenDiem => $diem)
                            @if (is_array($diem))
                                <li>
                                    <strong>{{ $nhanKyNang[$diem['name'] ?? $tenDiem] ?? ($diem['name'] ?? $tenDiem) }}:</strong>
                                    {{ $diem['score'] ?? '—' }}
                                </li>
                            @else
                                <li>
                                    <strong>{{ $nhanKyNang[$tenDiem] ?? ucfirst((string) $tenDiem) }}:</strong>
                                    {{ is_scalar($diem) ? $diem : '—' }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
            @break

        @default
            <ul style="{{ $danhSachStyle }}">
                @foreach ($duLieu as $khoa => $giaTri)
                    @if (is_scalar($giaTri))
                        <li><strong>{{ $khoa }}:</strong> {{ $giaTri }}</li>
                    @elseif (is_array($giaTri))
                        <li><strong>{{ $khoa }}:</strong> {{ implode(', ', array_filter($giaTri, 'is_scalar')) }}</li>
                    @endif
                @endforeach
            </ul>
    @endswitch
@endif
