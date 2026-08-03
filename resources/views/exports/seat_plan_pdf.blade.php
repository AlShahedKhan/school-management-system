<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seat Plan - {{ $school->school_name }}</title>
    <style>
        @page { margin: 18px; }
        body { margin: 0; color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { border-bottom: 2px solid #2563eb; margin-bottom: 14px; padding-bottom: 8px; text-align: center; }
        .header h1 { color: #1d4ed8; font-size: 18px; margin: 0 0 4px; }
        .header p { color: #64748b; margin: 2px 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 6px; text-align: left; }
        th { background: #eff6ff; color: #1e3a8a; font-weight: bold; }
        .number { text-align: center; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school->school_name }}</h1>
        <p>{{ collect([$school->village, $school->upazila, $school->district, $school->division])->filter()->implode(', ') }}</p>
        <p>{{ $school->mobile }}{{ $school->mobile && $school->email ? ' | ' : '' }}{{ $school->email }}</p>
        <p class="muted">Seat Plan</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Class</th>
                <th>Group</th>
                <th>Section</th>
                <th>Session</th>
                <th>Exam Name</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Seat Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $item['class_name'] ?: '-' }}</td>
                    <td>{{ $item['group_name'] ?: '-' }}</td>
                    <td>{{ $item['section_name'] ?: '-' }}</td>
                    <td>{{ $item['session_name'] ?: '-' }}</td>
                    <td>{{ $item['exam_name'] ?: '-' }}</td>
                    <td>{{ $item['student_id_number'] ?: '-' }}</td>
                    <td>{{ $item['student_name'] ?: '-' }}</td>
                    <td class="number">{{ $item['seat_number'] ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
