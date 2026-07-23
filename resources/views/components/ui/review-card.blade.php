@props([
    'author',
    'role' => null,
    'rating' => 5,
    'body',
    'initials' => null,
])

<article {{ $attributes->merge(['class' => 'flex w-[85%] shrink-0 snap-start flex-col rounded-card bg-surface p-8 shadow-card sm:w-[46%] lg:w-[31.5%]']) }}>
    <x-ui.rating :value="$rating" />
    <p class="mt-5 flex-1 text-base leading-relaxed text-navy-700">&ldquo;{{ $body }}&rdquo;</p>
    <footer class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
        <span class="flex size-11 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-bronze-400">
            {{ $initials ?: mb_substr($author, 0, 1) }}
        </span>
        <div>
            <p class="text-sm font-semibold text-navy-900">{{ $author }}</p>
            @if ($role)
                <p class="text-xs text-navy-500">{{ $role }}</p>
            @endif
        </div>
    </footer>
</article>
