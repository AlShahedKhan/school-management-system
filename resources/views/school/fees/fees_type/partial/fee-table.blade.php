<div class="table-card">
    <div class="table-responsive">
        <table class="min-w-[1000px]">
            <thead>
                <tr>
                    <x-table.th class="text-left">Sl</x-table.th>
                    <x-table.th>Class</x-table.th>
                    <x-table.th>Group</x-table.th>
                    <x-table.th>Section</x-table.th>
                    <x-table.th>Session</x-table.th>
                    <x-table.th>Fees Type</x-table.th>
                    <x-table.th>Fees Name</x-table.th>
                    <x-table.th>Amount</x-table.th>
                    <x-table.th>Pay Date</x-table.th>
                    <x-table.th class="text-center">Action</x-table.th>
                </tr>
            </thead>
            <tbody id="feeTableBody" class="bg-white divide-y divide-gray-100">
                <tr>
                    <td colspan="10" class="text-center py-10 text-gray-400 uppercase text-[10px] font-bold tracking-widest">
                        <i class="mdi mdi-loading mdi-spin mr-2"></i> Initializing Account Records...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">0 of 0</div>
        <div class="flex items-center gap-1" id="paginationControls"></div>
    </div>
</div>
