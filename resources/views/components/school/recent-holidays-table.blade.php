@props([
    'items' => [],
])

<section {{ $attributes->class(['min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4']) }} aria-labelledby="recent-holidays-title">
    <header class="mb-3 flex min-h-8 items-center">
        <h3 id="recent-holidays-title" class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">Recent Holidays</h3>
    </header>

    <x-school.data-table :empty="count($items) === 0" :empty-colspan="6" empty-message="No recent holidays found." min-width="800px" frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none">
        <x-slot:columns><colgroup><col style="width: 13%;"><col style="width: 25%;"><col style="width: 22%;"><col style="width: 14%;"><col style="width: 14%;"><col style="width: 12%;"></colgroup></x-slot:columns>
        <x-slot:head>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Type</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Reason</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Target</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Start</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">End</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Days</x-table.th>
        </x-slot:head>
        @foreach ($items as $item)
            <x-table.row unstyled class="hover:bg-gray-50">
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3"><span class="bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-orange-700">{{ $item['type'] }}</span></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['reason']"><span class="text-xs font-semibold text-slate-700">{{ $item['reason'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['target']"><span class="text-xs text-slate-600">{{ $item['target'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3"><time class="text-[11px] text-slate-500" datetime="{{ $item['datetime'] }}">{{ $item['start_date'] }}</time></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3"><span class="text-[11px] text-slate-500">{{ $item['end_date'] }}</span></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center"><span class="text-xs font-semibold text-orange-600">{{ $item['days'] }}</span></x-table.td>
            </x-table.row>
        @endforeach
    </x-school.data-table>
</section>
