<x-school.data-table
    :empty="$expenses->isEmpty()"
    :empty-colspan="7"
    empty-message="No expenses found."
    :show-footer="$expenses->hasPages()"
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

    <tbody id="expenseTableBody">
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
                <x-school.table-cell-scroll :title="$expense->expense_reason">
                    {{ $expense->expense_reason }}
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <x-school.table-cell-scroll :title="$expense->details ?? '-'">
                    {{ $expense->details ?? '-' }}
                </x-school.table-cell-scroll>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">
                ৳{{ number_format($expense->amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center min-w-[90px]">
                <div class="flex h-6 w-full items-center justify-center space-x-1">
                    <button type="button" onclick="editExpense({{ $expense->id }})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit Expense">
                        <i class="far fa-edit text-xs"></i>
                    </button>
                    <button type="button" onclick="deleteExpense({{ $expense->id }})" class="text-red-600 hover:text-red-800 p-1" title="Delete Expense">
                        <i class="far fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </x-table.td>
        </x-table.row>
    @endforeach
    </tbody>

    <x-slot:footer>
        {{ $expenses->links() }}
    </x-slot:footer>
</x-school.data-table>
