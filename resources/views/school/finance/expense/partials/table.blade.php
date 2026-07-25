<x-table
    unstyled
    :empty="$expenses->isEmpty()"
    :empty-colspan="7"
    empty-message="No expenses found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$expenses->hasPages()"
    footer-class="w-full border-t border-gray-300 px-0 py-3"
    scroll-class="donate-table-scroll"
    table-class="donate-fixed-table border-collapse border border-gray-300 text-xs"
    head-class="bg-gray-100"
    tbody-class=""
    class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-0 shadow-md"
    style="border-radius:0;"
>
    <x-slot:columns>
        <colgroup>
            <col style="width: 6%;">
            <col style="width: 16%;">
            <col style="width: 15%;">
            <col style="width: 28%;">
            <col style="width: 13%;">
            <col style="width: 14%;">
            <col style="width: 8%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">
            Sl
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Invoice No
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Expense Date
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">
            Expense Reason
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">
            Amount
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">
            Balance
        </x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    @foreach ($expenses as $expense)
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $expenses->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $expense->invoice_no }}">
                    {{ $expense->invoice_no }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3" title="{{ $expense->expense_date?->format('d-F-y') ?? '--' }}">
                {{ $expense->expense_date?->format('d-F-y') ?? '--' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $expense->expense_reason }}">
                    {{ $expense->expense_reason }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left">
                ৳{{ number_format($expense->amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-medium">
                ৳{{ number_format($expense->balance, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Expense {{ $expense->invoice_no }}"
                        onclick="editExpense({{ $expense->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Expense {{ $expense->invoice_no }}"
                        onclick="deleteExpense({{ $expense->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    <x-slot:footer>
        {{ $expenses->links() }}
    </x-slot:footer>
</x-table>



