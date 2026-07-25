@props([
    'id',
    'name',
])

<input
    type="password"
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes->class([
        'form-input-fixed h-9 w-full border border-slate-300 bg-white px-3 py-1 pr-10',
        'text-[10px] text-slate-700 outline-none transition-colors',
        'focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
    ])->merge([
        'style' => 'border-radius: 0;',
    ]) }}
>

<button
    type="button"
    class="absolute bottom-0 right-0 flex h-9 w-9 items-center justify-center border-0 bg-transparent p-0 text-slate-400 transition-colors hover:text-slate-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500"
    onclick="togglePass('{{ $id }}', this)"
    aria-label="Show password"
>
    <i class="mdi mdi-eye text-sm" aria-hidden="true"></i>
</button>
