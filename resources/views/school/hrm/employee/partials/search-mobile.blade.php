<form method="GET" action="{{ route('school.employee') }}" class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
    <x-input.search
        id="employeeSearchMobile"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search Employee..."
        class="col-span-2 min-w-0"
    />
    @include('school.hrm.employee.partials.restore-mobile')
</form>