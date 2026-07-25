@props([
    'type' => 'button',
    'size' => 'sm',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'h-8 px-4 text-[10px]',
        'md' => 'h-10 px-4 text-[12px] sm:h-9',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'flex items-center justify-center whitespace-nowrap rounded-none',
        'border border-slate-300 bg-white text-slate-600',
        'transition-colors hover:bg-slate-50',
        $sizeClasses,
    ]) }}
>
    {{ $slot }}
</button>
