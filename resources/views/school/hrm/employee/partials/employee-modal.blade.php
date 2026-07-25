<x-modal.form
    id="employeeModal"
    form-id="employeeForm"
    title="Employee Registration"
    close-button-id="closeEmployeeModal"
    title-class="employee-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    @include('school.hrm.employee.partials.inc.form')

    <x-slot:footer>
        <div id="employeeModalFooter" class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4">
            <x-button.secondary id="closeEmployeeModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>

            <button id="enableEditBtn" type="button" onclick="enableFormEditing()" class="w-full hidden h-8 rounded border border-blue-600 bg-blue-600 px-3 text-xs font-medium text-white transition-colors hover:bg-blue-700">
                Edit
            </button>

            <x-button.primary id="submitEmployeeBtn" type="submit" class="w-full">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>