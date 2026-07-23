@php
    $announcements = $data['announcements'] ?? [];
@endphp

@if (empty($announcements))
    <x-admin.empty-state title="No announcements" description="Company news and release notes will appear here." />
@else
    <ul class="space-y-3">
        @foreach ($announcements as $announcement)
            <li class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold admin-text">{{ $announcement['title'] ?? 'Announcement' }}</p>
                    @if (! empty($announcement['type']))
                        <x-admin.badge variant="info">{{ $announcement['type'] }}</x-admin.badge>
                    @endif
                </div>
                @if (! empty($announcement['body']))
                    <p class="mt-1 text-xs admin-text-secondary">{{ $announcement['body'] }}</p>
                @endif
                @if (! empty($announcement['date']))
                    <p class="mt-1 text-[0.7rem] uppercase tracking-wide admin-muted">{{ $announcement['date'] }}</p>
                @endif
            </li>
        @endforeach
    </ul>
@endif
