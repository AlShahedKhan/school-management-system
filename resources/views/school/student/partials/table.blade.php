<x-school.data-table
    :empty="false"
    :empty-colspan="13"
    empty-message="No students found."
    show-footer="true"
    minWidth="1250px"
>
    <x-slot:columns>
        <colgroup>
            <col style="min-width: 45px; width: 4%;">
            <col style="min-width: 55px; width: 5%;">
            <col style="min-width: 120px; width: 11%;">
            <col style="min-width: 140px; width: 13%;">
            <col style="min-width: 125px; width: 11%;">
            <col style="min-width: 130px; width: 12%;">
            <col style="min-width: 75px; width: 6%;">
            <col style="min-width: 75px; width: 6%;">
            <col style="min-width: 105px; width: 8%;">
            <col style="min-width: 90px; width: 7%;">
            <col style="min-width: 105px; width: 8%;">
            <col style="min-width: 75px; width: 6%;">
            <col style="min-width: 130px; width: 130px;">
        </colgroup>
    </x-slot:columns>

    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Photo</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Id Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Mobile Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Father Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Type</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Status</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-1 text-center font-semibold min-w-[130px]">Action</x-table.th>
    </x-slot:head>

    <tbody id="studentTableBody"></tbody>

    <x-slot:footer>
        <div class="pagination-container flex items-center justify-between w-full px-2">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                0 of 0
            </div>
            <div class="flex items-center gap-1" id="paginationControls"></div>
        </div>
    </x-slot:footer>
</x-school.data-table>

@include('school.partials.student-details-modal')
@include('school.partials.student-view-modal')
@include('school.partials.fee-template-modal')
@include('school.partials.student-edit-modal')
@include('school.partials.quick-add-modals')
@include('school.partials.student-deactivate-modal')
@include('school.partials.student-activate-modal')
@include('school.partials.student-delete-modal')
@include('school.student.partials.student-filter-modal')
