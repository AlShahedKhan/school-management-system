<x-modal.form
    id="filterModal"
    form-id="feeFilterForm"
    title="Fee filter"
    close-button-id="resetFilter"
    action="{{ route('school.fees-type') }}"
    method="GET"
    :enctype="null"
    class="school-fee-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
>
    <x-input.dropdown-select
        id="classFilter"
        name="class_id"
        placeholder="Class"
        :value="request('class_id')"
        :options="$classFilterOptions ?? []"
    />

    <x-input.dropdown-select
        id="groupFilter"
        name="group_id"
        placeholder="Group"
        :value="request('group_id')"
        :options="$groupFilterOptions ?? []"
    />

    <x-input.dropdown-select
        id="sectionFilter"
        name="section_id"
        placeholder="Section"
        :value="request('section_id')"
        :options="$sectionFilterOptions ?? []"
    />

    <x-input.dropdown-select
        id="sessionFilter"
        name="session_id"
        placeholder="Session"
        :value="request('session_id')"
        :options="$sessionFilterOptions ?? []"
    />

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="resetFilter" type="button" class="w-full">
                Reset
            </x-button.secondary>

            <x-button.primary id="applyFilter" type="button" class="w-full">
                Apply
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

<div id="exportModal"
    class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
    <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl">
        <div class="flex flex-col gap-1.5">
            <button
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                PDF
            </button>
            <button
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                EXCEL
            </button>
            <button
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                PRINT
            </button>
            <button id="closeExport"
                class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                Cancel
            </button>
        </div>
    </div>
</div>
