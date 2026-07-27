@props([
    'type' => 'button',
    'variant',
    'label',
])

@php
    [$variantClasses, $iconClasses] = match ($variant) {
        'view' => [
            'hover:bg-gray-100 hover:text-emerald-600 focus-visible:ring-emerald-500',
            'far fa-eye text-sm',
        ],
        'edit' => [
            'hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500',
            'far fa-edit text-sm',
        ],
        'delete' => [
            'hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500',
            'far fa-trash-alt text-sm',
        ],
        'status' => [
            'hover:bg-gray-100 hover:text-slate-700 focus-visible:ring-slate-500',
            'fas fa-toggle-on text-sm',
        ],
    };
@endphp

<button
    type="{{ $type }}"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    {{ $attributes->class([
        'flex h-8 w-7 items-center justify-center text-gray-600 transition-colors',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1',
        $variantClasses,
    ]) }}
>
    <i class="{{ $iconClasses }}" aria-hidden="true"></i>
</button>
