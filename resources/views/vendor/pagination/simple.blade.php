@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span class="page-link disabled" aria-label="Previous">&lsaquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-link" aria-label="Previous">&lsaquo;</a>
    @endif

    <span class="page-link" style="color:var(--gray-500)">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link" aria-label="Next">&rsaquo;</a>
    @else
        <span class="page-link disabled" aria-label="Next">&rsaquo;</span>
    @endif
</div>
@endif
