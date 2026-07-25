<x-modal.form
    id="filterModal"
    form-id="teacherFilterForm"
    title="Teacher filter"
    close-button-id="resetFilter"
    action="{{ route('school.teacher-registration') }}"
    method="GET"
    :enctype="null"
    class="teacher-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
>
    <x-input.dropdown-select
        id="teacherFilter"
        name="teacher_id"
        placeholder="Teacher"
        :value="request('teacher_id')"
        :options="$teacherFilterOptions ?? []"
        add-button-id="openTeacherFromFilter"
        add-button-label="Add teacher"
        add-button-target="teacherModal"
    />

    <div class="relative">
        <x-input.control
            id="teacherFilterDesignation"
            class="peer placeholder:text-transparent"
            name="designation"
            placeholder=" "
            :value="request('designation')"
            readonly
        />
        <x-input.floating-label for="teacherFilterDesignation">
            Designation
        </x-input.floating-label>
    </div>

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
