<x-table
    unstyled
    :empty="$donates->isEmpty()"
    :empty-colspan="9"
    empty-message="No donations found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$donates->hasPages()"
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
            <col style="width: 5%;">
            <col style="width: 12%;">
            <col style="width: 16%;">
            <col style="width: 12%;">
            <col style="width: 15%;">
            <col style="width: 18%;">
            <col style="width: 10%;">
            <col style="width: 6%;">
            <col style="width: 6%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">
            Sl
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Donate No
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Name
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Mobile Number
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Location
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Donate Reason
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">
            Amount
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">
            Status
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    @foreach ($donates as $donate)
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $donates->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
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
                ৳{{ number_format($donate->amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                @php
                    $statusClass = match ($donate->status) {
                        'Paid' => 'bg-green-100 text-green-700',
                        'Partial' => 'bg-yellow-100 text-yellow-700',
                        'Due' => 'bg-red-100 text-red-700',
                        'Over Due' => 'bg-blue-100 text-blue-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="rounded px-2 py-1 text-xs font-medium {{ $statusClass }}">
                    {{ $donate->status }}
                </span>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Donate {{ $donate->donate_no }}"
                        onclick="editDonate({{ $donate->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Donate {{ $donate->donate_no }}"
                        onclick="deleteDonate({{ $donate->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    <x-slot:footer>
        {{ $donates->links() }}
    </x-slot:footer>
</x-table>