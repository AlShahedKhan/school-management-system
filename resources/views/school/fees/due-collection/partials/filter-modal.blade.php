<x-modal.form
    id="filterModal"
    form-id="filterForm"
    title="Due Filter"
    close-button-id="resetFilter"
    action="#"
    method="GET"
    :enctype="null"
    class="due-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
>
    <x-input.dropdown-select
        id="classFilter"
        name="class"
        placeholder="Select Class"
        add-button-id="openClassFromDueFilter"
        add-button-label="Add class"
        add-button-target="classModal"
    />
    <x-input.dropdown-select
        id="groupFilter"
        name="group"
        placeholder="Select Group"
        add-button-id="openGroupFromDueFilter"
        add-button-label="Add group"
        add-button-target="groupModal"
    />
    <x-input.dropdown-select
        id="sectionFilter"
        name="section"
        placeholder="Select Section"
        add-button-id="openSectionFromDueFilter"
        add-button-label="Add section"
        add-button-target="sectionModal"
    />
    <x-input.dropdown-select
        id="sessionFilter"
        name="session"
        placeholder="Select Session"
        add-button-id="openSessionFromDueFilter"
        add-button-label="Add session"
        add-button-target="sessionModal"
    />
    <x-input.dropdown-select
        id="studentFilter"
        name="student"
        placeholder="Select Student"
    />
    <x-input.dropdown-select
        id="statusFilter"
        name="status"
        placeholder="All Statuses"
    />

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
            <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
