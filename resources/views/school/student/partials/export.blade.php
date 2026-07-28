<div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
    <x-button.secondary id="btnFilter">
        Filter
    </x-button.secondary>
    <x-dropdown button-id="btnExport1" menu-id="exportDropdown" label="Export">
        <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
        <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
        <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
    </x-dropdown>
    <x-dropdown button-id="btnStudent1" menu-id="studentDropdown" label="Student" align="full" variant="primary">
        <x-dropdown.item onclick="window.location.href='{{ route('school.student-admission') }}'">Admission</x-dropdown.item>
        <x-dropdown.item onclick="window.location.href='{{ route('school.student-bulk-upload') }}'">Bulk Upload</x-dropdown.item>
        <x-dropdown.item onclick="window.location.href='{{ route('school.student-re-admission') }}'">Re-Admission</x-dropdown.item>
        <x-dropdown.item onclick="window.location.href='{{ route('school.student-promote') }}'">Promote</x-dropdown.item>
    </x-dropdown>
</div>
