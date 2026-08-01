<x-modal.form
    id="filterModal"
    form-id="teacherFilterForm"
    title="Teacher Filter"
    close-button-id="closeFilterModal"
    title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    <div class="relative">
        <x-input.dropdown-select
            id="teacherFilter"
            name="teacher_id"
            placeholder="Select Teacher"
        />
        <x-input.floating-label for="teacherFilter" :floating="false">Teacher</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select
            id="teacherFilterDesignation"
            name="designation"
            placeholder="Select Designation"
        />
        <x-input.floating-label for="teacherFilterDesignation" :floating="false">Designation</x-input.floating-label>
    </div>

    <div class="relative md:col-span-2">
        <x-input.dropdown-select
            id="teacherFilterStatus"
            name="status"
            placeholder="Select Status"
        />
        <x-input.floating-label for="teacherFilterStatus" :floating="false">Status</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
            <x-button.secondary id="resetFilter" type="button" class="w-full">
                Reset
            </x-button.secondary>

            <x-button.primary id="applyFilter" type="button" class="w-full">
                Apply
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
