<x-modal.form
    id="filterModal"
    form-id="expenseFilterForm"
    title="Expense Filter"
    close-button-id="resetFilter"
    action="{{ route('school.expense') }}"
    method="GET"
    :enctype="null"
    class="expense-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
>
    <div class="relative">
        <x-input.select
            id="expenseFilterMonth"
            name="month"
            :value="request('month')"
        >
            <option value="">Select Month</option>
            <option value="1" @selected(request('month') == 1)>January</option>
            <option value="2" @selected(request('month') == 2)>February</option>
            <option value="3" @selected(request('month') == 3)>March</option>
            <option value="4" @selected(request('month') == 4)>April</option>
            <option value="5" @selected(request('month') == 5)>May</option>
            <option value="6" @selected(request('month') == 6)>June</option>
            <option value="7" @selected(request('month') == 7)>July</option>
            <option value="8" @selected(request('month') == 8)>August</option>
            <option value="9" @selected(request('month') == 9)>September</option>
            <option value="10" @selected(request('month') == 10)>October</option>
            <option value="11" @selected(request('month') == 11)>November</option>
            <option value="12" @selected(request('month') == 12)>December</option>
        </x-input.select>
        <x-input.floating-label for="expenseFilterMonth">
            Select Month
        </x-input.floating-label>
    </div>
    <div class="relative">
        <x-input.select
            id="expenseFilterYear"
            name="year"
            :value="request('year')"
        >
            <option value="">Select Year</option>
            @for ($year = now()->year; $year >= 2020; $year--)
                <option value="{{ $year }}" @selected(request('year') == $year)>
                    {{ $year }}
                </option>
            @endfor
        </x-input.select>
        <x-input.floating-label for="expenseFilterYear">
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