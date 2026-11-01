@if ($paginator->hasPages())
    <nav class="admin-pagination" aria-label="Phân trang câu hỏi">
        @if ($paginator->onFirstPage())
            <span class="admin-page is-disabled">‹ Trước</span>
        @else
            <a class="admin-page" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Trước</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="admin-page is-disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="admin-page is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="admin-page" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="admin-page" href="{{ $paginator->nextPageUrl() }}" rel="next">Sau ›</a>
        @else
            <span class="admin-page is-disabled">Sau ›</span>
        @endif
    </nav>
@endif
