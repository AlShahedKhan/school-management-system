<x-modal.form
    id="expenseModal"
    form-id="expenseForm"
    title="Expense Registration"
    close-button-id="closeExpenseModal"
    title-class="expense-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    @include('school.hrm.expense.partials.inc.form')

    <x-slot:footer>
        <div id="expenseModalFooter" class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4">
            <x-button.secondary id="closeExpenseModal" type="button" class="w-full">
                Cancel
            </x-button.secondary>

            <button id="enableEditBtn" type="button" onclick="enableFormEditing()" class="w-full hidden h-8 rounded border border-blue-600 bg-blue-600 px-3 text-xs font-medium text-white transition-colors hover:bg-blue-700">
                Edit
            </button>

            <x-button.primary id="submitExpenseBtn" type="submit" class="w-full">
                Save
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>