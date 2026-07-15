@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'fixed inset-y-0 right-0 z-[60] flex w-full max-w-md flex-col border-l admin-border admin-surface shadow-xl']) }} role="dialog" aria-modal="true" @if($title) aria-labelledby="admin-drawer-title" @endif>
    <div class="flex items-center justify-between border-b admin-border px-4 py-3">
        @if ($title)
            <h2 id="admin-drawer-title" class="text-sm font-semibold admin-text">{{ $title }}</h2>
        @endif
        {{ $header ?? '' }}
    </div>
    <div class="admin-scrollbar flex-1 overflow-y-auto p-4">{{ $slot }}</div>
    @if (isset($footer))
        <div class="border-t admin-border p-4">{{ $footer }}</div>
    @endif
</div>
