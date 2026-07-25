<div class="table-card">
    <div class="table-responsive">
        <table class="min-w-[1100px]">
            <thead>
                <tr>
                    <x-table.th class="text-left">Sl</x-table.th>
                    <x-table.th>Student Name</x-table.th>
                    <x-table.th>Student ID</x-table.th>
                    <x-table.th>Class</x-table.th>
                    <x-table.th>Fee Type</x-table.th>
                    <x-table.th>Fee Name</x-table.th>
                    <x-table.th>Amount</x-table.th>
                    <x-table.th>Paid</x-table.th>
                    <x-table.th>Due</x-table.th>
                    <x-table.th>Status</x-table.th>
                    <x-table.th class="text-center">Action</x-table.th>
                </tr>
            </thead>
            <tbody id="feeTableBody" class="bg-white divide-y divide-gray-100">
                <tr>
                    <td colspan="11"
                        class="text-center py-10 text-gray-400 uppercase text-[10px] font-bold tracking-widest">
                        <i class="mdi mdi-loading mdi-spin mr-2"></i> Loading Student Fees...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">0 of 0
        </div>
        <div class="flex items-center gap-1" id="paginationControls"></div>
    </div>
</div>
