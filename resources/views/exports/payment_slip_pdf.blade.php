<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Slip — {{ $student->student_name }}</title>
    @php
        function pdfSlipStatus($payDate, $total, $paid) {
            $due = max((float)$total - (float)$paid, 0);
            $d = $payDate ? \Carbon\Carbon::parse($payDate) : null;
            $today = \Carbon\Carbon::today();
            $isFuture = $d && $d->copy()->startOfDay()->gt($today);
            $isOverdue = $d && $due > 0 && $d->copy()->startOfDay()->lt($today);
            if ((float)$paid >= (float)$total && (float)$total > 0) return $isFuture ? ['Advance', '#2563eb'] : ['Paid', '#059669'];
            if ((float)$paid > 0 && (float)$paid < (float)$total) {
                if ($isFuture) return ['Advance Partial', '#06b6d4'];
                if ($isOverdue) return ['Over Due Partial', '#9333ea'];
                return ['Partial Paid', '#d97706'];
            }
            if ($isOverdue) return ['Over Due', '#dc2626'];
            return ['Due', '#ef4444'];
        }
    @endphp
    <style>
        @page { margin: 15mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif;
            font-size: 11px; color: #1f2937; line-height: 1.4; background: #fff;
            padding: 10mm;
        }

        .header { text-align: center; margin-bottom: 8px; }
        .header .school-name { font-size: 17px; font-weight: 700; color: #0f766e; letter-spacing: 0.02em; }
        .header .school-info { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .header .badge {
            display: inline-block; margin-top: 6px; border: 1px solid #14b8a6; color: #0d9488;
            font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em;
            padding: 2px 14px; border-radius: 999px;
        }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 10px 0 12px 0; }

        table.student-info { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 14px; }
        table.student-info td { padding: 1.5px 0; vertical-align: top; }
        table.student-info .col { width: 50%; }
        table.student-info .lbl { color: #6b7280; }
        table.student-info .lbl.w24 { display: inline-block; width: 96px; }
        table.student-info .lbl.w20 { display: inline-block; width: 80px; }
        table.student-info .colon { color: #9ca3af; margin-right: 6px; }
        table.student-info .val { font-weight: 600; color: #111827; }

        table.payment-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
        table.payment-table th {
            background: #f9fafb; color: #6b7280; font-weight: 600; padding: 6px 5px;
            border: 1px solid #e5e7eb; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.04em;
        }
        table.payment-table td { padding: 5px; border: 1px solid #f3f4f6; vertical-align: top; }
        table.payment-table tr:nth-child(even) td { background: #fafafa; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .summary { float: right; width: 256px; margin-top: 14px; border-collapse: collapse; font-size: 10.5px; }
        .summary td { padding: 6px 10px; border: 1px solid #e5e7eb; }
        .summary td.lbl { background: #f9fafb; color: #6b7280; }
        .summary td.val { text-align: right; font-weight: 700; color: #111827; }
        .c-emerald { color: #059669; }
        .c-red { color: #dc2626; }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
        <div class="school-info">
            @if(!empty($school->village) || !empty($school->upazila) || !empty($school->district))
                <div>{{ implode(', ', array_filter([$school->village, $school->upazila, $school->district ?? null])) }}</div>
            @endif
            @if(!empty($school->mobile))
                <div>Mobile: {{ $school->mobile }}</div>
            @endif
        </div>
        <span class="badge">Payment Invoice</span>
    </div>
    <hr class="divider">

    <table class="student-info">
        <tr>
            <td class="col">
                <div><span class="lbl w24">Student Id</span><span class="colon">:</span><span class="val">{{ $student->student_id_number }}</span></div>
                <div><span class="lbl w24">Student Name</span><span class="colon">:</span><span class="val">{{ $student->student_name }}</span></div>
                <div><span class="lbl w24">Duration</span><span class="colon">:</span><span class="val">{{ $duration }}</span></div>
                <div><span class="lbl w24">Print Date</span><span class="colon">:</span><span class="val">{{ $printDate }}</span></div>
            </td>
            <td class="col">
                <div><span class="lbl w20">Class</span><span class="colon">:</span><span class="val">{{ $student->schoolClass->class_name ?? '—' }}</span></div>
                <div><span class="lbl w20">Group</span><span class="colon">:</span><span class="val">{{ $student->schoolGroup->group_name ?? '—' }}</span></div>
                <div><span class="lbl w20">Section</span><span class="colon">:</span><span class="val">{{ $student->schoolSection->section_name ?? '—' }}</span></div>
                <div><span class="lbl w20">Session</span><span class="colon">:</span><span class="val">{{ $student->schoolSession->session_year ?? '—' }}</span></div>
            </td>
        </tr>
    </table>

    <table class="payment-table">
        <thead>
            <tr>
                <th width="5%">Sl</th>
                <th>Pay Date</th>
                <th>Receive Month</th>
                <th>Method</th>
                <th>Status</th>
                <th>Fee Type</th>
                <th>Fee Name</th>
                <th class="text-right">Payable</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Due</th>
                <th class="text-right">Over Due</th>
            </tr>
        </thead>
        <tbody>
            @php
                $shownFees = [];
                $sumPayable = 0;
                $sumPaid = 0;
                $sumDue = 0;
                $sumOverdue = 0;
            @endphp
            @forelse ($payments as $i => $p)
                @php
                    $total     = (float) ($p->total_payable ?? 0);
                    $paid      = (float) ($p->type_amount   ?? 0);
                    $due       = max($total - $paid, 0);
                    $isOverdue = $due > 0 && $p->pay_date && \Carbon\Carbon::parse($p->pay_date)->startOfDay()->lt(\Carbon\Carbon::today());
                    $feeKey    = $p->fees_type . '||' . $p->fee_name;
                    $showTotal = !in_array($feeKey, $shownFees);
                    if ($showTotal) { $shownFees[] = $feeKey; $sumPayable += $total; }
                    $sumPaid += $paid;
                    $sumDue += $due;
                    if ($isOverdue) { $sumOverdue += $due; }
                    $receiveMonth = $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('F') : '-';
                    [$statusText, $statusColor] = pdfSlipStatus($p->pay_date, $total, $paid);
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('d-M-y') : '-' }}</td>
                    <td class="text-center" style="font-weight:600; color:#111827;">{{ $receiveMonth }}</td>
                    <td class="text-center">{{ $p->pay_method ?? '-' }}</td>
                    <td class="text-center" style="font-weight:600; color:{{ $statusColor }};">{{ $statusText }}</td>
                    <td>{{ $p->fees_type ?? '-' }}</td>
                    <td>{{ $p->fee_name ?? '-' }}</td>
                    <td class="text-right">{{ $showTotal ? number_format($total, 2) : '-' }}</td>
                    <td class="text-right">{{ number_format($paid, 2) }}</td>
                    <td class="text-right">{{ number_format($due, 2) }}</td>
                    <td class="text-right">{{ $isOverdue ? number_format($due, 2) : '0.00' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding:12px; color:#9ca3af;">No payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($payments))
        <table class="summary">
            <tr>
                <td class="lbl">Total Payable</td>
                <td class="val">{{ number_format($sumPayable, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Paid</td>
                <td class="val c-emerald">{{ number_format($sumPaid, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Due</td>
                <td class="val c-red">{{ number_format($sumDue, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Over Due</td>
                <td class="val c-red">{{ number_format($sumOverdue, 2) }}</td>
            </tr>
        </table>
    @endif

</body>
</html>
