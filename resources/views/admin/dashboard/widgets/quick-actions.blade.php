@php
    $actions = $data['actions'] ?? [];
@endphp

@if (empty($actions))
    <x-admin.empty-state title="No quick actions" description="You don't have permission for any quick actions." />
@else
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($actions as $action)
            @if ($action['href'])
                <a href="{{ $action['href'] }}" class="group flex items-start gap-3 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-4 text-left transition-all duration-200 admin-focus-ring hover:border-admin-brand/40 hover:shadow-md">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand transition-colors duration-200 group-hover:bg-admin-brand group-hover:text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $action['icon'] }}"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold admin-text">{{ $action['label'] }}</span>
                        <span class="mt-0.5 block text-xs admin-muted">{{ $action['description'] }}</span>
                    </span>
                </a>
            @else
                <button type="button" data-quick-action="{{ $action['label'] }}" class="group flex items-start gap-3 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-4 text-left transition-all duration-200 admin-focus-ring hover:border-admin-brand/40 hover:shadow-md">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand transition-colors duration-200 group-hover:bg-admin-brand group-hover:text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $action['icon'] }}"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold admin-text">{{ $action['label'] }}</span>
                        <span class="mt-0.5 block text-xs admin-muted">{{ $action['description'] }}</span>
                    </span>
                </button>
            @endif
        @endforeach
    </div>
@endif
