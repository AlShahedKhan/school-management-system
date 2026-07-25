<x-table
    unstyled
    :empty="$employees->isEmpty()"
    :empty-colspan="7"
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
            <col style="width:6%;">
            <col style="width:24%;">
            <col style="width:18%;">
            <col style="width:18%;">
            <col style="width:14%;">
            <col style="width:10%;">
            <col style="width:10%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Sl
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Employee Name
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Mobile Number
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Designation
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Salary
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Status
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    @foreach ($employees as $employee)
        @php
            $paid = (float) ($employee->paid_amount ?? 0);
            $salary = (float) $employee->salary_amount;
            if ($paid == 0) {
                $status = 'Due';
                $statusClass = 'bg-red-100 text-red-700';
            } elseif ($paid < $salary) {
                $status = 'Partial';
                $statusClass = 'bg-yellow-100 text-yellow-700';
            } elseif ($paid == $salary) {
                $status = 'Paid';
                $statusClass = 'bg-green-100 text-green-700';
            } else {
                $status = 'Advance';
                $statusClass = 'bg-blue-100 text-blue-700';
            }
        @endphp
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="h-8 border border-gray-300 px-3 text-center">
                {{ $employees->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                <div class="donate-cell-scroll" title="{{ $employee->name }}">
                    {{ $employee->name }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3">
                <a
                    href="tel:{{ $employee->mobile_number }}"
                    class="inline-block text-blue-500"
                    style="text-decoration: none !important;">
                    {{ $employee->mobile_number }}
                </a>
            </x-table.td>
            <x-table.td unstyled class="h-8 border border-gray-300 px-3">
                {{ $employee->designation }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-medium">
                ৳{{ number_format($employee->salary_amount, 2) }}
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <span class="rounded px-2 py-1 text-xs font-medium {{ $statusClass }}">
                    {{ $status }}
                </span>
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
    <x-slot:footer>
        {{ $employees->links() }}
    </x-slot:footer>
</x-table>