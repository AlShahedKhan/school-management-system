@props([
    'buttonId',
    'menuId',
    'label' => 'Options',
    'align' => 'left',
    'variant' => 'secondary',
])

@php
    $alignmentClass = match($align) {
        'right' => 'right-0 left-auto w-max',
        'full'  => 'left-0 right-0 w-full',
        default => 'left-0 right-auto min-w-full w-max',
    };
@endphp

<div {{ $attributes->class(['relative w-full lg:w-auto']) }}>
    @if ($variant === 'primary')
        <x-button.primary
            id="{{ $buttonId }}"
            aria-haspopup="menu"
            aria-expanded="false"
            aria-controls="{{ $menuId }}"
            class="w-full min-w-0 justify-center gap-1"
        >
            <span>{{ $label }}</span>
            <i class="mdi mdi-chevron-down text-xs" aria-hidden="true"></i>
        </x-button.primary>
    @else
        <x-button.secondary
            id="{{ $buttonId }}"
            aria-haspopup="menu"
            aria-expanded="false"
            aria-controls="{{ $menuId }}"
            class="w-full min-w-0 justify-center gap-1"
        >
            <span>{{ $label }}</span>
            <i class="mdi mdi-chevron-down text-xs" aria-hidden="true"></i>
        </x-button.secondary>
    @endif

    <div
        id="{{ $menuId }}"
        role="menu"
        class="absolute {{ $alignmentClass }} z-50 mt-1 hidden max-h-[180px] max-w-[calc(100vw-2rem)] overflow-y-auto border border-slate-200 bg-white shadow-lg"
    >
        {{ $slot }}
    </div>
</div>
