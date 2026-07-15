@props([
    'item',
    'currentRoute' => null,
])

@php
    $hasActiveChild = $item->isActive($currentRoute);
    $expanded = $hasActiveChild;
@endphp

<li data-nav-group>
    <button type="button"
            data-nav-group-toggle
            aria-expanded="{{ $expanded ? 'true' : 'false' }}"
            @class([
                'flex w-full items-center gap-3 rounded-[var(--radius-admin)] px-3 py-2.5 text-sm font-medium admin-focus-ring transition-colors',
                'bg-admin-accent-muted admin-text' => $hasActiveChild,
                'admin-text-secondary hover:bg-admin-accent-muted/60 hover:admin-text' => ! $hasActiveChild,
            ])>
        @if ($item->icon)
            <svg class="size-[1.125rem] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="{{ $item->icon }}"/>
            </svg>
        @endif
        <span class="truncate [[data-sidebar-collapsed=true]_&]:lg:hidden">{{ $item->label }}</span>
        <svg class="ml-auto size-4 shrink-0 admin-muted transition-transform [[data-sidebar-collapsed=true]_&]:lg:hidden [[aria-expanded=true]_&]:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="m9 18 6-6-6-6"/>
        </svg>
    </button>

    <ul data-nav-group-panel @if(! $expanded) hidden @endif class="mt-1 space-y-0.5 pl-9 [[data-sidebar-collapsed=true]_&]:lg:hidden">
        @foreach ($item->children as $child)
            <li>
                <x-admin.nav-item :item="$child" :current-route="$currentRoute" />
            </li>
        @endforeach
    </ul>
</li>
