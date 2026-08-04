<div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-2" style="border-radius:0;">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h3 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Due Collection</h3>
            <div class="flex items-center text-slate-400 text-[12px] mt-1">
                <span>School</span>
                <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                <span id="pageTitle" class="text-slate-500">Due Collection</span>
            </div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="hidden lg:flex items-center gap-2">
            <x-input.search id="masterSearch" placeholder="Search Name, ID, Class..." class="w-full lg:block lg:w-72" />
            <x-button.secondary id="btnResetSearch">Reset</x-button.secondary>
        </div>
        <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
            <x-button.secondary id="btnFilter">Filter</x-button.secondary>
            <x-dropdown button-id="btnExport1" menu-id="exportDropdown" label="Export" align="right">
                <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
                <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
                <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
            </x-dropdown>
            <x-dropdown button-id="btnStatusFilter" menu-id="headerStatusFilterMenu" label="Status">
                <x-dropdown.item id="statusFilterAll">All Status</x-dropdown.item>
                <x-dropdown.item id="statusFilterDue">Due</x-dropdown.item>
                <x-dropdown.item id="statusFilterDuePartial">Due Partial</x-dropdown.item>
                <x-dropdown.item id="statusFilterOverDue">Over Due</x-dropdown.item>
                <x-dropdown.item id="statusFilterOverDuePartial">Over Due Partial</x-dropdown.item>
            </x-dropdown>
        </div>
    </div>
    <div class="mt-3 flex gap-2 lg:hidden">
        <x-input.search id="masterSearchMobile" placeholder="Search..." class="flex-1 min-w-0" />
        <x-button.secondary id="btnResetSearch">Reset</x-button.secondary>
    </div>
</div>
