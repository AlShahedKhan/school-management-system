@php
    $payDateOptions = [];
    for ($i = 1; $i <= 31; $i++) {
        $dayStr = sprintf('Every Day-%02d', $i);
        $payDateOptions[$dayStr] = $dayStr;
    }

    $statusOptions = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
@endphp

<input type="hidden" name="employee_id" id="employee_id">

<div class="relative">
    <x-input.control id="employeeName" class="peer placeholder:text-transparent" name="name" placeholder=" " required />
    <x-input.floating-label for="employeeName">
        Name *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="employeeDesignation" class="peer placeholder:text-transparent" name="designation" placeholder=" " required />
    <x-input.floating-label for="employeeDesignation">
        Designation *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control
        id="employeeMobile"
        class="peer placeholder:text-transparent"
        name="mobile_number"
        required
        placeholder=" "
        inputmode="numeric"
        pattern="[0-9]+"
        title="Must provide numbers only." />
    <x-input.floating-label for="employeeMobile">
        Mobile Number *
    </x-input.floating-label>
    <p id="employeeMobileError" class="mt-1 hidden text-[10px] text-red-500">
        Must provide numbers only.
    </p>
</div>

<div class="relative">
    <x-input.control id="employeeSalary" class="peer placeholder:text-transparent" type="number" step="0.01" name="salary_amount" placeholder=" " required />
    <x-input.floating-label for="employeeSalary">
        Salary *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="employeeSalaryStartDate" class="peer placeholder:text-transparent" type="date" name="salary_start_date" placeholder=" " required />
    <x-input.floating-label for="employeeSalaryStartDate" :floating="false" class="pointer-events-auto text-slate-500">
        Salary Start Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.dropdown-select
        id="employeePayDate"
        name="pay_date"
        placeholder="Select Pay Date..."
        :options="$payDateOptions"
    />
    <x-input.floating-label for="employeePayDate" :floating="false" class="pointer-events-auto text-slate-500">
        Pay Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.dropdown-select
        id="employeeStatus"
        name="employee_status"
        placeholder="Select Status..."
        value="active"
        :options="$statusOptions"
    />
    <x-input.floating-label for="employeeStatus" :floating="false" class="pointer-events-auto text-slate-500">
        employee status *
    </x-input.floating-label>
</div>
