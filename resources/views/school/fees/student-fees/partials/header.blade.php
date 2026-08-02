<div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-2" style="border-radius:0;">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h3 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h3>
            <div class="flex items-center text-slate-400 text-[12px] mt-1">
                <span>School</span>
                <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                <span id="pageTitle" class="text-slate-500"></span>
            </div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="hidden lg:flex items-center gap-2">
            <x-input.search id="feeSearch" placeholder="Search..." class="w-full lg:block lg:w-72" />
            <x-button.secondary id="btnRestoreDesktop" class="hidden lg:inline-flex">Restore</x-button.secondary>
        </div>
        <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
            <x-button.secondary id="btnFilter">Filter</x-button.secondary>
            <x-dropdown button-id="btnStatusFilter" menu-id="headerStatusFilterMenu" label="Status">
                <x-dropdown.item id="statusFilterAll">All Status</x-dropdown.item>
                <x-dropdown.item id="statusFilterPaid">Paid</x-dropdown.item>
                <x-dropdown.item id="statusFilterPartialPaid">Partial Paid</x-dropdown.item>
                <x-dropdown.item id="statusFilterDue">Due</x-dropdown.item>
                <x-dropdown.item id="statusFilterDuePartial">Due Partial</x-dropdown.item>
                <x-dropdown.item id="statusFilterOverDue">Over Due</x-dropdown.item>
                <x-dropdown.item id="statusFilterOverDuePartial">Over Due Partial</x-dropdown.item>
                <x-dropdown.item id="statusFilterAdvance">Advance</x-dropdown.item>
                <x-dropdown.item id="statusFilterAdvancePartial">Advance Partial</x-dropdown.item>
                <x-dropdown.item id="statusFilterPending">Pending</x-dropdown.item>
            </x-dropdown>
            <x-dropdown button-id="btnExport" menu-id="exportDropdown" label="Export">
                <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
                <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
                <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
            </x-dropdown>
        </div>
    </div>
    <div class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
        <x-input.search id="feeSearchMobile" placeholder="Search Fee..." class="col-span-2 min-w-0" />
        <x-button.secondary id="btnRestoreMobile">Restore</x-button.secondary>
    </div>
</div>
