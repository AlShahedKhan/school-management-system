<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Slip — {{ $student->student_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    @php
        function slipStatus($payDate, $total, $paid) {
            $due = max((float)$total - (float)$paid, 0);
            $d = $payDate ? \Carbon\Carbon::parse($payDate) : null;
            $today = \Carbon\Carbon::today();
            $isFuture = $d && $d->copy()->startOfDay()->gt($today);
            $isOverdue = $d && $due > 0 && $d->copy()->startOfDay()->lt($today);
            if ((float)$paid >= (float)$total && (float)$total > 0) return $isFuture ? ['Advance', 'text-teal-600'] : ['Paid', 'text-green-600'];
            if ((float)$paid > 0 && (float)$paid < (float)$total) {
                if ($isFuture) return ['Advance Partial', 'text-cyan-600'];
                if ($isOverdue) return ['Over Due Partial', 'text-purple-600'];
                return ['Partial Paid', 'text-blue-600'];
            }
            if ($isOverdue) return ['Over Due', 'text-red-700'];
            return ['Due', 'text-red-500'];
        }
    @endphp
    <style>
        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 20mm !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased p-4 sm:p-8 flex flex-col items-center">

    {{-- Action Buttons --}}
    <div class="no-print w-full max-w-[210mm] flex justify-end gap-3 mb-4">
        <a href="{{ route('school.payment') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-2 px-4 rounded shadow transition">
            &larr; Back
        </a>
        <button onclick="window.print()" class="bg-gray-700 hover:bg-gray-800 text-white text-xs font-medium py-2 px-4 rounded shadow transition">
            Print Slip
        </button>
        <button onclick="downloadSlip('pdf')"
            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-2 px-4 rounded shadow transition">
            Download PDF
        </button>
        <button onclick="downloadSlip('excel')"
            class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-4 rounded shadow transition">
            Download Excel
        </button>
    </div>

    {{-- Slip Container --}}
    <div class="print-container bg-white w-[210mm] min-h-[297mm] p-8 border border-gray-300 shadow-lg flex flex-col justify-between text-[11px] text-gray-800 tracking-tight">

        {{-- ══════════ HEADER ══════════ --}}
        <div>
            <div class="text-center mb-6 py-2">
                <h1 class="text-sm font-bold tracking-wider text-gray-900 whitespace-nowrap">{{ $school->school_name ?? 'School Name' }}</h1>
                <p class="text-gray-600 whitespace-nowrap text-[10px] mt-0.5">
                    {{ implode(', ', array_filter([$school->village, $school->upazila])) }}
                </p>
                @if(!empty($school->mobile))
                    <p class="text-gray-600 whitespace-nowrap text-[10px]">Mobile: {{ $school->mobile }}</p>
                @endif
            </div>

            {{-- ══════════ STUDENT INFO ══════════ --}}
            <div class="grid grid-cols-2 gap-x-12 gap-y-1 mb-4 border-b border-gray-100 pb-4">
                <div class="space-y-0.5">
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 font-medium">Student Id</span>
                        <span class="mr-2">:</span>
                        <span class="truncate">{{ $student->student_id_number }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 font-medium">Student Name</span>
                        <span class="mr-2">:</span>
                        <span class="truncate font-semibold text-gray-900">{{ $student->student_name }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 font-medium">Duration</span>
                        <span class="mr-2">:</span>
                        <span class="truncate">{{ $duration }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 font-medium">Print Date</span>
                        <span class="mr-2">:</span>
                        <span class="truncate">{{ \Carbon\Carbon::now()->format('j-F-Y h:i A') }}</span>
                    </div>
                </div>
                <div class="space-y-0.5 text-right">
                    <div class="flex whitespace-nowrap justify-end">
                        <span class="flex-shrink-0 font-medium">Class</span>
                        <span class="mx-1.5">:</span>
                        <span>{{ $student->schoolClass->class_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap justify-end">
                        <span class="flex-shrink-0 font-medium">Group</span>
                        <span class="mx-1.5">:</span>
                        <span>{{ $student->schoolGroup->group_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap justify-end">
                        <span class="flex-shrink-0 font-medium">Section</span>
                        <span class="mx-1.5">:</span>
                        <span>{{ $student->schoolSection->section_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap justify-end">
                        <span class="flex-shrink-0 font-medium">Session</span>
                        <span class="mx-1.5">:</span>
                        <span>{{ $student->schoolSession->session_year ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- ══════════ PAYMENT TABLE ══════════ --}}
            <div class="w-full">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <tr class="bg-[#3b82f6] text-white text-[10px] font-semibold">
                            <th class="py-1.5 px-1.5 whitespace-nowrap w-6 border border-blue-400/30">Sl</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Pay Date</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Receive Month</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Receive Method</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Receive Status</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Fee Type</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Fee Name</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Payable</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Paid</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap border border-blue-400/30">Due</th>
                            <th class="py-1.5 px-1.5 whitespace-nowrap pr-2 border border-blue-400/30">Over Due</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-[10.5px]">
                        @php $shownFees = []; @endphp
                        @forelse ($payments as $i => $p)
                            @php
                                $total = (float) ($p->total_payable ?? 0);
                                $paid = (float) ($p->type_amount ?? 0);
                                $due = max($total - $paid, 0);
                                $isOverdue = $due > 0 && $p->pay_date && \Carbon\Carbon::parse($p->pay_date)->startOfDay()->lt(\Carbon\Carbon::today());
                                $feeKey = $p->fees_type . '||' . $p->fee_name;
                                $showTotal = !in_array($feeKey, $shownFees);
                                if ($showTotal) $shownFees[] = $feeKey;
                                $receiveMonth = $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('F') : '-';
                                [$statusText, $statusColor] = slipStatus($p->pay_date, $total, $paid);
                            @endphp
                            <tr class="align-top">
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $i + 1 }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('j-F-Y') : '-' }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $receiveMonth }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $p->pay_method ?? '-' }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap font-medium {{ $statusColor }} border border-gray-200/60">{{ $statusText }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $p->fees_type ?? '-' }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $p->fee_name ?? '-' }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ $showTotal ? number_format($total, 2) : '-' }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ number_format($paid, 2) }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap border border-gray-200/60">{{ number_format($due, 2) }}</td>
                                <td class="py-1.5 px-1.5 whitespace-nowrap text-center border border-gray-200/60">{{ $isOverdue ? 'YES' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-gray-400">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>

<script>
function downloadSlip(format) {
    const label = format === 'pdf' ? 'PDF' : 'Excel';
    const actualUrl = '/api/school/payments/slip-' + format + '?' + new URLSearchParams({
        student_id: '{{ request('student_id') }}',
        from_date: '{{ request('from_date') }}',
        to_date: '{{ request('to_date') }}'
    }).toString();

    Swal.fire({
        title: 'Generating ' + label + '...',
        text: 'Please wait.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading()
    });

    // Trigger download — use server's Content-Disposition filename
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = actualUrl;
    document.body.appendChild(iframe);

    // Close loading + show toast after a moment
    setTimeout(() => {
        Swal.close();
        Toastify({
            text: label + ' download started successfully!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: { background: '#16a34a', borderRadius: '4px', fontSize: '13px' },
        }).showToast();
    }, 2000);
}
</script>
</body>
</html>