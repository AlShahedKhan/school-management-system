<input type="hidden" name="expense_id" id="expense_id">
<div class="relative">
    <x-input.control
        id="expenseDate"
        class="peer"
        type="date"
        name="expense_date"
    />
    <x-input.floating-label for="expenseDate" :floating="false">
        Expense Date
    </x-input.floating-label>
    <div id="expense_date_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control
        id="expenseReason"
        class="peer placeholder:text-transparent"
        name="expense_reason"
        placeholder=" "
    />
    <x-input.floating-label for="expenseReason">
        Expense Reason
    </x-input.floating-label>
    <div id="expense_reason_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>
<div class="relative">
    <x-input.control
        id="expenseAmount"
        class="peer placeholder:text-transparent"
        type="number"
        name="amount"
        placeholder=" "
        min="0"
        step="0.01"
    />
    <x-input.floating-label for="expenseAmount">
        Amount
    </x-input.floating-label>
    <div id="amount_error" class="mt-1 hidden text-[10px] text-red-500"></div>
</div>