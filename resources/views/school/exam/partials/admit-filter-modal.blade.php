<x-modal.form
    id="filterModal"
    form-id="admitFilterForm"
    title="Admit Card Filter"
    close-button-id="closeAdmitFilterModal"
    title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    <div class="relative">
        <x-input.dropdown-select id="filter_class_name" placeholder="Select Class" :options="[]" />
        <x-input.floating-label for="filter_class_name" :floating="false">Class</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select id="filter_group_name" placeholder="Select Group" :options="[]" />
        <x-input.floating-label for="filter_group_name" :floating="false">Group</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select id="filter_section_name" placeholder="Select Section" :options="[]" />
        <x-input.floating-label for="filter_section_name" :floating="false">Section</x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select id="filter_session_name" placeholder="Select Session" :options="[]" />
        <x-input.floating-label for="filter_session_name" :floating="false">Session</x-input.floating-label>
    </div>

    <div class="relative md:col-span-2">
        <x-input.dropdown-select id="filter_exam_name" placeholder="Select Exam" :options="[]" />
        <x-input.floating-label for="filter_exam_name" :floating="false">Exam Name</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
            <x-button.secondary type="button" onclick="resetFilters()" class="w-full">
                Reset
            </x-button.secondary>
            <x-button.primary type="button" onclick="applyFilters()" class="w-full">
                Apply
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>
