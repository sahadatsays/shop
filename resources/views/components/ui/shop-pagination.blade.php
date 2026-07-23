@props(['paginator'])

@if ($paginator->hasPages())
    <nav {{ $attributes->merge(['class' => 'mt-14 flex items-center justify-center gap-1.5']) }} aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true"
                  class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-300">
                <span class="sr-only">Previous page</span>
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">
                <span class="sr-only">Previous page</span>
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
            </a>
        @endif

        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span aria-current="page"
                      class="flex size-11 items-center justify-center rounded-xl bg-navy-900 text-sm font-semibold text-white shadow-soft">{{ $page }}</span>
            @else
                <a href="{{ $url }}"
                   class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-sm font-semibold text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-700 transition-all duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white">
                <span class="sr-only">Next page</span>
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
            </a>
        @else
            <span aria-disabled="true"
                  class="flex size-11 items-center justify-center rounded-xl border border-navy-200 text-navy-300">
                <span class="sr-only">Next page</span>
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
            </span>
        @endif
    </nav>
@endif
