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

    $school = \App\Models\School::where('user_id', Auth::id())->first();
    $employeeOptions = [];
    $teacherOptions = [];
    if ($school) {
        $employeeOptions = \App\Models\Employee::where('school_id', $school->id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        $teacherOptions = \App\Models\Teacher::where('school_id', $school->id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
    $typeOptions = [
        'employee' => 'Employee',
        'teacher'  => 'Teacher',
    ];
@endphp

<x-modal.form
    id="filterModal"
    form-id="payrollFilterForm"
    title="Payroll Filter"
    close-button-id="closeFilterModal"
    action="{{ route('school.payroll') }}"
    method="GET"
    :enctype="null"
    class="payroll-filter-modal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[540px]"
    fields-class="grid grid-cols-1 gap-3 md:grid-cols-2"
>
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif

    <div class="relative">
        <x-input.dropdown-select
            id="payrollFilterType"
            name="type"
            placeholder="Select Type..."
            :options="$typeOptions"
            :value="request('type')"
        />
        <x-input.floating-label for="payrollFilterType" :floating="false" class="pointer-events-auto text-slate-500">
            Type (Teacher / Employee)
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select
            id="payrollFilterEmployee"
            name="employee_id"
            placeholder="Select Employee..."
            :options="$employeeOptions"
            :value="request('employee_id')"
        />
        <x-input.floating-label for="payrollFilterEmployee" :floating="false" class="pointer-events-auto text-slate-500">
            Select Employee
        </x-input.floating-label>
    </div>

    <div class="relative">
        <x-input.dropdown-select
            id="payrollFilterMonth"
            name="month"
            placeholder="Select Month..."
            :options="$months"
            :value="request('month')"
        />
        <x-input.floating-label for="payrollFilterMonth" :floating="false" class="pointer-events-auto text-slate-500">
            Select Month
        </x-input.floating-label>
    </div>

    <div class="relative">
        @php
            $yearOptions = [];
            for ($y = $currentYear; $y >= 2020; $y--) {
                $yearOptions[(string)$y] = (string)$y;
            }
        @endphp
        <x-input.dropdown-select
            id="payrollFilterYear"
            name="year"
            placeholder="Select Year..."
            :options="$yearOptions"
            :value="request('year')"
        />
        <x-input.floating-label for="payrollFilterYear" :floating="false" class="pointer-events-auto text-slate-500">
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
