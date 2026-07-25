@props([
    'empty' => false,
    'emptyMessage' => 'No data found.',
    'emptyColspan' => 100,
    'emptyCellClass' => 'px-6 py-10 text-center text-gray-500',
    'footerClass' => 'border-t border-gray-200 px-6 py-4',
    'headClass' => 'text-xs uppercase text-gray-600',
    'scrollClass' => 'overflow-x-auto',
    'showFooter' => true,
    'tableClass' => 'w-full text-left text-sm',
    'tbodyClass' => 'divide-y divide-gray-200',
    'tbodyId' => null,
    'unstyled' => false,
    'tbodyId' => null,
])

<div
    {{ $attributes->class([
        'overflow-hidden border bg-white' => ! $unstyled,
    ]) }}>
    <div class="{{ $scrollClass }}">
        <table class="{{ $tableClass }}">
            @isset($columns)
                {{ $columns }}
            @endisset

            @isset($head)
            <thead class="{{ $headClass }}">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            @endisset

            <tbody class="{{ $tbodyClass }}" @if($tbodyId) id="{{ $tbodyId }}" @endif>
                @if ($empty)
                <tr>
                    <td
                        colspan="{{ $emptyColspan }}"
                        class="{{ $emptyCellClass }}">
                        {{ $emptyMessage }}
                    </td>
                </tr>
                @else
                {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    @if ($showFooter)
        @isset($footer)
        <div class="{{ $footerClass }}">
            {{ $footer }}
        </div>
        @endisset
    @endif
</div>
