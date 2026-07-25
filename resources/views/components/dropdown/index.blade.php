@props([
    'buttonId',
    'menuId',
    'label' => 'Options',
])

<div {{ $attributes->class(['relative w-full lg:w-auto']) }}>
    <button
        type="button"
        id="{{ $buttonId }}"
        aria-haspopup="menu"
        aria-expanded="false"
        aria-controls="{{ $menuId }}"
        class="flex h-8 w-full min-w-0 items-center justify-center gap-1 whitespace-nowrap border border-slate-300 bg-white px-4 text-[10px] text-slate-600 transition-colors hover:bg-slate-50"
    >
        <span>{{ $label }}</span>
        <i class="mdi mdi-chevron-down text-xs" aria-hidden="true"></i>
    </button>

    <div
        id="{{ $menuId }}"
        role="menu"
        class="absolute left-0 right-auto z-50 mt-2 hidden max-h-[120px] w-full max-w-[calc(100vw-2rem)] overflow-y-auto border border-slate-200 bg-white shadow-lg"
    >
        {{ $slot }}
    </div>
</div>
