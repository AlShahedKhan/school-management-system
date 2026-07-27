<x-modal.form
    id="filterModal"
    form-id="studentFilterForm"
    title="Filter Students"
    close-button-id="resetFilter"
    action="#"
    method="GET"
    :enctype="null"
    class="student-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
>
    <x-input.dropdown-select
        id="classFilter"
        name="class"
        placeholder="Select Class"
        :value="request('class')"
        :options="[]"
    />

    <x-input.dropdown-select
        id="groupFilter"
        name="group"
        placeholder="Select Group"
        :value="request('group')"
        :options="[]"
    />

    <x-input.dropdown-select
        id="sectionFilter"
        name="section"
        placeholder="Select Section"
        :value="request('section')"
        :options="[]"
    />

    <x-input.dropdown-select
        id="sessionFilter"
        name="session"
        placeholder="Select Session"
        :value="request('session')"
        :options="[]"
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
