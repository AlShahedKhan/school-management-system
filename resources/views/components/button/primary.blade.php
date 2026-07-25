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
        'border-2 border-blue-600 bg-blue-600 text-white',
        'transition-colors hover:bg-blue-700',
        $sizeClasses,
    ]) }}
>
    {{ $slot }}
</button>
