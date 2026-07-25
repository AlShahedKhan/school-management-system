@props([
    'unstyled' => false,
])

<th
    {{ $attributes->class([
        'px-6 py-3 font-medium' => ! $unstyled,
    ]) }}
>
    {{ $slot }}
</th>
