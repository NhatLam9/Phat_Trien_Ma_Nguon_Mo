@if ($paginator->hasPages())
    <div class="det-pagination-wrapper" style="display: flex; flex-direction: column; align-items: flex-start; gap: 8px; margin-top: 24px; margin-bottom: 24px;">
        @if(method_exists($paginator, 'firstItem') && $paginator->total() > 0)
            <div style="font-size: 13px; color: #64748b; font-weight: 500;">
                Hiển thị từ <strong style="color: #1e293b;">{{ $paginator->firstItem() }}</strong> đến <strong style="color: #1e293b;">{{ $paginator->lastItem() }}</strong> trong tổng số <strong style="color: #1e293b;">{{ $paginator->total() }}</strong> kết quả
            </div>
        @endif

        <nav class="det-pagination" role="navigation" aria-label="Phân trang" style="display: inline-flex; align-items: center; gap: 5px; flex-wrap: wrap;">
            {{-- Nút Trang trước --}}
            @if ($paginator->onFirstPage())
                <span class="det-page-item is-disabled" style="display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 12px; font-size: 13px; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; cursor: not-allowed; user-select: none;">
                    ‹ Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="det-page-item" style="display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 12px; font-size: 13px; font-weight: 600; color: #334155; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; transition: all 0.15s;">
                    ‹ Trước
                </a>
            @endif

            {{-- Danh sách các số trang --}}
            @foreach ($elements as $element)
                {{-- Dấu "..." --}}
                @if (is_string($element))
                    <span class="det-page-item is-disabled" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 8px; font-size: 13px; color: #94a3b8;">
                        {{ $element }}
                    </span>
                @endif

                {{-- Mảng các trang --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="det-page-item is-active" aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; font-size: 13px; font-weight: 700; color: #ffffff; background: #00b86b; border: 1px solid #00b86b; border-radius: 6px; user-select: none;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="det-page-item" style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; font-size: 13px; font-weight: 600; color: #334155; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; transition: all 0.15s;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Nút Trang sau --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="det-page-item" style="display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 12px; font-size: 13px; font-weight: 600; color: #334155; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; transition: all 0.15s;">
                    Sau ›
                </a>
            @else
                <span class="det-page-item is-disabled" style="display: inline-flex; align-items: center; justify-content: center; height: 34px; padding: 0 12px; font-size: 13px; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; cursor: not-allowed; user-select: none;">
                    Sau ›
                </span>
            @endif
        </nav>
    </div>
@endif

