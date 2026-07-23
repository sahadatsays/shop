<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface shadow-sm']) }}>
    @if (isset($header))
        <div class="border-b admin-border bg-admin-bg/30 px-4 py-3.5 sm:px-6">{{ $header }}</div>
    @endif

    @if (isset($toolbar))
        <div class="border-b admin-border bg-admin-bg/30 px-4 py-3.5 sm:px-6">{{ $toolbar }}</div>
    @endif

    @if (isset($mobile))
        <div class="p-4 md:hidden">{{ $mobile }}</div>
    @endif

    <div @class(['hidden overflow-x-auto admin-scrollbar md:block', 'border-t admin-border' => isset($mobile)])>
        <table class="min-w-full text-left text-sm">
            {{ $slot }}
        </table>
    </div>

    @if (isset($footer))
        <div class="border-t admin-border bg-admin-bg/20 px-4 py-3.5 sm:px-6">{{ $footer }}</div>
    @endif
</div>
