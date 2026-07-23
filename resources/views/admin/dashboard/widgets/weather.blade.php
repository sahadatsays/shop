@php
    $configured = $data['configured'] ?? false;
    $location = $data['location'] ?? 'Set a location';
@endphp

<div class="flex items-center gap-4">
    <span class="flex size-12 items-center justify-center rounded-full bg-admin-accent-muted text-admin-brand">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M17 18a5 5 0 0 0-10 0M12 2v2M4.9 6.3l1.4 1.4M2 13h2M20 13h2M17.7 7.7l1.4-1.4M7 18a5 5 0 0 1 10 0"/></svg>
    </span>
    <div>
        <p class="text-sm font-semibold admin-text">{{ $location }}</p>
        @if ($configured)
            <p class="text-xs admin-muted">Live weather for delivery planning.</p>
        @else
            <p class="text-xs admin-muted">Add a weather API key in config to enable live conditions.</p>
        @endif
    </div>
</div>
