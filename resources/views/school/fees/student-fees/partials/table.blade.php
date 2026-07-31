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
    .pagination-btn { padding: 6px 12px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 35px; border-radius: 0; }
    .pagination-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .pagination-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .status-badge { padding: 2px 8px; font-size: 10px; font-weight: 700; border-radius: 2px; text-transform: uppercase; }
    .status-paid { background: #d1fae5; color: #059669; }
    .status-partial_paid { background: #dbeafe; color: #2563eb; }
    .status-due { background: #fef3c7; color: #d97706; }
    .status-due_partial { background: #fef3c7; color: #d97706; }
    .status-over_due { background: #fee2e2; color: #dc2626; }
    .status-over_due_partial { background: #fee2e2; color: #dc2626; }
    .status-advance { background: #d1fae5; color: #059669; }
    .status-advance_partial { background: #dbeafe; color: #2563eb; }
    .status-pending { background: #f3f4f6; color: #6b7280; }
</style>
<x-table
    unstyled :empty="false" empty-message="No student fees found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="true" footer-class="w-full border-t border-gray-300 px-0 py-3"
    scroll-class="donate-table-scroll" table-class="donate-fixed-table border-collapse border border-gray-300 text-xs"
    head-class="bg-gray-100" tbody-class="" tbody-id="feeTableBody"
    class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-0 shadow-md" style="border-radius:0;"
>
    <x-slot:columns>
        <colgroup>
            <col style="width: 3%;"><col style="width: 8%;"><col style="width: 10%;"><col style="width: 7%;"><col style="width: 7%;"><col style="width: 7%;"><col style="width: 7%;"><col style="width: 8%;"><col style="width: 9%;"><col style="width: 7%;"><col style="width: 7%;"><col style="width: 6%;"><col style="width: 6%;"><col style="width: 7%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Id</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Fee Type</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Fee Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Payable</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Paid</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Due</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Over Due</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Status</x-table.th>
    </x-slot:head>
    <x-slot:footer>
        <div class="flex items-center justify-between px-4 py-2">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">0 of 0</div>
            <div class="flex items-center gap-1" id="paginationControls"></div>
        </div>
    </x-slot:footer>
</x-table>
