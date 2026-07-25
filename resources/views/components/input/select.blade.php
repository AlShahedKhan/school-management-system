<div class="relative">
    <select
        {{ $attributes->class([
            'form-input-fixed h-9 w-full appearance-none border border-slate-300 bg-white px-3 pr-9',
            'text-[10px] text-slate-700 outline-none transition-colors',
            'focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
        ])->merge([
            'style' => 'border-radius: 0;',
        ]) }}
    >
        {{ $slot }}
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400" aria-hidden="true">
        <i class="fas fa-chevron-down text-[10px]"></i>
    </span>
</div>
