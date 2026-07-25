@props([
    'rows' => 4,
])

<textarea
    rows="{{ $rows }}"
    {{ $attributes->class([
        'form-input-fixed w-full resize-none border border-gray-200 px-3 py-2 text-xs text-slate-700 outline-none',
        'transition-colors focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
    ])->merge([
        'style' => 'border-radius: 0;',
    ]) }}
>{{ $slot }}</textarea>
