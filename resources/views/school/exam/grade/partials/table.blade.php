<style>
    .pagination-btn { padding: 6px 12px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 35px; border-radius: 0; }
    .pagination-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .pagination-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
</style>
<x-school.data-table
    :empty="false"
    :empty-colspan="7"
    empty-message="No grades found."
    show-footer="true"
    min-width="700px"
    tbody-id="gradeTableBody"
>
    <x-slot:columns>
        <colgroup>
            <col style="width: 55px;">
            <col style="width: 125px;">
            <col style="width: 125px;">
            <col style="width: 145px;">
            <col style="width: 95px;">
            <col style="width: 100px;">
            <col style="width: 95px;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Minimum Mark</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Maximum Mark</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Letter Name</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Point</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Total Mark</x-table.th>
        <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
    </x-slot:head>
    <x-slot:footer>
        <div class="flex w-full items-center justify-between px-2">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">0 of 0</div>
            <div class="flex items-center gap-1" id="paginationControls"></div>
        </div>
    </x-slot:footer>
</x-school.data-table>
