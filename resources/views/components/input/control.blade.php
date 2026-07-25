@props([
    'type' => 'text',
])

<input
    type="{{ $type }}"
    {{ $attributes->class([
        'form-input-fixed h-8 w-full border border-gray-200 px-3 text-xs',
    ])->merge([
        'style' => 'border-radius: 0;',
    ]) }}
>
