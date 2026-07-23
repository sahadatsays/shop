@php
    $metrics = $data['metrics'] ?? [];
@endphp

@if (empty($metrics))
    <x-admin.empty-state title="No metrics" description="No figures available for this range." />
@else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($metrics as $metric)
            <div class="rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium admin-muted">{{ $metric['label'] }}</p>
                        <p class="mt-1.5 text-xl font-semibold tracking-tight admin-text" data-widget-metric>{{ $metric['value'] }}</p>
                        @if (! empty($metric['change']))
                            <p @class([
                                'mt-1 flex items-center gap-1 text-xs font-medium',
                                'text-admin-success' => ($metric['trend'] ?? 'neutral') === 'up',
                                'text-admin-danger' => ($metric['trend'] ?? 'neutral') === 'down',
                                'admin-muted' => ($metric['trend'] ?? 'neutral') === 'neutral',
                            ])>
                                @if (($metric['trend'] ?? '') === 'up')
                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                                @elseif (($metric['trend'] ?? '') === 'down')
                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                @endif
                                {{ $metric['change'] }}
                            </p>
                        @endif
                    </div>
                    @if (! empty($metric['icon']))
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $metric['icon'] }}"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
