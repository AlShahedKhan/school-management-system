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
            if ((float)$paid >= (float)$total && (float)$total > 0) return $isFuture ? 'Advance' : 'Paid';
            if ((float)$paid > 0 && (float)$paid < (float)$total) {
                if ($isFuture) return 'Advance Partial';
                if ($isOverdue) return 'Over Due Partial';
                return 'Partial Paid';
            }
            if ($isOverdue) return 'Over Due';
            return 'Due';
        }
    @endphp
    <style>
        @page { margin: 15mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif;
            font-size: 10px; color: #000; background: #fff; line-height: 1.4;
        }
        .header { text-align: center; margin-bottom: 6px; }
        .header .school-name { font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; }
        .header .school-info { font-size: 9px; color: #333; margin-top: 2px; }
        .header .school-info span { display: inline-block; margin: 0 4px; }
        .header-divider { border: none; border-top: 1.5px solid #000; margin: 6px 0 10px 0; }

        .student-info { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9.5px; }
        .student-info td { padding: 1.5px 4px; vertical-align: top; border: none; }
        .student-info .left-col { width: 50%; }
        .student-info .right-col { width: 50%; text-align: right; }
        .student-info .right-col .label { margin-left: 2px; }

        table.payment-table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        table.payment-table th {
            background: #3b82f6; color: #fff; font-weight: 700; padding: 5px 4px;
            text-align: center; border: 1px solid #60a5fa; font-size: 8px;
            text-transform: uppercase; letter-spacing: 0.03em;
        }
        table.payment-table td { padding: 3px 4px; border: 1px solid #ccc; vertical-align: middle; }
        table.payment-table tbody tr:nth-child(even) td { background: #f9f9f9; }
        table.payment-table tbody tr:nth-child(odd) td { background: #fff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .status-ref { margin-top: 12px; font-size: 8px; }
        .status-ref .title { font-weight: 700; margin-bottom: 3px; }
        .status-ref .list { display: flex; flex-wrap: wrap; gap: 0 18px; }
        .status-ref .list span { display: inline-block; }

        .c-green { color: #16a34a; }
        .c-blue { color: #2563eb; }
        .c-red { color: #ef4444; }
        .c-orange { color: #f97316; }
        .c-red-dark { color: #b91c1c; }
        .c-purple { color: #9333ea; }
        .c-teal { color: #0d9488; }
        .c-cyan { color: #0891b2; }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
        <div class="school-info">
            @if(!empty($school->village) || !empty($school->upazila))
                <span>{{ implode(', ', array_filter([$school->village, $school->upazila])) }}</span>
            @endif
            @if(!empty($school->mobile))
                <span>| Mobile: {{ $school->mobile }}</span>
            @endif
        </div>
    </div>
    <hr class="header-divider">

    <table class="student-info">
        <tr>
            <td class="left-col">
                <div><span class="label">Student ID</span> : {{ $student->student_id_number }}</div>
                <div><span class="label">Student Name</span> : {{ $student->student_name }}</div>
                <div><span class="label">Duration</span> : {{ $duration }}</div>
                <div><span class="label">Print Date</span> : {{ $printDate }}</div>
            </td>
            <td class="right-col">
                <div><span class="label">Class</span> : {{ $student->schoolClass->class_name ?? '—' }}</div>
                <div><span class="label">Group</span> : {{ $student->schoolGroup->group_name ?? '—' }}</div>
                <div><span class="label">Section</span> : {{ $student->schoolSection->section_name ?? '—' }}</div>
                <div><span class="label">Session</span> : {{ $student->schoolSession->session_year ?? '—' }}</div>
            </td>
        </tr>
    </table>

    <table class="payment-table">
        <thead>
            <tr>
                <th width="18">Sl</th>
                <th width="52">Pay Date</th>
                <th width="52">Receive Month</th>
                <th width="48">Receive Method</th>
                <th width="52">Receive Status</th>
                <th>Fee Type</th>
                <th>Fee Name</th>
                <th width="48">Payable</th>
                <th width="42">Paid</th>
                <th width="42">Due</th>
                <th width="40">Over Due</th>
            </tr>
        </thead>
        <tbody>
            @php
                $shownFees = [];
                $colorMap = [
                    'Paid' => 'c-green',
                    'Partial Paid' => 'c-blue',
                    'Due' => 'c-red',
                    'Due Partial' => 'c-orange',
                    'Over Due' => 'c-red-dark',
                    'Over Due Partial' => 'c-purple',
                    'Advance' => 'c-teal',
                    'Advance Partial' => 'c-cyan',
                ];
            @endphp
            @forelse ($payments as $i => $p)
                @php
                    $total     = (float) ($p->total_payable ?? 0);
                    $paid      = (float) ($p->type_amount   ?? 0);
                    $due       = max($total - $paid, 0);
                    $isOverdue = $due > 0 && $p->pay_date && \Carbon\Carbon::parse($p->pay_date)->startOfDay()->lt(\Carbon\Carbon::today());
                    $feeKey    = $p->fees_type . '||' . $p->fee_name;
                    $showTotal = !in_array($feeKey, $shownFees);
                    if ($showTotal) $shownFees[] = $feeKey;
                    $receiveMonth = $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('F') : '-';
                    $statusText = pdfSlipStatus($p->pay_date, $total, $paid);
                    $statusClass = $colorMap[$statusText] ?? '';
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('j-F-Y') : '-' }}</td>
                    <td class="text-center">{{ $receiveMonth }}</td>
                    <td class="text-center">{{ $p->pay_method ?? '-' }}</td>
                    <td class="text-center {{ $statusClass }}" style="font-weight:600;">{{ $statusText }}</td>
                    <td>{{ $p->fees_type ?? '-' }}</td>
                    <td>{{ $p->fee_name ?? '-' }}</td>
                    <td class="text-right">{{ $showTotal ? number_format($total, 2) : '-' }}</td>
                    <td class="text-right">{{ number_format($paid, 2) }}</td>
                    <td class="text-right">{{ number_format($due, 2) }}</td>
                    <td class="text-center">{{ $isOverdue ? 'YES' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding:12px;color:#888;">No payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


</body>
</html>