@php
    $events = $data['events'] ?? collect();
@endphp

@if ($events->isEmpty())
    <x-admin.empty-state title="Nothing scheduled" description="Offers, promotions, and discounts in this range will appear here." />
@else
    <ul class="divide-y admin-border">
        @foreach ($events as $event)
            <li class="flex items-center gap-3 py-2.5">
                <span class="flex size-9 shrink-0 flex-col items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium admin-text">{{ $event['title'] }}</p>
                    <p class="text-xs admin-muted">
                        {{ $event['starts_at'] }}@if ($event['ends_at']) – {{ $event['ends_at'] }}@endif
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <x-admin.badge variant="muted">{{ $event['type'] }}</x-admin.badge>
                    <x-admin.badge :variant="$event['active'] ? 'success' : 'muted'">{{ $event['active'] ? 'Active' : 'Off' }}</x-admin.badge>
                </div>
            </li>
        @endforeach
    </ul>
@endif
