<x-school.data-table
    :empty="false"
    :empty-colspan="11"
    empty-message="No teachers found."
    show-footer="true"
    minWidth="1105px"
>
    <x-slot:columns>
        <colgroup>
            <col style="width: 45px;">
            <col style="width: 55px;">
            <col style="width: 120px;">
            <col style="width: 140px;">
            <col style="width: 130px;">
            <col style="width: 125px;">
            <col style="width: 95px;">
            <col style="width: 120px;">
            <col style="width: 90px;">
            <col style="width: 75px;">
            <col style="width: 110px;">
        </colgroup>
    </x-slot:columns>

    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Photo</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">ID Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Designation</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Mobile Number</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-semibold">Salary</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Salary Start Date</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Pay Date</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Status</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
    </x-slot:head>

    <tbody id="teacherTableBody"></tbody>

    <x-slot:footer>
        <div class="pagination-container flex items-center justify-between w-full px-2">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                0 of 0
            </div>
            <div class="flex items-center gap-1" id="paginationControls"></div>
        </div>
    </x-slot:footer>
</x-school.data-table>
