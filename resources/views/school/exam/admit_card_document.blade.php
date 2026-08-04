<!doctype html>
<html lang="{{ $language ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($language ?? 'en') === 'bn' ? 'প্রবেশপত্র' : 'Admit Card' }} - {{ $school['school_name'] ?? 'School' }}</title>
    <style>
        @font-face { font-family: NotoBengali; src: url('/fonts/NotoSansBengali-VariableFont_wdth,wght.ttf') format('truetype'); font-weight: 100 900; }
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; color: #111; background: #eef1ef; }
        body { font-family: Arial, Helvetica, sans-serif; }
        body.bn { font-family: NotoBengali, Arial, sans-serif; }
        .sheet { width: 194mm; min-height: 281mm; margin: 0 auto 8mm; padding: 3mm 0; background: #fff; page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .card-wrap { height: 136mm; padding: 2mm; break-inside: avoid; page-break-inside: avoid; }
        .card {
            position: relative; height: 132mm; overflow: hidden; padding: 4.5mm 5mm 3.5mm;
            border: 1mm solid #315b43;
            background: #f8f8f5;
            box-shadow: inset 0 0 0 .3mm #789279, inset 0 0 0 1.5mm #f8f8f5;
        }
        .watermark { position: absolute; z-index: 0; left: 50%; top: 51%; width: 52mm; height: 52mm; transform: translate(-50%, -50%); opacity: .065; object-fit: contain; }
        .content { position: relative; z-index: 1; height: 100%; display: flex; flex-direction: column; }
        .header { display: grid; grid-template-columns: 21mm 1fr 21mm; align-items: center; min-height: 24mm; padding: 0 1mm 2mm; border-bottom: .35mm solid #555; }
        .round-image, .round-placeholder { width: 18mm; height: 18mm; border-radius: 50%; border: .35mm solid #777; object-fit: cover; display: block; }
        .round-placeholder { display: flex; align-items: center; justify-content: center; color: #777; font-size: 7px; border-style: dashed; background: #eee; }
        .student-photo { margin-left: auto; }
        .school { text-align: center; line-height: 1.12; }
        .school h1 { margin: 0; font-size: 17px; font-weight: 800; text-transform: uppercase; }
        .school p { margin: 1px 0 0; font-size: 8.4px; }
        .badge { display: inline-block; margin-top: 2px; padding: 1px 11px 2px; color: #fff; background: #17233f; border: .3mm solid #555; font-size: 9px; font-weight: 700; text-transform: uppercase; line-height: 1.1; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 8mm; padding: 5.5mm 0 3mm; font-size: 9.2px; line-height: 1.45; }
        .detail-row { display: grid; grid-template-columns: 29mm 3mm 1fr; min-height: 4mm; }
        .detail-row strong { font-weight: 700; }
        .routine { width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 8.2px; text-align: center; }
        .routine th, .routine td { height: 5.4mm; border: .3mm solid #333; padding: .6mm 1mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .routine th { height: 5.7mm; color: #fff; background: #153e2c; font-weight: 700; }
        .routine col.date { width: 15%; } .routine col.subject { width: 18.333%; }
        .footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto; padding: 0 5mm; font-size: 7.8px; }
        .footer-instructions { flex: 1; max-width: 126mm; margin-left: -5mm; padding-right: 5mm; }
        .footer-instructions .instructions-title { margin: 0 0 .5mm; text-align: center; font-size: 10px; font-weight: 800; }
        .footer-instructions ol { margin: 0; padding-left: 0; font-size: 7.4px; line-height: 1.25; font-weight: 600; list-style: none; }
        .signature-block { width: 34mm; text-align: center; }
        .signature-image { display: block; width: 25mm; height: 8mm; margin: 0 auto .5mm; object-fit: contain; }
        .signature-space { height: 8.5mm; }
        .signature-line { padding-top: .7mm; border-top: .3mm solid #333; }
        @media screen {
            .sheet { box-shadow: 0 8px 24px rgba(15, 45, 28, .14); }
            .screen-toolbar { position: sticky; z-index: 20; top: 0; display: flex; justify-content: center; gap: 8px; padding: 10px; background: #fff; border-bottom: 1px solid #d6ddd8; }
            .screen-toolbar button, .screen-toolbar a { border: 1px solid #315b43; padding: 7px 14px; color: #244733; background: #fff; font: 700 11px Arial, sans-serif; text-decoration: none; cursor: pointer; }
            .screen-toolbar .primary { color: #fff; background: #315b43; }
        }
        @media print { body { background: #fff; } .screen-toolbar { display: none !important; } .sheet { margin-bottom: 0; box-shadow: none; } }
    </style>
</head>
@php
    $lang = ($language ?? 'en') === 'bn' ? 'bn' : 'en';
    $labels = $lang === 'bn' ? [
        'title' => 'প্রবেশপত্র', 'student_name' => 'শিক্ষার্থীর নাম', 'student_id' => 'শিক্ষার্থী আইডি',
        'father_name' => 'পিতার নাম', 'admit_no' => 'প্রবেশপত্র নং', 'class' => 'শ্রেণি', 'group' => 'বিভাগ',
        'section' => 'শাখা', 'session' => 'শিক্ষাবর্ষ', 'exam' => 'পরীক্ষার নাম', 'date' => 'তারিখ',
        'subject' => 'বিষয়', 'rules' => 'পরীক্ষার নিয়মাবলী', 'issue' => 'প্রদানের তারিখ', 'principal' => 'প্রধান শিক্ষক',
        'logo' => 'লোগো', 'photo' => 'ছবি',
    ] : [
        'title' => 'Admit Card', 'student_name' => 'Student Name', 'student_id' => 'Student ID',
        'father_name' => "Father's Name", 'admit_no' => 'Admit Card No', 'class' => 'Class', 'group' => 'Group',
        'section' => 'Section', 'session' => 'Session', 'exam' => 'Exam Name', 'date' => 'Date',
        'subject' => 'Subject', 'rules' => 'Examination Instructions', 'issue' => 'Issue Date', 'principal' => 'Principal',
        'logo' => 'Logo', 'photo' => 'Photo',
    ];
    $digits = fn ($value) => $lang === 'bn' ? strtr((string) $value, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']) : (string) $value;
    $issueDate = $digits(now()->format('d-m-Y'));
    $normal = fn ($value) => strtolower(trim((string) ($value ?? '')));
@endphp
<body class="{{ $lang }}" data-admit-card-ready="true">
    @if(!empty($showToolbar))
        <div class="screen-toolbar">
            @if(!empty($downloadUrl))<a class="primary" href="{{ $downloadUrl }}">{{ $lang === 'bn' ? 'পিডিএফ ডাউনলোড' : 'Download PDF' }}</a>@endif
            <button type="button" onclick="window.print()">{{ $lang === 'bn' ? 'প্রিন্ট' : 'Print' }}</button>
        </div>
    @endif

    @foreach(collect($cards)->chunk(2) as $pageCards)
        <main class="sheet">
            @foreach($pageCards as $card)
                @php
                    $matching = collect($routines ?? [])->filter(function ($routine) use ($card, $normal) {
                        if ($normal(data_get($routine, 'class_name')) !== $normal(data_get($card, 'class_name'))
                            || $normal(data_get($routine, 'session_name')) !== $normal(data_get($card, 'session_name'))
                            || $normal(data_get($routine, 'exam_name')) !== $normal(data_get($card, 'exam_name'))) return false;
                        $routineGroup = $normal(data_get($routine, 'group_name'));
                        $routineSection = $normal(data_get($routine, 'section_name'));
                        return ($routineGroup === '' || $routineGroup === $normal(data_get($card, 'group_name')))
                            && ($routineSection === '' || $routineSection === $normal(data_get($card, 'section_name')));
                    })->sortBy(fn ($routine) => data_get($routine, 'exam_date').' '.data_get($routine, 'start_time'))->values();
                    $routineRowCount = (int) ceil($matching->count() / 3);
                    $routineSpacerHeight = (6 - $routineRowCount) * 5.4;
                @endphp
                <section class="card-wrap">
                    <article class="card">
                        @if(!empty($school['logo_data_uri'] ?? $school['logo'] ?? null))
                            <img class="watermark" src="{{ $school['logo_data_uri'] ?? $school['logo'] }}" alt="">
                        @endif
                        <div class="content">
                            <header class="header">
                                <div>
                                    @if(!empty($school['logo_data_uri'] ?? $school['logo'] ?? null))
                                        <img class="round-image" src="{{ $school['logo_data_uri'] ?? $school['logo'] }}" alt="{{ $labels['logo'] }}">
                                    @else <div class="round-placeholder">{{ $labels['logo'] }}</div> @endif
                                </div>
                                <div class="school">
                                    <h1>{{ $school['school_name'] ?? 'School Name' }}</h1>
                                    <p>{{ collect([$school['mobile'] ?? null, $school['email'] ?? null])->filter()->implode(' | ') }}</p>
                                    <p>{{ $school['full_address'] ?? '' }}</p>
                                    <span class="badge">{{ $labels['title'] }}</span>
                                </div>
                                <div>
                                    @if(!empty(data_get($card, 'student_image_data_uri')))
                                        <img class="round-image student-photo" src="{{ data_get($card, 'student_image_data_uri') }}" alt="{{ $labels['photo'] }}">
                                    @else <div class="round-placeholder student-photo">{{ $labels['photo'] }}</div> @endif
                                </div>
                            </header>

                            <div class="details">
                                <div>
                                    <div class="detail-row"><span>{{ $labels['student_name'] }}</span><span>:</span><strong>{{ data_get($card, 'student_name', '-') ?: '-' }}</strong></div>
                                    <div class="detail-row"><span>{{ $labels['student_id'] }}</span><span>:</span><strong>{{ $digits(data_get($card, 'student_id_number', '-')) }}</strong></div>
                                    <div class="detail-row"><span>{{ $labels['admit_no'] }}</span><span>:</span><span>{{ $digits(data_get($card, 'admit_card_number', '-')) }}</span></div>
                                    <div class="detail-row"><span>{{ $labels['exam'] }}</span><span>:</span><strong>{{ data_get($card, 'exam_name', '-') ?: '-' }}</strong></div>
                                </div>
                                <div>
                                    <div class="detail-row"><span>{{ $labels['class'] }}</span><span>:</span><strong>{{ data_get($card, 'class_name', '-') ?: '-' }}</strong></div>
                                    <div class="detail-row"><span>{{ $labels['group'] }}</span><span>:</span><span>{{ data_get($card, 'group_name', '-') ?: '-' }}</span></div>
                                    <div class="detail-row"><span>{{ $labels['section'] }}</span><span>:</span><span>{{ data_get($card, 'section_name', '-') ?: '-' }}</span></div>
                                    <div class="detail-row"><span>{{ $labels['session'] }}</span><span>:</span><span>{{ $digits(data_get($card, 'session_name', '-')) }}</span></div>
                                </div>
                            </div>

                            <table class="routine">
                                <colgroup>@for($i=0;$i<3;$i++)<col class="date"><col class="subject">@endfor</colgroup>
                                <thead><tr>@for($i=0;$i<3;$i++)<th>{{ $labels['date'] }}</th><th>{{ $labels['subject'] }}</th>@endfor</tr></thead>
                                <tbody>
                                    @for($row = 0; $row < $routineRowCount; $row++)
                                        <tr>
                                            @for($group = 0; $group < 3; $group++)
                                                @php $routine = $matching->get(($row * 3) + $group); @endphp
                                                <td>{{ $routine ? $digits(\Illuminate\Support\Carbon::parse(data_get($routine, 'exam_date'))->format('d-M-y')) : '' }}</td>
                                                <td>{{ $routine ? data_get($routine, 'subject_name') : '' }}</td>
                                            @endfor
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <div aria-hidden="true" style="height: {{ $routineSpacerHeight }}mm;"></div>

                            <footer class="footer">
                                <div class="footer-instructions">
                                    <h2 class="instructions-title">{{ $labels['rules'] }}</h2>
                                    <ol>@foreach($instructions ?? [] as $instruction)<li>{{ $instruction }}</li>@endforeach</ol>
                                </div>
                                <div class="signature-block">
                                    @if(!empty($school['principal_signature_data_uri'] ?? $school['principal_signature'] ?? null))
                                        <img class="signature-image" src="{{ $school['principal_signature_data_uri'] ?? $school['principal_signature'] }}" alt="">
                                    @else <div class="signature-space"></div> @endif
                                    <div class="signature-line">{{ $labels['principal'] }}</div>
                                </div>
                            </footer>
                        </div>
                    </article>
                </section>
            @endforeach
        </main>
    @endforeach
</body>
</html>
