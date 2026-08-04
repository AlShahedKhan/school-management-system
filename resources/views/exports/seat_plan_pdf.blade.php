<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seat Number - {{ $school->school_name }}</title>
    <style>
        @page { margin: 8mm; size: A4 portrait; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #fff; color: #10234a; font-family: DejaVu Sans, sans-serif; }
        .seat-page { display: table; width: 100%; table-layout: fixed; page-break-after: always; }
        .seat-page:last-child { page-break-after: auto; }
        .seat-row { display: table-row; page-break-inside: avoid; }
        .seat-cell { display: table-cell; width: 50%; height: 54mm; padding: 1mm 1.5mm; vertical-align: top; }
        .seat-card { position: relative; overflow: hidden; height: 50mm; border: .35mm solid #1f2937; padding: 3.2mm 3mm; font-size: 7.2px; }
        .watermark { position: absolute; inset: 12mm 31mm auto; width: 38mm; opacity: .055; }
        .card-content { position: relative; z-index: 1; }
        .school-name { margin: 0; color: #111827; text-align: center; font-size: 9px; font-weight: 700; }
        .school-meta { margin: .4mm 0 0; color: #334155; text-align: center; font-size: 6.5px; }
        .card-title { display: table; margin: 1.3mm auto 1.8mm; border: .3mm solid #111827; padding: .55mm 5mm; color: #111827; font-size: 7px; font-weight: 700; }
        .rule { border-top: .25mm solid #94a3b8; }
        .details { width: 100%; margin-top: 3.2mm; border-collapse: collapse; table-layout: fixed; }
        .details td { padding: .75mm 0; vertical-align: top; white-space: nowrap; }
        .details .label { width: 27%; color: #334155; font-weight: 700; }
        .details .colon { width: 5%; text-align: center; font-weight: 700; }
        .details .value { width: 18%; color: #0f172a; font-weight: 700; overflow: hidden; text-overflow: ellipsis; }
        .details .seat-value { color: #4338ca; }
        .preview { display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 16px; background: #f1f5f9; }
        .preview .seat-card { width: 96mm; height: 58mm; background: #fff; font-size: 8px; }
        .preview .school-name { font-size: 10px; }
        .preview .school-meta { font-size: 7px; }
        .preview .details { margin-top: 4mm; }
    </style>
</head>
<body>
@php($logo = $school->logo ? asset('storage/'.$school->logo) : null)
@if(!empty($preview))
    <div class="preview">
        @foreach($items as $item)
            @include('exports.partials.seat_plan_card', ['item' => $item, 'school' => $school, 'logo' => $logo])
        @endforeach
    </div>
@else
    @foreach(collect($items)->chunk(10) as $page)
        <div class="seat-page">
            @foreach($page->chunk(2) as $row)
                <div class="seat-row">
                    @foreach($row as $item)
                        <div class="seat-cell">
                            @include('exports.partials.seat_plan_card', ['item' => $item, 'school' => $school, 'logo' => $logo])
                        </div>
                    @endforeach
                    @if($row->count() < 2)<div class="seat-cell"></div>@endif
                </div>
            @endforeach
        </div>
    @endforeach
@endif
</body>
</html>
