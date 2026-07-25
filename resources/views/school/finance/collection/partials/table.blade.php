<x-table
    unstyled
    :empty="$collections->isEmpty()"
    :empty-colspan="13"
    empty-message="No collection records found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$collections->hasPages()"
    footer-class="w-full border-t border-gray-300 px-0 py-3"
    scroll-class="donate-table-scroll"
    table-class="donate-fixed-table border-collapse border border-gray-300 text-xs"
    head-class="bg-gray-100"
    tbody-class=""
    class="mb-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4"
    style="border-radius:0;"
>
    <x-slot:columns>
        <colgroup>
            <col style="width:5%;">
            <col style="width:10%;">
            <col style="width:14%;">
            <col style="width:12%;">
            <col style="width:12%;">
            <col style="width:16%;">
            <col style="width:10%;">
            <col style="width:8%;">
            <col style="width:8%;">
            <col style="width:9%;">
            <col style="width:9%;">
            <col style="width:8%;">
            <col style="width:9%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Donate No
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Name
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Mobile Number
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Location
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Donate Reason
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Paid Amount
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Receive Month
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Receive Year
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Due
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Over Due
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Status
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    @foreach ($collections as $collection)
        @php
            $donate = $collection->donate;
            $totalPaid = $donate->collections->sum('paid_amount');
            $due = max(0, $donate->amount - $totalPaid);
            $overDue = max(0, $totalPaid - $donate->amount);
            if ($totalPaid == 0) {
                $status = 'Due';
                $statusClass = 'bg-red-100 text-red-700';
            } elseif ($totalPaid < $donate->amount) {
                $status = 'Partial';
                $statusClass = 'bg-yellow-100 text-yellow-700';
            } elseif ($totalPaid == $donate->amount) {
                $status = 'Paid';
                $statusClass = 'bg-green-100 text-green-700';
            } else {
                $status = 'Over Due';
                $statusClass = 'bg-blue-100 text-blue-700';
            }
        @endphp
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-center">
                {{ $collections->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $donate->donate_no }}">
                    {{ $donate->donate_no }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $donate->name }}">
                    {{ $donate->name }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
                <a
                    href="tel:{{ $donate->mobile_number }}"
                    class="inline-block text-blue-500"
                    style="text-decoration: none !important;">
                    {{ $donate->mobile_number }}
                </a>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $donate->location }}">
                    {{ $donate->location ?? '--' }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $donate->donate_reason }}">
                    {{ $donate->donate_reason }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-medium">
                ৳{{ number_format($collection->paid_amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ \Carbon\Carbon::create()->month($collection->receive_month)->format('F') }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $collection->receive_year }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left">
                ৳{{ number_format($due, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left">
                ৳{{ number_format($overDue, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <span class="rounded px-2 py-1 text-xs font-medium {{ $statusClass }}">
                    {{ $status }}
                </span>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Collection {{ $donate->donate_no }}"
                        onclick="editCollection({{ $collection->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Collection {{ $donate->donate_no }}"
                        onclick="deleteCollection({{ $collection->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    <x-slot:footer>
        {{ $collections->links() }}
    </x-slot:footer>
</x-table>