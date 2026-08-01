<form method="GET" action="{{ route('school.employee') }}" class="hidden lg:flex items-center gap-2">
    <x-input.search
        id="employeeSearch"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search..."
        class="hidden w-full lg:block lg:w-72"
    />
    @include('school.hrm.employee.partials.restore-desktop')
</form>