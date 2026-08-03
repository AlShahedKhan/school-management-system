<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admit Cards - {{ $school['school_name'] ?? 'School' }}</title>
    <style>
        @page { size: A4 portrait; margin: 13mm 14mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }
        .page { width: 100%; page-break-inside: avoid; }
        .page-break { page-break-after: always; }
        .card {
            position: relative;
            min-height: 112mm;
            overflow: hidden;
            border: 1px solid #0f172a;
            padding: 7mm 6mm 5mm;
            background: #fff;
        }
        table { border-collapse: collapse; }
        .header-table { width: 100%; table-layout: fixed; }
        .header-side { width: 17%; vertical-align: middle; }
        .header-center { width: 66%; text-align: center; vertical-align: middle; }
        .school-name {
            margin: 0 0 3px;
            color: #111827;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.15;
            text-transform: uppercase;
        }
        .school-meta { margin: 2px 0; color: #334155; font-size: 8px; font-weight: 600; }
        .portrait,
        .portrait-placeholder {
            width: 58px;
            height: 58px;
            border: 1px solid #cbd5e1;
            border-radius: 50%;
        }
        .portrait { display: block; object-fit: cover; }
        .portrait-placeholder {
            display: block;
            color: #94a3b8;
            text-align: center;
            border-style: dashed;
            font-size: 7px;
            line-height: 58px;
        }
        .student-photo { margin-left: auto; }
        .admit-badge {
            display: inline-block;
            margin-top: 5px;
            border: 1px solid #0f172a;
            padding: 2px 14px;
            color: #0f172a;
            font-size: 9px;
            font-weight: 700;
            line-height: 1;
            text-transform: uppercase;
        }
        .divider { margin: 6px 0 7px; border-top: 1px solid #64748b; }
        .watermark {
            position: absolute;
            top: 33mm;
            left: 50%;
            width: 48mm;
            height: 48mm;
            margin-left: -24mm;
            opacity: .07;
            object-fit: contain;
        }
        .details-table {
            position: relative;
            z-index: 1;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 7px;
        }
        .details-column { width: 50%; vertical-align: top; }
        .details-column:first-child { padding-right: 10px; }
        .details-column:last-child { padding-left: 10px; }
        .detail-table { width: 100%; table-layout: fixed; }
        .detail-table td { padding: 2px 0; vertical-align: top; line-height: 1.25; }
        .detail-label { width: 88px; color: #334155; font-weight: 700; }
        .detail-colon { width: 9px; text-align: center; }
        .detail-value { overflow-wrap: break-word; color: #0f172a; }
        .detail-value.strong { font-weight: 700; }
        .detail-value.exam { color: #1e3a8a; font-weight: 700; }
        .routine { position: relative; z-index: 1; width: 100%; table-layout: fixed; }
        .routine th,
        .routine td {
            height: 21px;
            border: 1px solid #0f172a;
            padding: 3px 5px;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
        }
        .routine th { background: #f1f5f9; font-size: 8px; font-weight: 700; }
        .routine td { font-size: 8px; }
        .routine .subject { text-align: left; }
        .routine .empty { color: #64748b; text-align: center; }
        .signature-table {
            position: relative;
            z-index: 1;
            width: 100%;
            table-layout: fixed;
            margin-top: 12px;
        }
        .signature-cell { width: 50%; vertical-align: bottom; }
        .signature-cell:last-child { text-align: right; }
        .signature-block { display: inline-block; width: 105px; text-align: center; }
        .signature-image { display: block; max-width: 82px; max-height: 27px; margin: 0 auto 2px; }
        .issue-date { height: 29px; padding-top: 17px; font-size: 7px; font-weight: 600; }
        .signature-space { height: 29px; }
        .signature-label { border-top: 1px solid #0f172a; padding-top: 2px; color: #475569; font-size: 7px; font-weight: 700; }
    </style>
</head>
<body>
@foreach ($cards as $card)
    @php
        $normalize = static fn ($value) => strtolower(trim((string) ($value ?? '')));
        $baseRoutines = collect($routines)->filter(function ($routine) use ($card, $normalize) {
            return $normalize($routine['class_name'] ?? null) === $normalize($card['class_name'] ?? null)
                && $normalize($routine['session_name'] ?? null) === $normalize($card['session_name'] ?? null)
                && $normalize($routine['exam_name'] ?? null) === $normalize($card['exam_name'] ?? null);
        });
        $exactRoutines = $baseRoutines->filter(function ($routine) use ($card, $normalize) {
            return $normalize($routine['group_name'] ?? null) === $normalize($card['group_name'] ?? null)
                && $normalize($routine['section_name'] ?? null) === $normalize($card['section_name'] ?? null);
        });
        $cardRoutines = $exactRoutines->isNotEmpty() ? $exactRoutines : $baseRoutines;
        $address = $school['full_address'] ?? collect([
            $school['village'] ?? null,
            $school['upazila'] ?? null,
            $school['district'] ?? null,
            $school['division'] ?? null,
        ])->filter()->implode(', ');
    @endphp

    <section class="page">
        <div class="card">
            @if (! empty($school['logo_data_uri']))
                <img class="watermark" src="{{ $school['logo_data_uri'] }}" alt="">
            @endif

            <table class="header-table">
                <tr>
                    <td class="header-side">
                        @if (! empty($school['logo_data_uri']))
                            <img class="portrait" src="{{ $school['logo_data_uri'] }}" alt="School logo">
                        @else
                            <span class="portrait-placeholder">Logo</span>
                        @endif
                    </td>
                    <td class="header-center">
                        <h1 class="school-name">{{ $school['school_name'] ?? 'School Name' }}</h1>
                        <p class="school-meta">
                            {{ $school['mobile'] ?? '' }}
                            {{ ! empty($school['mobile']) && ! empty($school['email']) ? ' | ' : '' }}
                            {{ $school['email'] ?? '' }}
                        </p>
                        <p class="school-meta">{{ $address ?: '-' }}</p>
                        <span class="admit-badge">Admit Card</span>
                    </td>
                    <td class="header-side">
                        @if (! empty($card['student_image_data_uri']))
                            <img class="portrait student-photo" src="{{ $card['student_image_data_uri'] }}" alt="Student photo">
                        @else
                            <span class="portrait-placeholder student-photo">Photo</span>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <table class="details-table">
                <tr>
                    <td class="details-column">
                        <table class="detail-table">
                            <tr><td class="detail-label">Student Name</td><td class="detail-colon">:</td><td class="detail-value strong">{{ $card['student_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Student ID</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['student_id_number'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Seat No</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['seat_number'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Father's Name</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['father_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Admit Card No</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['admit_card_number'] ?: '-' }}</td></tr>
                        </table>
                    </td>
                    <td class="details-column">
                        <table class="detail-table">
                            <tr><td class="detail-label">Class</td><td class="detail-colon">:</td><td class="detail-value strong">{{ $card['class_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Group</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['group_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Section</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['section_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Session</td><td class="detail-colon">:</td><td class="detail-value">{{ $card['session_name'] ?: '-' }}</td></tr>
                            <tr><td class="detail-label">Exam Name</td><td class="detail-colon">:</td><td class="detail-value exam">{{ $card['exam_name'] ?: '-' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="routine">
                <colgroup>
                    <col style="width:18%">
                    <col style="width:27%">
                    <col style="width:20%">
                    <col style="width:35%">
                </colgroup>
                <thead>
                    <tr><th>Date</th><th>Time</th><th>Duration</th><th>Subject</th></tr>
                </thead>
                <tbody>
                    @forelse ($cardRoutines as $routine)
                        <tr>
                            <td>{{ $routine['exam_date'] ?? '-' }}</td>
                            <td>{{ $routine['start_time'] ?? '-' }}{{ ! empty($routine['end_time']) ? ' - '.$routine['end_time'] : '' }}</td>
                            <td>{{ $routine['total_hours'] ?? '-' }}</td>
                            <td class="subject">{{ $routine['subject_name'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td>-</td><td>-</td><td>-</td><td class="subject">-</td></tr>
                    @endforelse
                </tbody>
            </table>

            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-block">
                            <div class="issue-date">{{ now()->format('d-M-Y') }}</div>
                            <div class="signature-label">Issue Date</div>
                        </div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-block">
                            <div class="signature-space">
                                @if (! empty($school['principal_signature_data_uri']))
                                    <img class="signature-image" src="{{ $school['principal_signature_data_uri'] }}" alt="Principal signature">
                                @endif
                            </div>
                            <div class="signature-label">Principal</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
    @unless ($loop->last)<div class="page-break"></div>@endunless
@endforeach
</body>
</html>
