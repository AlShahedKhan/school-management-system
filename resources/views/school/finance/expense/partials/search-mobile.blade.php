<form method="GET" action="{{ route('school.expense') }}" class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
    <x-input.search
        id="expenseSearchMobile"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Faculty..."
        class="col-span-2 min-w-0"
    />
    @include('school.finance.expense.partials.restore-mobile')
</form>