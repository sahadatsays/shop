@props([
    'title',
    'open' => false,
])

<details {{ $attributes->merge(['class' => 'group border-b border-navy-100']) }} @if ($open) open @endif>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 font-display text-base font-semibold text-navy-900 transition-colors duration-200 select-none hover:text-olive-700 [&::-webkit-details-marker]:hidden">
        {{ $title }}
        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-600 transition-transform duration-300 group-open:rotate-180" aria-hidden="true">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </span>
    </summary>
    <div class="pb-6 text-sm leading-relaxed text-navy-600">
        {{ $slot }}
    </div>
</details>
