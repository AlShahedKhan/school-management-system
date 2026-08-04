<style>
    .donate-cell-scroll { display: block; width: 100%; max-width: 100%; overflow-x: auto; overflow-y: hidden; white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
    .donate-cell-scroll::-webkit-scrollbar { display: none; }
    .donate-cell-scroll.is-scrollable { cursor: grab; }
    .donate-cell-scroll.is-dragging { cursor: grabbing; user-select: none; }
    .donate-fixed-table { width: 100%; min-width: 1550px; table-layout: fixed; }
    .donate-table-frame { width: 100%; overflow: hidden; border: 1px solid #d1d5db; background: #fff; }
    .donate-table-scroll { width: 100%; overflow-x: auto; overflow-y: hidden; background: #fff; -webkit-overflow-scrolling: touch; scrollbar-width: thin; scrollbar-color: #e5e7eb transparent; }
    .donate-table-scroll::-webkit-scrollbar { height: 4px; }
    .donate-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .donate-table-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 9999px; }
    .donate-table-scroll::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    @media (min-width: 1024px) { .donate-fixed-table { width: 100%; min-width: 1550px; } .donate-table-scroll { overflow-x: auto; } }
    @media (max-width: 1023px) { .donate-fixed-table { width: 1550px; min-width: 1550px; } }
</style>

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
    tbody-id="expenseTableBody"
    class="mb-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4"
    style="border-radius:0;"
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
                <div class="donate-cell-scroll" title="{{ $expense->expense_reason }}">
                    {{ $expense->expense_reason }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $expense->details ?? '-' }}">
                    {{ $expense->details ?? '-' }}
                </div>
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
</x-table>
