<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Academic Result - {{ $school->school_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 10px; }
        h1 { margin: 0; color: #1d4ed8; font-size: 18px; text-align: center; }
        h2 { margin: 3px 0 14px; font-size: 12px; text-align: center; font-weight: normal; }
        .meta { width: 100%; margin-bottom: 14px; }
        .meta td { width: 33.33%; padding: 3px 6px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eff6ff; font-weight: bold; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 5px; }
        .center { text-align: center; }
        .summary { margin-top: 14px; width: 100%; }
        .summary td { padding: 5px; border: 1px solid #e2e8f0; }
        .summary-card { height: 76px; text-align: center; vertical-align: middle; }
        .summary-label { display: block; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .summary-value { display: block; margin-top: 6px; font-size: 16px; font-weight: bold; }
        .summary-note { display: block; margin-top: 3px; color: #64748b; font-size: 8px; }
        .summary-qr { width: 62px; height: 62px; }
    </style>
</head>
<body>
    <h1>{{ $school->school_name }}</h1>
    <h2>Academic Result</h2>

    <table class="meta">
        <tr>
            <td><strong>Student:</strong> {{ $result['student_name'] ?? 'N/A' }}</td>
            <td><strong>Student ID:</strong> {{ $result['student_id_number'] ?? 'N/A' }}</td>
            <td><strong>Admit Card:</strong> {{ $result['admit_card_number'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Class:</strong> {{ $result['class_name'] ?? 'N/A' }}</td>
            <td><strong>Group:</strong> {{ $result['group_name'] ?? 'N/A' }}</td>
            <td><strong>Section:</strong> {{ $result['section_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Exam:</strong> {{ $result['exam_name'] ?? 'N/A' }}</td>
            <td><strong>Session:</strong> {{ $result['session_name'] ?? 'N/A' }}</td>
            <td><strong>Roll:</strong> {{ $result['roll_no'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th class="center">Full Mark</th>
                <th class="center">Highest Mark</th>
                <th class="center">Mark</th>
                <th class="center">Grade</th>
                <th class="center">Point</th>
            </tr>
        </thead>
        <tbody>
            @foreach (($result['subjects'] ?? []) as $subject)
                <tr>
                    <td>{{ $subject['name'] ?? 'N/A' }}</td>
                    <td class="center">{{ $subject['full_mark'] ?? 0 }}</td>
                    <td class="center">{{ $subject['highest_mark'] ?? 0 }}</td>
                    <td class="center">{{ $subject['mark'] ?? 0 }}</td>
                    <td class="center">{{ $subject['grade'] ?? '-' }}</td>
                    <td class="center">{{ $subject['point'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="summary-card">
                <span class="summary-label">GPA (Without 4th)</span>
                <span class="summary-value">{{ $result['gpa_without_fourth'] ?? $result['gpa'] ?? '0.00' }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Position</span>
                <span class="summary-value">{{ $result['position'] ?? 'N/A' }}</span>
                <span class="summary-note">Out of {{ $result['position_total'] ?? 'N/A' }} in Section</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Attendance</span>
                <span class="summary-value">{{ $result['attendance']['percentage'] ?? 0 }}%</span>
                <span class="summary-note">Present {{ $result['attendance']['present_days'] ?? 0 }} / Absent {{ $result['attendance']['absent_days'] ?? 0 }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Working Days</span>
                <span class="summary-value">{{ $result['attendance']['working_days'] ?? 0 }}</span>
                <span class="summary-note">Excluding holidays</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Verify Result</span>
                @if (! empty($result['qr_code']))
                    <img class="summary-qr" src="{{ $result['qr_code'] }}" alt="Result verification QR code">
                @else
                    <span class="summary-note">QR unavailable</span>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
