<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Report — {{ $student->student_name }}</title>
    <style>
        /*
         * ── Bangla Unicode Font Registration ──────────────────────────────
         * DomPDF resolves @font-face src with local file:// paths.
         * The TTF files were pre-processed by php-font-lib (UFM generated)
         * and registered in storage/fonts/installed-fonts.json via:
         *   php artisan dompdf:load-bangla-font
         */
        @font-face {
            font-family: 'NotoSansBengali';
            font-style: normal;
            font-weight: normal;
            src: url('{{ str_replace("\\", "/", storage_path("fonts/notosansbengali_normal.ttf")) }}');
        }
        @font-face {
            font-family: 'NotoSansBengali';
            font-style: normal;
            font-weight: bold;
            src: url('{{ str_replace("\\", "/", storage_path("fonts/notosansbengali_bold.ttf")) }}');
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        /*
         * Apply Bangla font globally with DejaVu Sans as Latin fallback.
         * DomPDF does not support font-family stacks the same way browsers do,
         * so we set NotoSansBengali as primary — it contains full Latin glyphs
         * too, so English text renders correctly alongside Bangla.
         */
        body {
            font-family: 'NotoSansBengali', 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
            padding: 18px 22px;
        }

        /* ═══════════════════════════════════════
           HEADER BANNER
        ═══════════════════════════════════════ */
        .header-banner {
            background: #1e3a5f;
            padding: 0;
            margin-bottom: 0;
        }

        /* Top accent stripe */
        .header-accent {
            background: #2563eb;
            height: 4px;
            width: 100%;
        }

        /* Two-column layout inside banner */
        .header-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .header-inner td {
            padding: 14px 18px;
            vertical-align: middle;
            border: none;
        }

        /* Left — Student details */
        .col-student {
            width: 50%;
            border-right: 1px solid rgba(255,255,255,0.15);
        }

        /* Right — School info */
        .col-school {
            width: 50%;
            text-align: right;
        }

        .header-doc-title {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #93c5fd;
            margin-bottom: 6px;
        }

        .student-name {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
            letter-spacing: 0.01em;
        }

        .student-id-badge {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.08em;
            padding: 2px 7px;
            margin-bottom: 8px;
        }

        .student-meta-row {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .student-meta-row td {
            padding: 1px 10px 1px 0;
            font-size: 8.5px;
            color: #cbd5e1;
            border: none;
            white-space: nowrap;
        }
        .student-meta-row .lbl {
            color: #94a3b8;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .student-meta-row .val {
            color: #e2e8f0;
            font-weight: 600;
        }

        /* School side */
        .school-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 3px;
        }
        .school-tagline {
            font-size: 7.5px;
            color: #93c5fd;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .school-contact {
            font-size: 8px;
            color: #cbd5e1;
            line-height: 1.7;
        }
        .school-contact .contact-icon {
            color: #60a5fa;
            font-weight: 700;
            margin-right: 3px;
        }

        /* Bottom bar of header */
        .header-footer-bar {
            background: #2563eb;
            width: 100%;
            border-collapse: collapse;
        }
        .header-footer-bar td {
            padding: 5px 18px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #bfdbfe;
            border: none;
        }
        .header-footer-bar .generated {
            text-align: right;
            color: #dbeafe;
        }

        /* ═══════════════════════════════════════
           SECTION LABEL
        ═══════════════════════════════════════ */
        .section-label {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #2563eb;
            margin: 14px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #dbeafe;
        }

        /* ═══════════════════════════════════════
           PAYMENTS TABLE
        ═══════════════════════════════════════ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table.data-table th {
            background: #1e3a5f;
            color: #bfdbfe;
            padding: 6px 7px;
            text-align: left;
            font-weight: 700;
            border: 1px solid #1e3a5f;
            text-transform: uppercase;
            font-size: 7.5px;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        table.data-table td {
            padding: 4px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }
        table.data-table tbody tr:nth-child(odd) td {
            background: #ffffff;
        }

        /* Status badges */
        .badge-paid    { color: #059669; font-weight: 700; text-transform: uppercase; font-size: 7.5px; }
        .badge-partial { color: #d97706; font-weight: 700; text-transform: uppercase; font-size: 7.5px; }
        .badge-unpaid  { color: #dc2626; font-weight: 700; text-transform: uppercase; font-size: 7.5px; }

        /* Totals footer */
        .tfoot-row td {
            background: #1e3a5f;
            color: #e2e8f0;
            font-weight: 700;
            border: 1px solid #1e3a5f;
            font-size: 9px;
            padding: 5px 7px;
        }

        /* Colour helpers */
        .text-green  { color: #059669; font-weight: 600; }
        .text-red    { color: #dc2626; font-weight: 600; }
        .text-muted  { color: #94a3b8; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* Empty state */
        .empty-row td {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-style: italic;
        }

        /* ═══════════════════════════════════════
           PAGE FOOTER
        ═══════════════════════════════════════ */
        .page-footer {
            margin-top: 14px;
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .page-footer td {
            font-size: 7.5px;
            color: #94a3b8;
            padding-top: 5px;
            border: none;
        }
        .page-footer .right { text-align: right; }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════════════
         HEADER BANNER
    ═══════════════════════════════════════ --}}
    <div class="header-banner">

        <div class="header-accent"></div>

        <table class="header-inner">
            <tr>
                {{-- LEFT: Student Details --}}
                <td class="col-student">
                    <div class="header-doc-title">&#9632; Payment Report</div>
                    <div class="student-name">{{ $student->student_name }}</div>
                    <div class="student-id-badge">ID: {{ $student->student_id_number }}</div>

                    <table class="student-meta-row">
                        <tr>
                            <td><span class="lbl">Class</span><br><span class="val">{{ $student->schoolClass->class_name ?? '—' }}</span></td>
                            <td><span class="lbl">Group</span><br><span class="val">{{ $student->schoolGroup->group_name ?? '—' }}</span></td>
                            <td><span class="lbl">Section</span><br><span class="val">{{ $student->schoolSection->section_name ?? '—' }}</span></td>
                            <td><span class="lbl">Session</span><br><span class="val">{{ $student->schoolSession->session_year ?? '—' }}</span></td>
                        </tr>
                    </table>
                </td>

                {{-- RIGHT: School Info --}}
                <td class="col-school">
                    <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
                    <div class="school-tagline">Official Payment Statement</div>
                    <div class="school-contact">
                        @if(!empty($school->village) || !empty($school->upazila) || !empty($school->district))
                            <div>
                                <span class="contact-icon">&#9679;</span>
                                {{ implode(', ', array_filter([$school->village, $school->upazila, $school->district, $school->division])) }}
                            </div>
                        @endif
                        @if(!empty($school->mobile))
                            <div><span class="contact-icon">&#9742;</span> {{ $school->mobile }}</div>
                        @endif
                        @if(!empty($school->email))
                            <div><span class="contact-icon">&#9993;</span> {{ $school->email }}</div>
                        @endif
                        @if(!empty($school->eiin_number))
                            <div><span class="contact-icon">&#9632;</span> EIIN: {{ $school->eiin_number }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Bottom bar --}}
        <table class="header-footer-bar">
            <tr>
                <td>Student Payment Summary</td>
                <td class="generated">Generated: {{ $generatedAt }}</td>
            </tr>
        </table>

    </div>

    {{-- ═══════════════════════════════════════
         PAYMENTS TABLE
    ═══════════════════════════════════════ --}}
    <div class="section-label">Payment Records</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:22px;" class="text-center">SL</th>
                <th style="width:66px;">Pay Date</th>
                <th style="width:66px;">Pay Method</th>
                <th style="width:50px;" class="text-center">Status</th>
                <th>Fee Type</th>
                <th>Fee Name</th>
                <th style="width:62px;" class="text-right">Total Fee</th>
                <th style="width:54px;" class="text-right">Paid</th>
                <th style="width:54px;" class="text-right">Due</th>
                <th style="width:46px;" class="text-center">Overdue</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Track which fee combos have already shown their total_payable
                $shownFees = [];
            @endphp
            @forelse ($payments as $i => $p)
                @php
                    $total     = (float) ($p->total_payable ?? 0);
                    $paid      = (float) ($p->type_amount   ?? 0);
                    $due       = (float) ($p->payable_due   ?? 0);
                    $isOverdue = $due > 0 && \Carbon\Carbon::parse($p->pay_date)->startOfDay()->lt(\Carbon\Carbon::today());
                    $feeKey    = $p->fees_type . '||' . $p->fee_name;
                    $showTotal = !in_array($feeKey, $shownFees);
                    if ($showTotal) $shownFees[] = $feeKey;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->pay_date)->format('j-F-Y') }}</td>
                    <td>{{ $p->pay_method ?? '—' }}</td>
                    <td class="text-center">
                        @if ($p->status === 'paid')
                            <span class="badge-paid">Paid</span>
                        @elseif ($p->status === 'partial')
                            <span class="badge-partial">Partial</span>
                        @else
                            <span class="badge-unpaid">Unpaid</span>
                        @endif
                    </td>
                    <td>{{ $p->fees_type ?? '—' }}</td>
                    <td>{{ $p->fee_name  ?? '—' }}</td>
                    <td class="text-right">{{ $showTotal ? number_format($total, 2) : '—' }}</td>
                    <td class="text-right text-green">{{ number_format($paid, 2) }}</td>
                    <td class="text-right {{ $due > 0 ? 'text-red' : 'text-green' }}">{{ number_format($due, 2) }}</td>
                    <td class="text-center">
                        @if ($isOverdue)
                            <span class="text-red">YES</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="10">No payment records found for this student.</td>
                </tr>
            @endforelse
        </tbody>

        @if ($payments->isNotEmpty())
        <tfoot>
            <tr class="tfoot-row">
                <td colspan="6" class="text-right">Grand Total</td>
                <td class="text-right">{{ number_format($grandTotal, 2) }}</td>
                <td class="text-right" style="color:#6ee7b7;">{{ number_format($grandPaid, 2) }}</td>
                <td class="text-right" style="color:{{ $grandDue > 0 ? '#fca5a5' : '#6ee7b7' }};">{{ number_format($grandDue, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- ═══════════════════════════════════════
         PAGE FOOTER
    ═══════════════════════════════════════ --}}
    <table class="page-footer">
        <tr>
            <td>{{ $school->school_name ?? '' }} &mdash; Confidential Payment Record</td>
            <td class="right">Page 1 &nbsp;|&nbsp; {{ $generatedAt }}</td>
        </tr>
    </table>

</body>
</html>
