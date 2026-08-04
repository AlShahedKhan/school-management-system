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
                if ($isFuture) return ['Advance Partial', '#0891b2'];
                if ($isOverdue) return ['Over Due Partial', '#9333ea'];
                return ['Partial Paid', '#d97706'];
            }
            if ($isOverdue) return ['Over Due', '#dc2626'];
            return ['Due', '#ef4444'];
        }
    @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        @page { size: A4 portrait; margin: 25mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 11px; color: #1f2937; line-height: 1.4; background: #fff;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* HEADER */
        .header { text-align: center; margin-bottom: 20px; }
        .logo-wrap {
            width: 96px; height: 96px; margin: 0 auto 12px auto;
            background: #fff; overflow: hidden; line-height: 96px; text-align: center;
        }
        .logo-wrap img { width: 96px; height: 96px; }
        .logo-wrap .no-logo {
            display: inline-block; font-size: 40px; font-weight: 700; color: #154734; line-height: 96px;
            font-family: 'DejaVu Serif', serif;
        }
        .school-name { font-size: 18px; font-weight: 700; color: #154734; text-transform: uppercase; }
        .address { font-size: 12px; color: #154734; font-weight: 600; margin-top: 8px;}
        .mobile { font-size: 12px; color: #154734; font-weight: 700; margin-top: 2px;}

        /* PAYMENT INVOICE banner */
        .banner {
            border-top: 2px solid #154734;
            margin: 20px 0;
            text-align: center;
            line-height: 0;
        }
        .banner span {
            display: inline-block;
            background: #154734; color: #fff;
            font-size: 11px; font-weight: 700; letter-spacing: 0.25em;
            padding: 6px 24px;
            position: relative; top: -13px;
            line-height: 1.4;
        }

        /* STUDENT INFO */
        table.student-info {
            width: 100%; border: 2px solid #154734; border-collapse: collapse;
            margin-bottom: 20px; font-size: 11px;
        }
        table.student-info td { padding: 16px 12px; vertical-align: top; }
        table.student-info td.divider { border-left: 2px solid #154734; }
        table.student-info .row { margin-bottom: 6px; white-space: nowrap; }
        table.student-info .lbl { font-weight: 600; color: #111827; }
        table.student-info .colon { color: #9ca3af; margin: 0 6px; }
        table.student-info .val { font-weight: 600; color: #111827; }

        /* PAYMENT TABLE */
        table.payment-table { width: 100%; border-collapse: collapse; }
        table.payment-table thead { display: table-header-group; }
        table.payment-table th {
            background: #154734; color: #fff;
            font-size: 9.5px; font-weight: 600;
            padding: 8px 8px; white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 10%;
        }
        table.payment-table td {
            padding: 8px 8px; border: 1px solid #f3f4f6;
            font-size: 10.5px; font-weight: 600; color: #374151;
            vertical-align: top; white-space: nowrap;
        }
        table.payment-table tr { page-break-inside: avoid; }
        table.payment-table tr:nth-child(even) td { background: #f7f8f8; }
        table.payment-table td.col-sl { width: 10%; }
        .text-right { text-align: right !important; }

        /* SUMMARY */
        table.summary {
            float: right; width: 256px;
            border: 2px solid #154734; border-collapse: collapse;
            font-size: 11px; margin-top: 20px;
        }
        table.summary th {
            background: #154734; color: #fff;
            font-size: 10px; font-weight: 700; letter-spacing: 0.15em;
            padding: 6px 12px; border: 1px solid #154734;
        }
        table.summary td { padding: 6px 12px; border: 1px solid #e5e7eb; }
        table.summary td.lbl { background: #f9fafb; color: #6b7280; font-weight: 600; }
        table.summary td.val { text-align: right; font-weight: 700; color: #111827; }
        .c-emerald { color: #059669 !important; }
        .c-red { color: #dc2626 !important; }
        .slip-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            max-width: 420px;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
            text-align: center;
        }
        .slip-watermark img { width: 100%; height: auto; object-fit: contain; }
        .slip-content-above { position: relative; z-index: 1; }
    </style>
</head>
<body>

    <div class="slip-watermark" aria-hidden="true">
        @if(!empty($school->logo) && file_exists(public_path('storage/' . $school->logo)))
            <img src="{{ public_path('storage/' . $school->logo) }}" alt="watermark">
        @else
            <img src="{{ public_path('images/logo.png') }}" alt="watermark">
        @endif
    </div>

    <div class="slip-content-above">

    <div class="header">
        <div class="logo-wrap">
            @if(!empty($school->logo) && file_exists(public_path('storage/' . $school->logo)))
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->school_name }}">
            @else
                <span class="no-logo">{{ mb_strtoupper(mb_substr($school->school_name ?? 'S', 0, 1)) }}</span>
            @endif
        </div>
        <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
        @if(!empty($school->village))
            <div class="address">{{ $school->village }}</div>
        @endif
        @if(!empty($school->mobile) || !empty($school->email))
            <div class="mobile">
                Mobile: {{ $school->mobile ?? '' }}@if(!empty($school->email)) | Email: {{ $school->email }}@endif
            </div>
        @endif
    </div>

    <div class="banner"><span>PAYMENT INVOICE</span></div>

    <table class="student-info">
        <tr>
            <td width="50%">
                <div class="row"><span class="lbl">Student Id</span><span class="colon">:</span><span class="val">{{ $student->student_id_number }}</span></div>
                <div class="row"><span class="lbl">Student Name</span><span class="colon">:</span><span class="val">{{ $student->student_name }}</span></div>
                <div class="row"><span class="lbl">Duration</span><span class="colon">:</span><span class="val">{{ $duration }}</span></div>
                <div class="row"><span class="lbl">Print Date</span><span class="colon">:</span><span class="val">{{ $printDate }}</span></div>
            </td>
            <td class="divider" width="50%">
                <div class="row"><span class="lbl">Class</span><span class="colon">:</span><span class="val">{{ $student->schoolClass->class_name ?? '—' }}</span></div>
                <div class="row"><span class="lbl">Group</span><span class="colon">:</span><span class="val">{{ $student->schoolGroup->group_name ?? '—' }}</span></div>
                <div class="row"><span class="lbl">Section</span><span class="colon">:</span><span class="val">{{ $student->schoolSection->section_name ?? '—' }}</span></div>
                <div class="row"><span class="lbl">Session</span><span class="colon">:</span><span class="val">{{ $student->schoolSession->session_year ?? '—' }}</span></div>
            </td>
        </tr>
    </table>

    <table class="payment-table">
        <thead>
            <tr>
                <th class="col-sl">SL</th>
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
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->pay_date ? \Carbon\Carbon::parse($p->pay_date)->format('d-M-y') : '-' }}</td>
                    <td style="color:#111827;">{{ $receiveMonth }}</td>
                    <td>{{ $p->pay_method ?? '-' }}</td>
                    <td style="color:{{ $statusColor }};">{{ $statusText }}</td>
                    <td>{{ $p->fees_type ?? '-' }}</td>
                    <td>{{ $p->fee_name ?? '-' }}</td>
                    <td class="text-right">{{ $showTotal ? number_format($total, 2) : '-' }}</td>
                    <td class="text-right">{{ number_format($paid, 2) }}</td>
                    <td class="text-right">{{ number_format($due, 2) }}</td>
                    <td class="text-right" style="color:{{ $isOverdue ? '#dc2626' : '#374151' }};">{{ $isOverdue ? number_format($due, 2) : '0.00' }}</td>
                </tr>
                
            @empty
                <tr>
                    <td colspan="11" style="padding:12px; color:#9ca3af; text-align:center;">No payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($payments))
        <table class="summary">
            <tr>
                <th colspan="2">SUMMARY</th>
            </tr>
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
        <div style="clear: both;"></div>
    @endif
</div>

</div>

</body>
</html>
