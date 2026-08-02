@extends('layouts.school')

@section('content')
    <style>
        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: .75rem;
            box-sizing: border-box;
        }

    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <x-school.list-header title="Profit & Loss" breadcrumb-current="Profit & Loss" keep-title>
                <x-slot:actions>
                    <form method="GET" class="flex flex-wrap items-end gap-2">
                        <div>
                            <x-input.control
                                id="pl_from"
                                name="from_date"
                                type="date"
                                value="{{ request('from_date') }}"
                                class="peer h-8 w-40"
                            />
                            <x-input.floating-label for="pl_from" :floating="false">From</x-input.floating-label>
                        </div>
                        <div>
                            <x-input.control
                                id="pl_to"
                                name="to_date"
                                type="date"
                                value="{{ request('to_date') }}"
                                class="peer h-8 w-40"
                            />
                            <x-input.floating-label for="pl_to" :floating="false">To</x-input.floating-label>
                        </div>
                        <x-button.primary type="submit">Filter</x-button.primary>
                    </form>
                </x-slot:actions>
            </x-school.list-header>

            <div class="mb-2 mt-0 grid grid-cols-2 gap-2 md:grid-cols-3">
                <x-dashboard.stat-card label="Total Income" :value="number_format($summary['total_cash_in'], 2)" icon="fa-hand-holding-usd" icon-style="bg-emerald-50 text-emerald-600" />
                <x-dashboard.stat-card label="Total Expense" :value="number_format($summary['total_cash_out'], 2)" icon="fa-receipt" icon-style="bg-red-50 text-red-600" />
                <x-dashboard.stat-card label="Net Profit / Loss" :value="number_format($summary['net'], 2)" icon="fa-balance-scale" icon-style="bg-violet-50 text-violet-700" />
            </div>

            <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
                <section class="min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4">
                    <header class="mb-3 flex min-h-8 items-center">
                        <h3 class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">
                            Cash In
                        </h3>
                    </header>

                    <x-school.data-table
                        :empty="false"
                        :empty-colspan="4"
                        empty-message="No cash in entries"
                        min-width="560px"
                        frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none"
                    >
                        <x-slot:columns>
                            <colgroup>
                                <col style="width:12%;">
                                <col style="width:43%;">
                                <col style="width:15%;">
                                <col style="width:30%;">
                            </colgroup>
                        </x-slot:columns>

                        <x-slot:head>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">SL</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Cash In</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Entries</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Amount</x-table.th>
                        </x-slot:head>

                        @forelse ($summary['cash_in_by_module'] as $row)
                            <x-table.row unstyled class="hover:bg-gray-50">
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $loop->iteration }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $row->source_module }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $row->entries }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ number_format((float) $row->total, 2) }}</x-table.td>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.td unstyled colspan="4" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No cash in entries</x-table.td>
                            </x-table.row>
                        @endforelse
                    </x-school.data-table>
                </section>

                <section class="min-w-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4">
                    <header class="mb-3 flex min-h-8 items-center">
                        <h3 class="!m-0 truncate !text-[15px] !font-normal leading-tight text-gray-800 sm:!text-xl">
                            Cash Out
                        </h3>
                    </header>

                    <x-school.data-table
                        :empty="false"
                        :empty-colspan="4"
                        empty-message="No cash out entries"
                        min-width="560px"
                        frame-class="school-data-table-frame border-0 bg-white p-0 shadow-none"
                    >
                        <x-slot:columns>
                            <colgroup>
                                <col style="width:12%;">
                                <col style="width:43%;">
                                <col style="width:15%;">
                                <col style="width:30%;">
                            </colgroup>
                        </x-slot:columns>

                        <x-slot:head>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">SL</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Cash Out</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Entries</x-table.th>
                            <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Amount</x-table.th>
                        </x-slot:head>

                        @forelse ($summary['cash_out_by_module'] as $row)
                            <x-table.row unstyled class="hover:bg-gray-50">
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $loop->iteration }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $row->source_module }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ $row->entries }}</x-table.td>
                                <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-left">{{ number_format((float) $row->total, 2) }}</x-table.td>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.td unstyled colspan="4" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No cash out entries</x-table.td>
                            </x-table.row>
                        @endforelse
                    </x-school.data-table>
                </section>
            </div>
        </div>
    </div>
@endsection
