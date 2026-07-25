@props([
    'items' => [],
])

<section {{ $attributes->class(['min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4']) }} aria-labelledby="recent-leaves-title">
    <header class="mb-3 flex min-h-8 items-center">
        <h3 id="recent-leaves-title" class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">Recent Leaves</h3>
    </header>

    <x-school.data-table :empty="count($items) === 0" :empty-colspan="6" empty-message="No recorded leave days found." min-width="800px" frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none">
        <x-slot:columns><colgroup><col style="width: 8%;"><col style="width: 25%;"><col style="width: 21%;"><col style="width: 18%;"><col style="width: 14%;"><col style="width: 14%;"></colgroup></x-slot:columns>
        <x-slot:head>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Photo</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Employee</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Designation</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Payroll Period</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Leave Days</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Recorded</x-table.th>
        </x-slot:head>
        @foreach ($items as $item)
            <x-table.row unstyled class="hover:bg-gray-50">
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center"><img src="{{ $item['photo'] }}" alt="{{ $item['name'] }}" class="mx-auto h-6 w-6 rounded-full object-cover"></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['name']"><span class="text-xs font-semibold text-slate-700">{{ $item['name'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['designation']"><span class="text-xs text-slate-600">{{ $item['designation'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['period']"><span class="text-xs text-slate-600">{{ $item['period'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center"><span class="text-xs font-semibold text-indigo-600">{{ $item['days'] }}</span></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3"><time class="text-[11px] text-slate-500" datetime="{{ $item['datetime'] }}">{{ $item['date'] }}</time></x-table.td>
            </x-table.row>
        @endforeach
    </x-school.data-table>
</section>
