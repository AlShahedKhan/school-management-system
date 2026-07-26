@props([
    'label',
    'value',
    'icon',
    'iconStyle',
])

<div
    {{ $attributes->class([
        'flex h-[72px] items-center border border-slate-200 bg-white px-[14px] shadow-sm',
        'transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md',
        'md:h-[76px] xl:h-20',
    ]) }}
>
    <div class="{{ $iconStyle }} mr-2 flex h-9 w-8 shrink-0 items-center justify-center rounded-sm">
        <i class="fas {{ $icon }} text-[15px]" aria-hidden="true"></i>
    </div>

    <div class="flex min-w-0 flex-col justify-center gap-0">
        <span class="block truncate text-xs font-medium leading-4 text-slate-600">
            {{ $label }}
        </span>
        <span class="block truncate text-xs font-bold leading-4 tracking-wide text-slate-950">
            {{ $value }}
        </span>
    </div>
</div>
