@props([
    'tabs' => [],
])

<div {{ $attributes->merge(['class' => 'inline-flex max-w-full overflow-x-auto rounded-[var(--radius-admin)] border admin-border bg-admin-bg p-1 admin-scrollbar']) }} role="tablist">
    @foreach ($tabs as $tab)
        @php $active = $tab['active'] ?? false; @endphp
        <a
            href="{{ $tab['href'] }}"
            role="tab"
            @if($active) aria-selected="true" @endif
            @class([
                'shrink-0 rounded-[calc(var(--radius-admin)-2px)] px-3.5 py-1.5 text-xs font-medium transition-colors duration-150 admin-focus-ring sm:text-sm',
                'bg-admin-surface admin-text shadow-sm' => $active,
                'admin-muted hover:admin-text-secondary' => ! $active,
            ])
        >
            {{ $tab['label'] }}
            @if (isset($tab['count']))
                <span @class(['ml-1 tabular-nums', 'admin-muted' => ! $active])>{{ $tab['count'] }}</span>
            @endif
        </a>
    @endforeach
</div>
