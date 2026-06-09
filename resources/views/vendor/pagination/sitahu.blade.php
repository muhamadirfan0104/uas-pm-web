@if ($paginator->hasPages())
    <nav class="sitahu-pagination" role="navigation" aria-label="Navigasi halaman">
        <div class="sitahu-page-info">
            <span>Menampilkan</span>
            <strong>{{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}</strong>
            <span>dari</span>
            <strong>{{ $paginator->total() }}</strong>
            <span>data</span>
        </div>

        <ul class="sitahu-page-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="sitahu-page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                    <span class="sitahu-page-link sitahu-page-arrow"><i class="bi bi-chevron-left"></i></span>
                </li>
            @else
                <li class="sitahu-page-item">
                    <a class="sitahu-page-link sitahu-page-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="sitahu-page-item disabled" aria-disabled="true"><span class="sitahu-page-link dots">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="sitahu-page-item active" aria-current="page"><span class="sitahu-page-link">{{ $page }}</span></li>
                        @else
                            <li class="sitahu-page-item"><a class="sitahu-page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="sitahu-page-item">
                    <a class="sitahu-page-link sitahu-page-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a>
                </li>
            @else
                <li class="sitahu-page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                    <span class="sitahu-page-link sitahu-page-arrow"><i class="bi bi-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
