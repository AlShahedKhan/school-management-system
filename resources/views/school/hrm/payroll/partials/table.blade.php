<x-table
    unstyled
    :empty="$payrolls->isEmpty()"
    :empty-colspan="13"
    empty-message="No payroll records found."
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$payrolls->hasPages()"
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
            <col style="width:16%;">
            <col style="width:12%;">
            <col style="width:12%;">
            <col style="width:9%;">
            <col style="width:9%;">
            <col style="width:9%;">
            <col style="width:8%;">
            <col style="width:8%;">
            <col style="width:10%;">
            <col style="width:8%;">
            <col style="width:7%;">
            <col style="width:7%;">
        </colgroup>
    </x-slot:columns>
    <x-slot:head>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Sl
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-left font-semibold">
            Employee
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
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Due
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-right font-semibold">
            Receive Amount
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Receive Month
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Receive Year
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Receive Date
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Payment Method
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Status
        </x-table.th>
        <x-table.th unstyled class="h-8 border border-gray-300 px-3 text-center font-semibold">
            Action
        </x-table.th>
    </x-slot:head>
    @foreach ($payrolls as $payroll)
        @php
            $employee = $payroll->employee;
            $totalPaid = $employee->payrolls
                ->where('receive_month', $payroll->receive_month)
                ->where('receive_year', $payroll->receive_year)
                ->sum('receive_amount');
            $salary = $employee->salary_amount;
            $due = max(0, $salary - $totalPaid);
            $monthEnd = \Carbon\Carbon::create(
                $payroll->receive_year,
                $payroll->receive_month
            )->endOfMonth();
            if ($totalPaid == 0) {
                $status = now()->gt($monthEnd) ? 'Over Due' : 'Due';
            } elseif ($totalPaid < $salary) {
                $status = now()->gt($monthEnd) ? 'Over Due' : 'Partial';
            } elseif ($totalPaid == $salary) {
                $status = 'Paid';
            } else {
                $status = 'Advance';
            }
            $statusClass = match ($status) {
                'Paid' => 'bg-green-100 text-green-700',
                'Partial' => 'bg-yellow-100 text-yellow-700',
                'Due' => 'bg-red-100 text-red-700',
                'Over Due' => 'bg-orange-100 text-orange-700',
                default => 'bg-blue-100 text-blue-700',
            };
        @endphp
        <x-table.row unstyled class="hover:bg-gray-50">
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                {{ $payrolls->firstItem() + $loop->index }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3">
                <div class="donate-cell-scroll">
                    {{ $employee->name }}
                </div>
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3">
                <a
                    href="tel:{{ $employee->mobile_number }}"
                    class="inline-block text-blue-500"
                    style="text-decoration: none !important;">
                    {{ $employee->mobile_number }}
                </a>
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3">
                {{ $employee->designation }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-left">
                ৳{{ number_format($salary,2) }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-left">
                ৳{{ number_format($due,2) }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-left font-medium">
                ৳{{ number_format($payroll->receive_amount,2) }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                {{ \Carbon\Carbon::create()->month($payroll->receive_month)->format('F') }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                {{ $payroll->receive_year }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                {{ $payroll->receive_date?->format('j-F-Y') }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                {{ $payroll->payment_method->label() }}
            </x-table.td>
            <x-table.td unstyled class="border border-gray-300 px-3 text-center">
                <span class="rounded px-2 py-1 text-xs font-medium {{ $statusClass }}">
                    {{ $status }}
                </span>
            </x-table.td>
            <x-table.td unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                <x-action.group>
                    <x-action.button
                        variant="edit"
                        label="Edit Payroll"
                        onclick="editPayroll({{ $payroll->id }})"
                    />
                    <x-action.button
                        variant="delete"
                        label="Delete Payroll"
                        onclick="deletePayroll({{ $payroll->id }})"
                    />
                </x-action.group>
            </x-table.td>
        </x-table.row>
    @endforeach
    <x-slot:footer>
        {{ $payrolls->links() }}
    </x-slot:footer>
</x-table>