@props([
    'item',
    'currentRoute' => null,
])

@php
    $active = $item->isActive($currentRoute);
    $href = $item->href();
    $disabled = $item->disabled || $href === null;
@endphp

@if ($disabled)
    <span @class([
        'group flex items-center gap-3 rounded-[var(--radius-admin)] px-3 py-2.5 text-sm font-medium admin-muted cursor-not-allowed',
        'bg-admin-accent-muted/40 admin-text' => $active,
    ])
          title="{{ $item->label }}">
        @if ($item->icon)
            <svg class="size-[1.125rem] shrink-0 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="{{ $item->icon }}"/>
            </svg>
        @endif
        <span class="truncate [[data-sidebar-collapsed=true]_&]:lg:hidden">{{ $item->label }}</span>
        @if ($item->badge)
            <x-admin.badge variant="muted" class="ml-auto [[data-sidebar-collapsed=true]_&]:lg:hidden">{{ $item->badge }}</x-admin.badge>
        @endif
    </span>
@else
    <a href="{{ $href }}"
       @class([
           'group flex items-center gap-3 rounded-[var(--radius-admin)] px-3 py-2.5 text-sm font-medium admin-focus-ring transition-all duration-150',
           'bg-admin-accent-muted admin-text shadow-sm' => $active,
           'admin-text-secondary hover:bg-admin-accent-muted/60 hover:admin-text hover:translate-x-0.5' => ! $active,
       ])
       @if($active) aria-current="page" @endif
       title="{{ $item->label }}">
        @if ($item->icon)
            <svg class="size-[1.125rem] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="{{ $item->icon }}"/>
            </svg>
        @endif
        <span class="truncate [[data-sidebar-collapsed=true]_&]:lg:hidden">{{ $item->label }}</span>
    </a>
@endif
