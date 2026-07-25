<div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
    <x-button.secondary id="btnFilter">
        Filter
    </x-button.secondary>
    <x-dropdown button-id="btnExport1" menu-id="exportDropdown" label="Export">
        <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
        <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
        <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
    </x-dropdown>
    <x-button.primary id="openDonateModal" class="flex-1 lg:flex-none">
        Donate
    </x-button.primary>
</div>