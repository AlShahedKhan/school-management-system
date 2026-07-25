@props([
    'id',
    'panelClass',
    'panelStyle' => null,
])

<div id="{{ $id }}" {{ $attributes }}>
    <div class="{{ $panelClass }}" @if ($panelStyle) style="{{ $panelStyle }}" @endif>
        {{ $slot }}
    </div>
</div>
