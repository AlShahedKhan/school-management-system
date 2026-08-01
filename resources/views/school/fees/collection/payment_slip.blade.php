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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @php
        function slipStatus($payDate, $total, $paid) {
            $due = max((float)$total - (float)$paid, 0);
            $d = $payDate ? \Carbon\Carbon::parse($payDate) : null;
            $today = \Carbon\Carbon::today();
            $isFuture = $d && $d->copy()->startOfDay()->gt($today);
            $isOverdue = $d && $due > 0 && $d->copy()->startOfDay()->lt($today);
            if ((float)$paid >= (float)$total && (float)$total > 0) return $isFuture ? ['Advance', 'text-blue-600'] : ['Paid', 'text-emerald-600'];
            if ((float)$paid > 0 && (float)$paid < (float)$total) {
                if ($isFuture) return ['Advance Partial', 'text-cyan-600'];
                if ($isOverdue) return ['Over Due Partial', 'text-purple-600'];
                return ['Partial Paid', 'text-amber-600'];
            }
            if ($isOverdue) return ['Over Due', 'text-red-600'];
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
        body, .print-container { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 antialiased p-4 sm:p-8 flex flex-col items-center" style="font-family: 'Inter', sans-serif;">

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
    <div class="print-container bg-white w-[210mm] min-h-[297mm] p-10 border border-gray-300 shadow-lg flex flex-col justify-between text-[11px] text-gray-800 tracking-tight">

        {{-- ══════════ HEADER ══════════ --}}
        <div>
            <div class="text-center mb-5">
                <h1 class="text-xl font-bold tracking-wide text-teal-700 whitespace-nowrap">{{ $school->school_name ?? 'School Name' }}</h1>
                <p class="text-gray-500 whitespace-nowrap text-[10.5px] mt-1">
                    {{ implode(', ', array_filter([$school->village, $school->upazila, $school->district ?? null])) }}
                </p>
                @if(!empty($school->mobile))
                    <p class="text-gray-500 whitespace-nowrap text-[10.5px]">Mobile: {{ $school->mobile }}</p>
                @endif
                <div class="mt-3 inline-block border border-teal-500 text-teal-600 text-[10px] font-semibold tracking-[0.15em] uppercase px-4 py-1 rounded-full">
                    Payment Invoice
                </div>
            </div>

            <div class="border-t border-gray-200 mb-4"></div>

            {{-- ══════════ STUDENT INFO ══════════ --}}
            <div class="grid grid-cols-2 gap-x-12 gap-y-1.5 mb-5 pb-4">
                <div class="space-y-1">
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 text-gray-500">Student Id</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="truncate font-semibold text-gray-900">{{ $student->student_id_number }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 text-gray-500">Student Name</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="truncate font-semibold text-gray-900">{{ $student->student_name }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 text-gray-500">Duration</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="truncate font-semibold text-gray-900">{{ $duration }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-24 flex-shrink-0 text-gray-500">Print Date</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="truncate font-semibold text-gray-900">{{ \Carbon\Carbon::now()->format('d-F-Y h:i A') }}</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex whitespace-nowrap">
                        <span class="w-20 flex-shrink-0 text-gray-500">Class</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="font-semibold text-gray-900">{{ $student->schoolClass->class_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-20 flex-shrink-0 text-gray-500">Group</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="font-semibold text-gray-900">{{ $student->schoolGroup->group_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-20 flex-shrink-0 text-gray-500">Section</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="font-semibold text-gray-900">{{ $student->schoolSection->section_name ?? '—' }}</span>
                    </div>
                    <div class="flex whitespace-nowrap">
                        <span class="w-20 flex-shrink-0 text-gray-500">Session</span>
                        <span class="mr-2 text-gray-400">:</span>
                        <span class="font-semibold text-gray-900">{{ $student->schoolSession->session_year ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- ══════════ PAYMENT TABLE ══════════ --}}
            <div class="w-full">
                <table class="w-full text-left border-collapse table-auto rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-[9.5px] font-semibold uppercase tracking-wide">
                            <th class="py-2 px-2 whitespace-nowrap w-6 border border-gray-200">Sl</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Pay Date</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Receive Month</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Method</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Status</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Fee Type</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200">Fee Name</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200 text-right">Payable</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200 text-right">Paid</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-gray-200 text-right">Due</th>
                            <th class="py-2 px-2 whitespace-nowrap pr-2 border border-gray-200 text-right">Over Due</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-[10.5px]">
                        @php
                            $shownFees = [];
                            $sumPayable = 0;
                            $sumPaid = 0;
                            $sumDue = 0;
                            $sumOverdue = 0;
                        @endphp
                        @forelse ($payments as $i => $p)
                            @php
                                $total = (float) ($p->total_payable ?? 0);
                                $paid = (float) ($p->type_amount ?? 0);
                                $due = max($total - $paid, 0);
                                $isOverdue = $due > 0 && $p->pay_date && \Carbon\Carbon::parse($p->pay_date)->startOfDay()->lt(\Carbon\Carbon::today());
                                $feeKey = $p->fees_type . '||' . $p->fee_name;
                                $showTotal = !in_array($feeKey, $shownFees);
                                if ($showTotal) { $shownFees[] = $feeKey; $sumPayable += $total; }
                                $sumPaid += $paid;
                                $sumDue += $due;
                                if ($isOverdue) { $sumOverdue += $due; }
                                $receiveMonth = $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('F') : '-';
                                [$statusText, $statusColor] = slipStatus($p->pay_date, $total, $paid);
                            @endphp
                            <tr class="align-top even:bg-gray-50/40">
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100">{{ $i + 1 }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100">{{ $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('d-M-y') : '-' }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100 font-semibold text-gray-900">{{ $receiveMonth }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100">{{ $p->pay_method ?? '-' }}</td>
                                <td class="py-2 px-2 whitespace-nowrap font-semibold {{ $statusColor }} border border-gray-100">{{ $statusText }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100">{{ $p->fees_type ?? '-' }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100">{{ $p->fee_name ?? '-' }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100 text-right">{{ $showTotal ? number_format($total, 2) : '-' }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100 text-right">{{ number_format($paid, 2) }}</td>
                                <td class="py-2 px-2 whitespace-nowrap border border-gray-100 text-right">{{ number_format($due, 2) }}</td>
                                <td class="py-2 px-2 whitespace-nowrap text-right border border-gray-100">{{ $isOverdue ? number_format($due, 2) : '0.00' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-gray-400">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ══════════ SUMMARY BOX ══════════ --}}
            @if(count($payments))
                <div class="flex justify-end mt-5">
                    <table class="text-[11px] border-collapse w-64">
                        <tbody>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-500 bg-gray-50">Total Payable</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-gray-900">{{ number_format($sumPayable, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-500 bg-gray-50">Paid</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-emerald-600">{{ number_format($sumPaid, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-500 bg-gray-50">Due</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-red-600">{{ number_format($sumDue, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-500 bg-gray-50">Over Due</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-red-600">{{ number_format($sumOverdue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
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