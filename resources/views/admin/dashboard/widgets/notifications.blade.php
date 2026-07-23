@php
    $notifications = $data['notifications'] ?? collect();
    $unread = $data['unread'] ?? 0;
@endphp

<div class="mb-3 flex items-center justify-between">
    <p class="text-xs font-medium admin-muted">
        @if ($unread > 0)
            <span class="font-semibold text-admin-brand">{{ $unread }}</span> unread
        @else
            You're all caught up
        @endif
    </p>
    <a href="{{ route('admin.notifications.index') }}" class="text-xs font-medium admin-text-secondary hover:text-admin-brand">View all</a>
</div>

@if ($notifications->isEmpty())
    <x-admin.empty-state title="No notifications" description="Alerts about orders, stock, and system events appear here." />
@else
    <ul class="divide-y admin-border">
        @foreach ($notifications as $notification)
            <li class="flex items-start gap-3 py-2.5" data-notification-id="{{ $notification['id'] }}">
                <span @class(['mt-1.5 size-2 shrink-0 rounded-full', 'bg-admin-brand' => ! $notification['read'], 'bg-admin-border' => $notification['read']])></span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium admin-text">{{ $notification['title'] }}</p>
                    @if ($notification['body'])
                        <p class="truncate text-xs admin-muted">{{ $notification['body'] }}</p>
                    @endif
                    <p class="mt-0.5 text-[0.7rem] uppercase tracking-wide admin-muted">{{ $notification['category'] }} · {{ $notification['time'] }}</p>
                </div>
                @if ($notification['action_url'])
                    <a href="{{ $notification['action_url'] }}" class="shrink-0 text-xs font-medium admin-text-secondary hover:text-admin-brand">{{ $notification['action_label'] ?: 'Open' }}</a>
                @endif
            </li>
        @endforeach
    </ul>
@endif
