<input type="hidden" name="expense_id" id="expense_id">

<div class="relative">
    <x-input.control id="expenseDate" class="peer placeholder:text-transparent" type="date" name="date" placeholder=" " required />
    <x-input.floating-label for="expenseDate" :floating="false" class="pointer-events-auto text-slate-500">
        Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseInvoiceNo" class="peer placeholder:text-transparent" name="invoice_no" placeholder=" " />
    <x-input.floating-label for="expenseInvoiceNo">
        Invoice No
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseReason" class="peer placeholder:text-transparent" name="expense_reason" placeholder=" " required />
    <x-input.floating-label for="expenseReason">
        Reason *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseDetails" class="peer placeholder:text-transparent" name="details" placeholder=" " />
    <x-input.floating-label for="expenseDetails">
        Details
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseAmount" class="peer placeholder:text-transparent" type="number" step="0.01" name="amount" placeholder=" " required />
    <x-input.floating-label for="expenseAmount">
        Amount *
    </x-input.floating-label>
</div>