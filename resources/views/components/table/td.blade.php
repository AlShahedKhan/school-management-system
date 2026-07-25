@props([
    'unstyled' => false,
])

<td
    {{ $attributes->class([
        'whitespace-nowrap px-6 py-4 text-gray-700' => ! $unstyled,
    ]) }}
>
    {{ $slot }}
</td>
