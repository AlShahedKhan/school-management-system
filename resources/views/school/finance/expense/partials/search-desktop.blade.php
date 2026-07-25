<form method="GET" action="{{ route('school.expense') }}" class="hidden lg:flex items-center gap-2">
    <x-input.search
        id="expenseSearch"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search..."
        class="hidden w-full lg:block lg:w-72"
    />
    @include('school.finance.expense.partials.restore-desktop')
</form>