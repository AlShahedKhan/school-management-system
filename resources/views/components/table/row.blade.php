@props([
    'unstyled' => false,
])

<tr
    {{ $attributes->class([
        'transition-colors hover:bg-gray-50' => ! $unstyled,
    ]) }}
>
    {{ $slot }}
</tr>
