@props([
    'label',
    'name',
    'preview' => 'imagePreview',
    'current' => null,
    'help' => 'PNG, JPG or WEBP up to 2MB.',
    'aspect' => 'square',
    'required' => false,
])

@php
    $hasError = $errors->has($name);
    $aspectClass = match ($aspect) {
        'wide' => 'aspect-[16/7]',
        'banner' => 'aspect-[21/9]',
        default => 'aspect-square max-h-64',
    };
@endphp

<x-admin.field :label="$label" :name="$name" :help="$hasError ? null : $help" :required="$required">
    <div class="relative overflow-hidden rounded-[var(--radius-admin-lg)] border-2 border-dashed transition-colors duration-150 {{ $hasError ? 'border-admin-danger bg-red-50/30 dark:bg-red-950/10' : 'admin-border bg-admin-bg hover:border-admin-brand/40' }}">
        <div class="{{ $aspectClass }} relative w-full">
            {{-- Preview / current --}}
            <img
                x-show="{{ $preview }} || @js((bool) $current)"
                x-cloak
                :src="{{ $preview }} || @js($current)"
                alt=""
                class="absolute inset-0 size-full object-cover"
            >

            {{-- Placeholder --}}
            <div
                x-show="! ({{ $preview }} || @js((bool) $current))"
                class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-4 text-center"
            >
                <span class="flex size-11 items-center justify-center rounded-full bg-admin-accent-muted text-admin-brand">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14M14 8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-medium admin-text">Click to upload</p>
                    <p class="mt-0.5 text-xs admin-muted">or drag and drop</p>
                </div>
            </div>

            {{-- Hover change hint --}}
            <div
                x-show="{{ $preview }} || @js((bool) $current)"
                x-cloak
                class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/55 to-transparent px-3 pb-3 pt-8 text-center"
            >
                <span class="inline-flex rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold text-slate-800 shadow-sm">Change image</span>
            </div>
        </div>

        <input
            type="file"
            name="{{ $name }}"
            accept="image/png,image/jpeg,image/webp,image/gif"
            @change="previewFile($event, '{{ $preview }}')"
            class="absolute inset-0 z-10 cursor-pointer opacity-0"
            aria-label="{{ $label }}"
        >
    </div>
</x-admin.field>
