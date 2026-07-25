@props([
    'items' => [],
])

<section
    {{ $attributes->class([
        'min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4',
    ]) }}
    aria-labelledby="recent-payments-title"
>
    <header class="mb-3 flex min-h-8 items-center">
        <h3 id="recent-payments-title" class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">
            Recent Payments
        </h3>
    </header>

    <x-school.data-table
        :empty="count($items) === 0"
        :empty-colspan="7"
        empty-message="No recent payments found."
        min-width="900px"
        frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none"
    >
        <x-slot:columns>
            <colgroup>
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 15%;">
                <col style="width: 20%;">
                <col style="width: 12%;">
                <col style="width: 12%;">
                <col style="width: 14%;">
            </colgroup>
        </x-slot:columns>

        <x-slot:head>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Photo</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student ID</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Fee</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Method</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">Paid</x-table.th>
            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Date</x-table.th>
        </x-slot:head>

        @foreach ($items as $item)
            <x-table.row unstyled class="hover:bg-gray-50">
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                    <img src="{{ $item['photo'] }}" alt="{{ $item['name'] }}" class="mx-auto h-6 w-6 rounded-full object-cover">
                </x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['name']"><span class="text-xs font-semibold text-slate-700">{{ $item['name'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['student_id']"><span class="font-mono text-xs text-slate-600">{{ $item['student_id'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['fee']"><span class="text-xs text-slate-600">{{ $item['fee'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 border border-gray-300 px-3"><x-school.table-cell-scroll :title="$item['method']"><span class="text-xs text-slate-600">{{ $item['method'] }}</span></x-school.table-cell-scroll></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right"><span class="text-xs font-semibold text-emerald-600">{{ $item['amount'] }}</span></x-table.td>
                <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3"><time class="text-[11px] text-slate-500" datetime="{{ $item['datetime'] }}">{{ $item['date'] }}</time></x-table.td>
            </x-table.row>
        @endforeach
    </x-school.data-table>
</section>
