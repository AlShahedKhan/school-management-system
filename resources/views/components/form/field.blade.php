@props([
    'label',
    'for' => null,
])

<div {{ $attributes->class(['col-span-1']) }}>
    <label
        @if ($for) for="{{ $for }}" @endif
        class="mb-1.5 block text-[10px] capitalize tracking-normal text-gray-500"
    >
        {{ $label }}
    </label>

    {{ $slot }}
</div>
