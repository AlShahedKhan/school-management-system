<form method="GET" action="{{ route('school.expense') }}" class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
    <x-input.search
        id="expenseSearchMobile"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Expense..."
        class="col-span-2 min-w-0"
    />
    <x-button.secondary id="btnRestoreMobile" onclick="window.location.href='{{ route('school.expense') }}'">
        Restore
    </x-button.secondary>
</form>