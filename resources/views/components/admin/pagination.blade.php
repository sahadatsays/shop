@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 px-1 py-3">
        <p class="text-sm admin-muted">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <div class="flex gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-[var(--radius-admin)] px-3 py-1.5 text-sm admin-muted">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="rounded-[var(--radius-admin)] px-3 py-1.5 text-sm admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60">Prev</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="rounded-[var(--radius-admin)] px-3 py-1.5 text-sm admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60">Next</a>
            @else
                <span class="rounded-[var(--radius-admin)] px-3 py-1.5 text-sm admin-muted">Next</span>
            @endif
        </div>
    </nav>
@endif
