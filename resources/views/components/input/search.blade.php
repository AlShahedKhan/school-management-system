@props([
    'placeholder' => 'Search...',
    'type' => 'text',
])

<div {{ $attributes->only('class')->class(['relative']) }}>
    <i
        class="mdi mdi-magnify pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"
        aria-hidden="true"
    ></i>

    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->except('class')->class([
            'h-8 w-full border border-slate-300 bg-white py-1 pl-8 pr-3',
            'text-[10px] text-slate-700 placeholder:text-slate-400',
            'outline-none transition-colors focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
        ]) }}
    >
</div>
