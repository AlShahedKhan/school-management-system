@php
    $payDateOptions = [];
    for ($i = 1; $i <= 31; $i++) {
        $dayStr = sprintf('Every Day-%02d', $i);
        $payDateOptions[$dayStr] = $dayStr;
    }
@endphp

<input type="hidden" name="teacher_id" id="teacher_id">

<div class="relative">
    <x-input.control id="teacherName" class="peer placeholder:text-transparent" name="name" placeholder=" " required />
    <x-input.floating-label for="teacherName">
        Full name *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="teacherDesignation" class="peer placeholder:text-transparent" name="designation" placeholder=" " />
    <x-input.floating-label for="teacherDesignation">
        Designation *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control
        id="teacherMobile"
        class="peer placeholder:text-transparent"
        name="mobile"
        required
        placeholder=" "
        inputmode="numeric"
        pattern="[0-9]+"
        title="Must provide numbers only." />
    <x-input.floating-label for="teacherMobile">
        Mobile Number *
    </x-input.floating-label>
    <p id="teacherMobileError" class="mt-1 hidden text-[10px] text-red-500">
        Must provide numbers only.
    </p>
</div>

<div class="relative">
    <x-input.control id="teacherEmail" class="peer placeholder:text-transparent" type="email" name="email" placeholder=" " required />
    <x-input.floating-label for="teacherEmail">
        Email address *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="teacherSalary" class="peer placeholder:text-transparent" type="number" step="0.01" name="salary_amount" placeholder=" " />
    <x-input.floating-label for="teacherSalary">
        Salary *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.control id="teacherSalaryStartDate" class="peer placeholder:text-transparent" type="date" name="salary_start_date" placeholder=" " />
    <x-input.floating-label for="teacherSalaryStartDate" :floating="false" class="pointer-events-auto text-slate-500">
        Salary Start Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.dropdown-select
        id="teacherPayDate"
        name="pay_date"
        placeholder="Select Pay Date..."
        :options="$payDateOptions"
    />
    <x-input.floating-label for="teacherPayDate" :floating="false" class="pointer-events-auto text-slate-500">
        Pay Date *
    </x-input.floating-label>
</div>

<div class="relative">
    <x-input.photo />
    <x-input.floating-label for="photoInput" :floating="false" class="pointer-events-auto peer-focus:text-slate-500">
        Profile photo
    </x-input.floating-label>
</div>
