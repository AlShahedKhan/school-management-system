@php
    $currentYear = (int) date('Y');
    $months = [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];

    $yearOptions = [];
    for ($y = $currentYear; $y >= 2020; $y--) {
        $yearOptions[(string)$y] = (string)$y;
    }
@endphp

<x-modal.form
    id="filterModal"
    form-id="expenseFilterForm"
    title="Expense Filter"
    close-button-id="closeFilterModal"
    action="{{ route('school.expense') }}"
    method="GET"
    :enctype="null"
    class="expense-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    fields-class="grid grid-cols-1 gap-3 md:grid-cols-2"
>
    <div class="relative">
        <x-input.dropdown-select
            id="expenseFilterMonth"
            name="month"
            placeholder="Select Month..."
            :options="$months"
            :value="request('month')"
        />
        <x-input.floating-label for="expenseFilterMonth" :floating="false" class="pointer-events-auto text-slate-500">
            Select Month
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select
            id="expenseFilterYear"
            name="year"
            placeholder="Select Year..."
            :options="$yearOptions"
            :value="request('year')"
        />
        <x-input.floating-label for="expenseFilterYear" :floating="false" class="pointer-events-auto text-slate-500">
            Select Year
        </x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
            <x-button.secondary
                id="resetFilter"
                type="button"
                class="w-full"
            >
                Reset
            </x-button.secondary>
            <x-button.primary
                id="applyFilter"
                type="button"
                class="w-full"
            >
                Apply
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>