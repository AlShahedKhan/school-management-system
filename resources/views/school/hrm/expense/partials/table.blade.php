<x-school.data-table
    :empty="$expenses->isEmpty()"
    :empty-colspan="7"
    empty-message="No expenses found."
    :show-footer="$expenses->hasPages()"
    tbody-id="expenseTableBody"
    minWidth="1000px"
>
    <x-slot:columns>
        <colgroup>
            <col style="width: 5%;">
            <col style="width: 12%;">
            <col style="width: 15%;">
            <col style="width: 20%;">
            <col style="width: 28%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
        </colgroup>
    </x-slot:columns>

    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Date</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Invoice No</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Reason</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Details</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">Amount</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold min-w-[90px]">Action</x-table.th>
    </x-slot:head>

    @foreach ($expenses as $expense)
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $expenses->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('Y-m-d') : '-' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-medium">
                {{ $expense->invoice_no ?? '-' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="school-data-table-cell-scroll" title="{{ $expense->expense_reason }}">
                    {{ $expense->expense_reason }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="school-data-table-cell-scroll" title="{{ $expense->details ?? '-' }}">
                    {{ $expense->details ?? '-' }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">
                ৳{{ number_format($expense->amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center min-w-[90px]">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Expense"
                        onclick="editExpense({{ $expense->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Expense"
                        onclick="deleteExpense({{ $expense->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach

    <x-slot:footer>
        {{ $expenses->links() }}
    </x-slot:footer>
</x-school.data-table>
