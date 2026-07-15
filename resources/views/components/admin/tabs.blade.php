@props([
    'tabs' => [],
    'active' => null,
])

<div {{ $attributes->merge(['class' => 'border-b admin-border']) }} role="tablist" aria-label="Section tabs">
    <div class="flex gap-1 overflow-x-auto admin-scrollbar">
        @foreach ($tabs as $tab)
            @php $isActive = ($active ?? $tabs[0]['id'] ?? null) === $tab['id']; @endphp
            <a href="{{ $tab['href'] ?? '#' }}"
               role="tab"
               @if($isActive) aria-selected="true" @else aria-selected="false" @endif
               @class([
                   'whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium admin-focus-ring',
                   'border-admin-accent admin-text' => $isActive,
                   'border-transparent admin-muted hover:admin-text-secondary' => ! $isActive,
               ])>
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
