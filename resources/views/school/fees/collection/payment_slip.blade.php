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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">
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
        :root {
            --brand-green: #154734;
            --brand-green-dark: #0f3626;
        }
        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; max-width: none !important; }
            .print-content { padding: 20mm 20mm 0 20mm !important; }
            .table-scroll { overflow: visible !important; }
            .table-scroll table { min-width: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
        body, .print-container { font-family: 'Inter', sans-serif; }

        .dropcap-word .cap {
            font-size: 1.55em;
            line-height: 1;
        }
        .dropcap-word .rest {
            letter-spacing: 0.04em;
        }
        .school-name {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .brand-bg { background-color: var(--brand-green); }
        .brand-text { color: var(--brand-green); }
        .brand-border { border-color: var(--brand-green); }
        .payment-table th,
        .payment-table td,
        .summary-table th,
        .summary-table td {
            font-weight: 600;
        }
        .slip-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -46%);
            width: 60%;
            max-width: 400px;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }
        .slip-watermark img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .slip-content-above {
            position: relative;
            z-index: 1;
        }
        .border-bg{
            border: 2px solid rgb(21 71 52);
        }
    </style>
</head>
<body class="bg-gray-100 antialiased p-4 sm:p-8 flex flex-col items-center" style="font-family: 'Inter', sans-serif;">

    {{-- Action Buttons --}}
    <div class="no-print w-full max-w-[210mm] flex flex-wrap justify-end gap-3 mb-4">
        <a href="{{ route('school.payment') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-2 px-4 shadow transition">
            &larr; Back
        </a>
        <button onclick="window.print()" class="bg-gray-700 hover:bg-gray-800 text-white text-xs font-medium py-2 px-4 shadow transition">
            Print Slip
        </button>
        <button onclick="downloadSlip('pdf')"
            class="brand-bg hover:opacity-90 text-white text-xs font-medium py-2 px-4 shadow transition">
            Download PDF
        </button>
        <button onclick="downloadSlip('excel')"
            class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-4 shadow transition">
            Download Excel
        </button>
    </div>

    {{-- Slip Container --}}
    <div class="print-container relative bg-white w-full max-w-[210mm] min-h-[auto] sm:min-h-[297mm] border shadow-lg flex flex-col justify-between text-[11px] text-gray-800 tracking-tight overflow-hidden">

        {{-- Watermark --}}
        <div class="slip-watermark" aria-hidden="true">
            @if(!empty($school->logo))
                <img src="{{ asset('storage/' . $school->logo) }}" alt="watermark">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="watermark">
            @endif
        </div>

        <div class="print-content slip-content-above px-4 sm:px-10 pt-4 sm:pt-10">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="flex flex-col items-center text-center mb-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white flex items-center justify-center overflow-hidden mb-3">
                    @if(!empty($school->logo))
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->school_name }}" class="w-full h-full object-cover">
                    @else
                        <svg viewBox="0 0 64 64" class="w-12 h-12 sm:w-14 sm:h-14 brand-text" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 4c1.6 0 3 1.3 3 3v3.3c3.4.9 6 4 6 7.7v2h5c2.2 0 4 1.8 4 4v2h-4v6c6.6 2 12 8.6 12 16v6H6v-6c0-7.4 5.4-14 12-16v-6H14v-2c0-2.2 1.8-4 4-4h5v-2c0-3.7 2.6-6.8 6-7.7V7c0-1.7 1.4-3 3-3zm0 8c-1.7 0-3 1.3-3 3v2h6v-2c0-1.7-1.3-3-3-3z"/>
                        </svg>
                    @endif
                </div>
                <h1 class="school-name dropcap-word text-lg sm:text-xl font-bold brand-text whitespace-nowrap">
                    {{$school->school_name ?? ''}}
                </h1>
                <p class="text-[12] mt-2 flex items-center justify-center gap-1 font-semibold espace-nowrap brand-text">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5-4.5-7-8.14-7-11.5a7 7 0 1114 0c0 3.36-2.5 7-7 11.5z"/><circle cx="12" cy="9.5" r="2.25"/></svg>
                    {{ implode(', ', array_filter([$school->village ?? null])) }}
                </p>
                @if(!empty($school->mobile))
                    <p class="text-[12] mt-0.5 flex items-center justify-center gap-1 font-bold whitespace-nowrap brand-text">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.97.76l1 4a1 1 0 01-.27.95L7.4 10.3a12 12 0 006.3 6.3l1.6-1.58a1 1 0 01.95-.27l4 1a1 1 0 01.76.97V19a2 2 0 01-2 2h-1C10.4 21 3 13.6 3 4.5V5z"/></svg>
                        Mobile: {{ $school->mobile }}
                    </p>
                @endif
            </div>

            <div class="relative flex items-center justify-center my-5">
                <div class="absolute inset-x-0 top-1/2 border-t border-bg"></div>
                <span class="relative brand-bg text-white text-[10px] sm:text-[11px] font-bold tracking-[0.25em] px-6 py-1.5">
                    PAYMENT INVOICE
                </span>
            </div>

            {{-- ══════════ STUDENT INFO ══════════ --}}
            <div class="relative border border-bg mb-5 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1.5">
                    <div class="space-y-1.5 sm:pr-6">
                        <div class="flex whitespace-nowrap">
                            <span class="w-24 flex-shrink-0 font-semibold">Student Id</span>
                            <span class="mr-2">:</span>
                            <span class="truncate font-semibold">{{ $student->student_id_number }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-24 flex-shrink-0 font-semibold">Student Name</span>
                            <span class="mr-2">:</span>
                            <span class="truncate font-semibold">{{ $student->student_name }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-24 flex-shrink-0 font-semibold">Duration</span>
                            <span class="mr-2">:</span>
                            <span class="truncate font-semibold">{{ $duration }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-24 flex-shrink-0 font-semibold">Print Date</span>
                            <span class="mr-2">:</span>
                            <span class="truncate font-semibold">{{ \Carbon\Carbon::now()->format('d-F-Y h:i A') }}</span>
                        </div>
                    </div>
                    <div class="space-y-1.5 sm:pl-6 mt-3 sm:mt-0">
                        <div class="flex whitespace-nowrap">
                            <span class="w-20 flex-shrink-0 font-semibold">Class</span>
                            <span class="mr-2">:</span>
                            <span class="font-semibold">{{ $student->schoolClass->class_name ?? '—' }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-20 flex-shrink-0 font-semibold">Group</span>
                            <span class="mr-2">:</span>
                            <span class="font-semibold">{{ $student->schoolGroup->group_name ?? '—' }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-20 flex-shrink-0 font-semibold">Section</span>
                            <span class="mr-2">:</span>
                            <span class="font-semibold">{{ $student->schoolSection->section_name ?? '—' }}</span>
                        </div>
                        <div class="flex whitespace-nowrap">
                            <span class="w-20 flex-shrink-0 font-semibold">Session</span>
                            <span class="mr-2">:</span>
                            <span class="font-semibold">{{ $student->schoolSession->session_year ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="hidden sm:block absolute left-1/2 top-4 bottom-4 border-l border-bg"></div>
            </div>

            {{-- ══════════ PAYMENT TABLE ══════════ --}}
            <div class="table-scroll w-full overflow-x-auto">
                <table class="w-full min-w-[640px] text-left border-collapse table-auto overflow-hidden">
                    <thead>
                        <tr class="brand-bg text-white text-[9.5px] font-semibold tracking-wide">
                            <th class="py-2 px-2 whitespace-nowrap w-6 border border-white/10">SL</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Pay Date</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Receive Month</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Method</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Status</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Fee Type</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10">Fee Name</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10 text-right">Payable</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10 text-right">Paid</th>
                            <th class="py-2 px-2 whitespace-nowrap border border-white/10 text-right">Due</th>
                            <th class="py-2 px-2 whitespace-nowrap pr-2 border border-white/10 text-right">Over Due</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-[10.5px] font-semibold">
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
                            <tr class="align-top even:bg-gray-50/60">
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
                                <td class="py-2 px-2 whitespace-nowrap text-right border border-gray-100 {{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">{{ $isOverdue ? number_format($due, 2) : '0.00' }}</td>
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
                    <table class="text-[11px] border-collapse w-64 border border-bg">
                        <thead>
                            <tr>
                                <th colspan="2" class="brand-bg text-white text-center py-1.5 tracking-[0.15em] text-[10px] font-bold">SUMMARY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-600 bg-gray-50 font-semibold">Total Payable</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-gray-900">{{ number_format($sumPayable, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-600 bg-gray-50 font-semibold">Paid</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-emerald-600">{{ number_format($sumPaid, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-600 bg-gray-50 font-semibold">Due</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-red-600">{{ number_format($sumDue, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 border border-gray-200 text-gray-600 bg-gray-50 font-semibold">Over Due</td>
                                <td class="py-1.5 px-3 border border-gray-200 text-right font-bold text-red-600">{{ number_format($sumOverdue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

        {{-- ══════════ FOOTER ══════════ --}}
        <div class="slip-content-above mt-6">
            <div class="flex items-center justify-center gap-2 text-gray-400 text-[10.5px] py-2">
                <span class="w-2 h-2 rotate-45 brand-bg inline-block"></span>
                <span class="w-2 h-2 rotate-45 brand-bg inline-block"></span>
                <span class="text-gray-600">Thank you for your feedback!</span>
                <span class="w-2 h-2 rotate-45 brand-bg inline-block"></span>
                <span class="w-2 h-2 rotate-45 brand-bg inline-block"></span>
            </div>
            <div class="brand-bg text-white text-center py-2.5 text-sm sm:text-base tracking-wide school-name">
                ASTHA ACADEMICS
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