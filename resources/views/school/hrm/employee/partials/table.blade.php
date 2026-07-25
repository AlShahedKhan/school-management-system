<x-table
    unstyled
    :empty="$employees->isEmpty()"
    :empty-colspan="9"
    empty-message="No employees found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$employees->hasPages()"
    footer-class="w-full border-t border-gray-300 px-0 py-3"
    scroll-class="donate-table-scroll"
    table-class="donate-fixed-table border-collapse border border-gray-300 text-xs"
    head-class="bg-gray-100"
    class="mb-0 border border-gray-200 bg-white p-2.5 shadow-md sm:p-4"
    style="border-radius:0;"
>
    <x-slot:columns>
        <colgroup>
            <col style="width:5%;">
            <col style="width:18%;">
            <col style="width:14%;">
            <col style="width:14%;">
            <col style="width:12%;">
            <col style="width:13%;">
            <col style="width:8%;">
            <col style="width:11%;">
            <col style="width:10%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            SL
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Name
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Designation
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Mobile Number
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Salary
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Salary Start Date
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Status
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Pay Date
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    <tbody id="employeeTableBody">
    @foreach ($employees as $employee)
        @php
            $statusVal = is_object($employee->employee_status) ? $employee->employee_status->value : (string) $employee->employee_status;
            $statusLabel = is_object($employee->employee_status) ? $employee->employee_status->label() : ucfirst($statusVal);
            $statusClass = strtolower($statusVal) === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            $startDate = $employee->salary_start_date ? \Carbon\Carbon::parse($employee->salary_start_date)->format('Y-m-d') : '-';
        @endphp
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-center">
                {{ $employees->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 font-medium">
                <div class="donate-cell-scroll" title="{{ $employee->name }}">
                    {{ $employee->name }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                {{ $employee->designation }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
                <a
                    href="tel:{{ $employee->mobile_number }}"
                    class="inline-block text-blue-500"
                    style="text-decoration: none !important;">
                    {{ $employee->mobile_number }}
                </a>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">
                ৳{{ number_format($employee->salary_amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $startDate }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <span class="rounded px-2 py-0.5 text-[10px] font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                {{ $employee->pay_date ?? '-' }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Employee {{ $employee->name }}"
                        onclick="editEmployee({{ $employee->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Employee {{ $employee->name }}"
                        onclick="deleteEmployee({{ $employee->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    </tbody>
    <x-slot:footer>
        {{ $employees->links() }}
    </x-slot:footer>
</x-table>