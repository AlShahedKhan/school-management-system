@php
    $currentYear = (int) date('Y');
    $currentMonth = (string) date('n');
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
@endphp

<div class="col-span-1 md:col-span-2 space-y-4 w-full">
    <!-- Type Selection (Employee / Teacher) -->
    <div class="flex items-center gap-4 bg-slate-50 p-2.5 border border-slate-200 rounded">
        <label class="text-xs font-semibold text-slate-700">Staff Type:</label>
        <label class="inline-flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
            <input type="radio" name="type" value="employee" checked class="accent-blue-600 focus:ring-0">
            <span>Employee</span>
        </label>
        <label class="inline-flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
            <input type="radio" name="type" value="teacher" class="accent-blue-600 focus:ring-0">
            <span>Teacher</span>
        </label>
    </div>

    <!-- Select Employee / Teacher Dropdown -->
    <div id="employeeSelectWrapper" class="relative">
        <x-input.dropdown-select
            id="payrollEmployeeId"
            name="employee_id"
            placeholder="Select Employee..."
            :options="$employeeOptions"
        />
        <x-input.floating-label for="payrollEmployeeId" :floating="false" class="pointer-events-auto text-slate-500">
            Select Employee *
        </x-input.floating-label>
    </div>

    <div id="teacherSelectWrapper" class="relative hidden">
        <x-input.dropdown-select
            id="payrollTeacherId"
            name="teacher_id"
            placeholder="Select Teacher..."
            :options="$teacherOptions"
        />
        <x-input.floating-label for="payrollTeacherId" :floating="false" class="pointer-events-auto text-slate-500">
            Select Teacher *
        </x-input.floating-label>
    </div>

    <!-- Auto-Loaded Details Card -->
    <div id="staffDetailsCard" class="hidden rounded border border-blue-100 bg-blue-50/60 p-3 text-xs text-slate-700">
        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
            <div><span class="font-medium text-slate-500">Designation:</span> <strong id="cardDesignation" class="text-slate-800">-</strong></div>
            <div><span class="font-medium text-slate-500">Mobile:</span> <strong id="cardMobile" class="text-slate-800">-</strong></div>
            <div><span class="font-medium text-slate-500">Monthly Salary:</span> <strong id="cardSalary" class="text-blue-700">৳0.00</strong></div>
            <div><span class="font-medium text-slate-500">Running Due:</span> <strong id="cardDue" class="text-red-600">৳0.00</strong></div>
            <div><span class="font-medium text-slate-500">Overdue (Old):</span> <strong id="cardOverdue" class="text-red-700">৳0.00</strong></div>
            <div><span class="font-medium text-slate-500">Total Due:</span> <strong id="cardTotalDue" class="font-bold text-red-800">৳0.00</strong></div>
        </div>
    </div>

    <!-- Pay Type & Month/Year -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.dropdown-select
                id="payrollPayType"
                name="pay_type"
                placeholder="Select Pay Type..."
                :options="['running' => 'Running Month', 'due' => 'Due', 'overdue' => 'Over Due']"
                value="running"
            />
            <x-input.floating-label for="payrollPayType" :floating="false" class="pointer-events-auto text-slate-500">
                Pay Type
            </x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="payrollMonth"
                name="receive_month"
                placeholder="Select Month..."
                :options="$months"
                :value="$currentMonth"
            />
            <x-input.floating-label for="payrollMonth" :floating="false" class="pointer-events-auto text-slate-500">
                Pay Month *
            </x-input.floating-label>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="relative">
            @php
                $yearOptions = [];
                for ($y = $currentYear; $y >= 2020; $y--) {
                    $yearOptions[(string)$y] = (string)$y;
                }
            @endphp
            <x-input.dropdown-select
                id="payrollYear"
                name="receive_year"
                placeholder="Select Year..."
                :options="$yearOptions"
                :value="(string)$currentYear"
            />
            <x-input.floating-label for="payrollYear" :floating="false" class="pointer-events-auto text-slate-500">
                Pay Year *
            </x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                id="payrollReceiveDate"
                class="peer"
                type="date"
                name="receive_date"
                value="{{ date('Y-m-d') }}"
                placeholder=" "
            />
            <x-input.floating-label for="payrollReceiveDate" :floating="true">
                Payment Date *
            </x-input.floating-label>
        </div>
    </div>

    <!-- Amount & Payment Method -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="relative">
            <x-input.control
                id="payrollAmount"
                class="peer"
                type="number"
                step="0.01"
                min="1"
                name="receive_amount"
                placeholder=" "
                required
            />
            <x-input.floating-label for="payrollAmount">
                Pay Amount (৳) *
            </x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="payrollPaymentMethod"
                name="payment_method"
                placeholder="Payment Method..."
                :options="['cash' => 'Cash', 'bank' => 'Bank Transfer', 'online' => 'Mobile Banking / Online']"
                value="cash"
            />
            <x-input.floating-label for="payrollPaymentMethod" :floating="false" class="pointer-events-auto text-slate-500">
                Payment Method
            </x-input.floating-label>
        </div>
    </div>

    <!-- Note -->
    <div class="relative">
        <x-input.control
            id="payrollNote"
            class="peer"
            name="note"
            placeholder=" "
        />
        <x-input.floating-label for="payrollNote">
            Note / Remarks
        </x-input.floating-label>
    </div>
</div>
