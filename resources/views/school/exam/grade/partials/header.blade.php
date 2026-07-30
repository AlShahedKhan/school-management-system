<x-school.list-header title="Exam Grade" breadcrumb-current="Exam Grade" keep-title>
    <x-slot:search>
        <x-input.search
            id="gradeSearch"
            placeholder="Search Grades..."
            class="w-72"
        />
        <x-button.secondary id="btnRestoreDesktop" type="button">
            Restore
        </x-button.secondary>
    </x-slot:search>

    <x-slot:actions>
        <x-button.secondary id="btnFilter" type="button" class="w-full">
            Filter
        </x-button.secondary>

        <x-dropdown button-id="btnExport1" menu-id="exportDropdown" label="Export" align="full">
            <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
            <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
            <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
        </x-dropdown>

        <x-button.primary
            id="openGradeModalBtn"
            type="button"
            class="w-full"
            onclick="openGradeModal()"
        >
            Add Grade
        </x-button.primary>
    </x-slot:actions>

    <x-slot:mobile-search>
        <x-input.search
            id="gradeSearchMobile"
            placeholder="Search Grades..."
            class="col-span-2 min-w-0"
        />
        <x-button.secondary id="btnRestoreMobile" type="button" class="w-full">
            Restore
        </x-button.secondary>
    </x-slot:mobile-search>
</x-school.list-header>
