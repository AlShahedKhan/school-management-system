@props([
    'title',
    'items' => [],
    'icon' => 'fa-circle',
    'iconStyle' => 'bg-blue-50 text-blue-600',
])

<section class="min-w-0 bg-white" aria-label="{{ $title }}">
    <header class="flex h-10 items-center justify-between border border-b-0 border-gray-300 bg-white px-3">
        <h2 class="truncate text-xs font-semibold text-slate-700">
            {{ $title }}
        </h2>
        <span class="shrink-0 text-[10px] font-semibold text-blue-600">
            View All
        </span>
    </header>

    <x-school.data-table
        :empty="count($items) === 0"
        :empty-colspan="4"
        empty-message="No recent records found."
        min-width="520px"
        class="border-gray-300 p-0 shadow-sm"
    >
        <x-slot:columns>
            <colgroup>
                <col style="width: 44px;">
                <col style="width: 38%;">
                <col style="width: 32%;">
                <col style="width: 110px;">
            </colgroup>
        </x-slot:columns>

        <x-slot:head>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-2 text-center font-semibold"></x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-2 text-left font-semibold">Name</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-2 text-left font-semibold">Details</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-2 text-left font-semibold">Date</x-table.th>
        </x-slot:head>

        @foreach ($items as $item)
            <x-table.row unstyled class="hover:bg-gray-50">
                <x-table.td unstyled class="h-9 whitespace-nowrap border border-gray-300 px-2 text-center">
                    <span class="{{ $iconStyle }} mx-auto flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                        aria-hidden="true">
                        <i class="fas {{ $item['icon'] ?? $icon }} text-[11px]"></i>
                    </span>
                </x-table.td>

                <x-table.td unstyled class="h-9 border border-gray-300 px-2">
                    <x-school.table-cell-scroll :title="$item['title']">
                        <span class="text-xs font-semibold text-slate-700">{{ $item['title'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-9 border border-gray-300 px-2">
                    <x-school.table-cell-scroll :title="$item['detail']">
                        <span class="text-xs text-slate-500">{{ $item['detail'] }}</span>
                    </x-school.table-cell-scroll>
                </x-table.td>

                <x-table.td unstyled class="h-9 whitespace-nowrap border border-gray-300 px-2">
                    <time class="text-[11px] text-slate-500" datetime="{{ $item['datetime'] }}">
                        {{ $item['date'] }}
                    </time>
                </x-table.td>
            </x-table.row>
        @endforeach
    </x-school.data-table>
</section>
