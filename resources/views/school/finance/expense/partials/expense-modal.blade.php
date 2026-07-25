<x-modal.form
    id="expenseModal"
    form-id="expenseForm"
    title="Expense Registration"
    close-button-id="closeExpenseModal"
    title-class="expense-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
>
    @include('school.finance.expense.partials.inc.form')
</x-modal.form>