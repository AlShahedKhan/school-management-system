<form method="GET" action="{{ route('school.expense') }}" class="flex items-center gap-2">
    <x-input.search
        id="expenseSearch"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Expense..."
        class="w-full lg:w-72"
    />
    <x-button.secondary id="btnRestoreDesktop" onclick="window.location.href='{{ route('school.expense') }}'">
        Restore
    </x-button.secondary>
</form>