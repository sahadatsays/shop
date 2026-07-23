@props([
    'paginator',
])

@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="Pagination" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs admin-muted sm:text-sm">
            Showing
            <span class="font-medium admin-text-secondary">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-medium admin-text-secondary">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-medium admin-text-secondary">{{ $paginator->total() }}</span>
            results
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 cursor-not-allowed items-center rounded-[var(--radius-admin)] border admin-border px-3 text-xs admin-muted">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-9 items-center rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 text-xs font-medium admin-text-secondary transition-colors admin-focus-ring hover:bg-admin-accent-muted/50">Previous</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span class="inline-flex size-9 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent text-xs font-semibold text-white dark:text-admin-bg" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="inline-flex size-9 items-center justify-center rounded-[var(--radius-admin)] border admin-border bg-admin-surface text-xs font-medium admin-text-secondary transition-colors admin-focus-ring hover:bg-admin-accent-muted/50">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-9 items-center rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 text-xs font-medium admin-text-secondary transition-colors admin-focus-ring hover:bg-admin-accent-muted/50">Next</a>
            @else
                <span class="inline-flex h-9 cursor-not-allowed items-center rounded-[var(--radius-admin)] border admin-border px-3 text-xs admin-muted">Next</span>
            @endif
        </div>
    </nav>
@endif
