@props([
    'title' => null,
])

<div
    {{ $attributes->class('school-data-table-cell-scroll') }}
    @if ($title !== null) title="{{ $title }}" @endif
>
    {{ $slot }}
</div>
