@if($paginator->hasPages())
<nav class="pagination" aria-label="Pagination">
    @if($paginator->onFirstPage())<span class="pagination-link disabled" aria-disabled="true"><span aria-hidden="true">←</span> Previous</span>@else<a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><span aria-hidden="true">←</span> Previous</a>@endif
    <strong><span class="sr-only">Current page:</span> {{ $paginator->currentPage() }} <span>of {{ $paginator->lastPage() }}</span></strong>
    @if($paginator->hasMorePages())<a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next <span aria-hidden="true">→</span></a>@else<span class="pagination-link disabled" aria-disabled="true">Next <span aria-hidden="true">→</span></span>@endif
</nav>
@endif
