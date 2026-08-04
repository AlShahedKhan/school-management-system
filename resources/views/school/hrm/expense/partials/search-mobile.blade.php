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