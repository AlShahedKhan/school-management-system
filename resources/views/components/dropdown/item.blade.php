@props([
    'type' => 'button',
])

<button type="{{ $type }}" role="menuitem"
    {{ $attributes->class([
        'flex h-6 w-full items-center whitespace-nowrap px-1.5 text-left font-medium tracking-tight text-slate-700',
        'transition-colors hover:bg-slate-50 focus:bg-slate-50 focus:outline-none',
    ]) }}>
    {{ $slot }}
</button>
