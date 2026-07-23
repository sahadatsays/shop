@php
    $chart = $data['chart'] ?? ['type' => 'bar', 'labels' => [], 'series' => []];
    $chartId = 'chart-'.$widget->key();
    $payload = [
        'id' => $chartId,
        'title' => $widget->name(),
        'type' => $chart['type'] ?? 'bar',
        'labels' => $chart['labels'] ?? [],
        'series' => $chart['series'] ?? [],
    ];
    $hasData = collect($chart['series'] ?? [])
        ->flatMap(fn ($series) => $series['data'] ?? [])
        ->contains(fn ($value) => (float) $value > 0);
@endphp

@if (! $hasData)
    <x-admin.empty-state title="No data yet" description="Nothing to chart for the selected range." />
@else
    <div data-widget-chart='@json($payload)'>
        <div id="{{ $chartId }}" class="min-h-[260px] w-full" role="img" aria-label="{{ $widget->name() }} chart"></div>
    </div>
@endif
