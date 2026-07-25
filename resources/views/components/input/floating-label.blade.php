@props([
    'for',
    'floating' => true,
])

<label
    for="{{ $for }}"
    {{ $attributes->class([
        'absolute -top-1.5 left-2 bg-white px-1 text-[10px] leading-4 text-slate-500',
        'pointer-events-none transition-all duration-150 peer-placeholder-shown:left-3 peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-xs peer-focus:-top-1.5 peer-focus:left-2 peer-focus:translate-y-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[10px] peer-focus:text-blue-600' => $floating,
        'peer-focus:text-blue-600' => ! $floating,
    ]) }}
>
    {{ $slot }}
</label>
