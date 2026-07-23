@php
    $events = $data['events'] ?? collect();
@endphp

@if ($events->isEmpty())
    <x-admin.empty-state title="No activity" description="System and business events will show up here." />
@else
    <ol class="relative space-y-4 border-l admin-border pl-5">
        @foreach ($events as $event)
            <li class="relative">
                <span class="absolute -left-[1.42rem] top-1 flex size-3 items-center justify-center rounded-full border-2 border-admin-surface bg-admin-brand"></span>
                <p class="text-sm admin-text">{{ $event['description'] }}</p>
                <p class="mt-0.5 text-xs admin-muted">
                    {{ $event['causer'] }}@if ($event['subject']) · {{ $event['subject'] }}@endif · {{ $event['created'] }}
                </p>
            </li>
        @endforeach
    </ol>
@endif
