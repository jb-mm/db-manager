@if ($paginator->hasPages())
    <nav class="{{ $isBootstrap ? '' : 'flex items-center justify-center gap-2 text-sm' }}" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="{{ $isBootstrap ? 'btn btn-sm btn-outline-secondary disabled' : 'px-3 py-1 rounded border border-gray-200 text-gray-400' }}">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Prev</a>
        @endif

        <span class="{{ $isBootstrap ? 'mx-2 small text-muted' : 'text-gray-600' }}">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Next</a>
        @else
            <span class="{{ $isBootstrap ? 'btn btn-sm btn-outline-secondary disabled' : 'px-3 py-1 rounded border border-gray-200 text-gray-400' }}">Next</span>
        @endif
    </nav>
@endif
