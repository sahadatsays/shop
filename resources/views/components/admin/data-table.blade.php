<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface']) }}>
    @if (isset($header))
        <div class="border-b admin-border px-4 py-3">{{ $header }}</div>
    @endif
    <div class="overflow-x-auto admin-scrollbar">
        <table class="min-w-full text-left text-sm">
            {{ $slot }}
        </table>
    </div>
    @if (isset($footer))
        <div class="border-t admin-border px-4 py-3">{{ $footer }}</div>
    @endif
</div>
