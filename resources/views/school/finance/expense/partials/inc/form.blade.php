@php
    $monthOptions = [
        'January' => 'January',
        'February' => 'February',
        'March' => 'March',
        'April' => 'April',
        'May' => 'May',
        'June' => 'June',
        'July' => 'July',
        'August' => 'August',
        'September' => 'September',
        'October' => 'October',
        'November' => 'November',
        'December' => 'December',
    ];

    $currentYear = (int) date('Y');
    $yearOptions = [];
    for ($y = $currentYear - 2; $y <= $currentYear + 5; $y++) {
        $yearOptions[(string)$y] = (string)$y;
    }
@endphp

<input type="hidden" name="expense_id" id="expense_id">

<div class="relative">
    <x-input.control id="expenseDate" class="peer placeholder:text-transparent" type="date" name="date" placeholder=" " required />
    <x-input.floating-label for="expenseDate" :floating="false" class="pointer-events-auto text-slate-500">
        Expense Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseReason" class="peer placeholder:text-transparent" name="expense_reason" placeholder=" " required />
    <x-input.floating-label for="expenseReason">
        Expense Reason *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.dropdown-select
        id="expenseMonth"
        name="month"
        placeholder="Select Month..."
        :options="$monthOptions"
    />
    <x-input.floating-label for="expenseMonth" :floating="false" class="pointer-events-auto text-slate-500">
        Select Month *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.dropdown-select
        id="expenseYear"
        name="year"
        placeholder="Select Year..."
        :options="$yearOptions"
    />
    <x-input.floating-label for="expenseYear" :floating="false" class="pointer-events-auto text-slate-500">
        Select Year *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="expenseAmount" class="peer placeholder:text-transparent" type="number" step="0.01" name="amount" placeholder=" " required />
    <x-input.floating-label for="expenseAmount">
        Amount *
    </x-input.floating-label>
</div>