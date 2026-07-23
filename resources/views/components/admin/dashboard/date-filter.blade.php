@props([
    'range',
    'options' => [],
    'from' => null,
    'to' => null,
])

<form
    method="GET"
    action="{{ route('admin.dashboard') }}"
    data-dashboard-date-filter
    class="flex flex-wrap items-center gap-2"
>
    <label class="sr-only" for="dashboard-range">Date range</label>
    <select
        id="dashboard-range"
        name="range"
        data-range-select
        class="admin-focus-ring rounded-[var(--radius-admin)] border admin-border admin-surface admin-text px-3 py-1.5 text-sm font-medium"
    >
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}" @selected($range->value === $option['value'])>{{ $option['label'] }}</option>
        @endforeach
    </select>

    <div data-custom-range @class(['flex items-center gap-2', 'hidden' => $range->value !== 'custom'])>
        <input type="date" name="from" value="{{ $from }}" class="admin-focus-ring rounded-[var(--radius-admin)] border admin-border admin-surface admin-text px-2.5 py-1.5 text-sm" aria-label="From date">
        <span class="text-xs admin-muted">to</span>
        <input type="date" name="to" value="{{ $to }}" class="admin-focus-ring rounded-[var(--radius-admin)] border admin-border admin-surface admin-text px-2.5 py-1.5 text-sm" aria-label="To date">
        <x-admin.button type="submit" size="sm" variant="secondary">Apply</x-admin.button>
    </div>
</form>
