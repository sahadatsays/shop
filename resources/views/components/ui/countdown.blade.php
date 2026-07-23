@props([
    'endsAt',
])

@php
    $endsAtIso = $endsAt instanceof \DateTimeInterface
        ? $endsAt->toIso8601String()
        : (string) $endsAt;
@endphp

<div
    x-data="{
        endsAt: new Date(@js($endsAtIso)).getTime(),
        days: '00',
        hours: '00',
        minutes: '00',
        seconds: '00',
        expired: false,
        tick() {
            const remaining = this.endsAt - Date.now();

            if (remaining <= 0) {
                this.expired = true;
                this.days = this.hours = this.minutes = this.seconds = '00';
                return;
            }

            this.days = String(Math.floor(remaining / 86400000)).padStart(2, '0');
            this.hours = String(Math.floor((remaining % 86400000) / 3600000)).padStart(2, '0');
            this.minutes = String(Math.floor((remaining % 3600000) / 60000)).padStart(2, '0');
            this.seconds = String(Math.floor((remaining % 60000) / 1000)).padStart(2, '0');
        },
    }"
    x-init="tick(); setInterval(() => tick(), 1000)"
    {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}
    role="timer"
    aria-live="polite"
>
    @foreach (['days' => 'Days', 'hours' => 'Hrs', 'minutes' => 'Min', 'seconds' => 'Sec'] as $unit => $label)
        <div class="flex flex-col items-center rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/10 backdrop-blur-sm">
            <span class="font-display text-xl font-bold tabular-nums text-white" x-text="{{ $unit }}">{{ $unit === 'days' ? '00' : '00' }}</span>
            <span class="text-[10px] font-semibold tracking-wider text-navy-300 uppercase">{{ $label }}</span>
        </div>
        @if ($unit !== 'seconds')
            <span class="font-display text-lg font-bold text-bronze-400" aria-hidden="true">:</span>
        @endif
    @endforeach
</div>
