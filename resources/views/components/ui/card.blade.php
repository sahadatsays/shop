@props(['hover' => false])

<div {{ $attributes->merge([
    'class' => 'rounded-card bg-surface shadow-card '.($hover ? 'transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-card-hover' : ''),
]) }}>
    {{ $slot }}
</div>
