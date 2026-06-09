@if ($paginator->hasPages())
    <nav class="sitahu-pagination" role="navigation" aria-label="Navigasi halaman">
        <div class="sitahu-page-info">
            <span>Halaman</span>
            <strong>{{ $paginator->currentPage() }}</strong>
        </div>
        <ul class="sitahu-page-list">
            @if ($paginator->onFirstPage())
                <li class="sitahu-page-item disabled" aria-disabled="true"><span class="sitahu-page-link sitahu-page-arrow"><i class="bi bi-chevron-left"></i></span></li>
            @else
                <li class="sitahu-page-item"><a class="sitahu-page-link sitahu-page-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a></li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="sitahu-page-item"><a class="sitahu-page-link sitahu-page-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a></li>
            @else
                <li class="sitahu-page-item disabled" aria-disabled="true"><span class="sitahu-page-link sitahu-page-arrow"><i class="bi bi-chevron-right"></i></span></li>
            @endif
        </ul>
    </nav>
@endif
