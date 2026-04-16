@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span class="page-link disabled"><i class="bi bi-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-link"><i class="bi bi-chevron-left"></i></a>
    @endif

    <span class="page-link" style="color:var(--gray-500)">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link"><i class="bi bi-chevron-right"></i></a>
    @else
        <span class="page-link disabled"><i class="bi bi-chevron-right"></i></span>
    @endif
</div>
@endif
