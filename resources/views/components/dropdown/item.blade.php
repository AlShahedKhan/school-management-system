@props([
    'type' => 'button',
])

<button
    type="{{ $type }}"
    role="menuitem"
    {{ $attributes->class([
        'flex h-7 w-full items-center whitespace-nowrap px-3 text-left text-[10px] text-slate-700',
        'transition-colors hover:bg-slate-50 focus:bg-slate-50 focus:outline-none',
    ]) }}
>
    {{ $slot }}
</button>
