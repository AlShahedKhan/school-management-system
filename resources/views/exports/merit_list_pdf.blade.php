<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Merit List - {{ $school->school_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 10px; }
        h1 { margin: 0 0 4px; color: #1d4ed8; font-size: 18px; text-align: center; }
        .school { margin-bottom: 16px; text-align: center; font-size: 11px; }
        .filters { width: 100%; margin-bottom: 14px; }
        .filters td { padding: 3px 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eff6ff; font-weight: bold; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 5px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h1>Merit List</h1>
    <div class="school">{{ $school->school_name }}</div>

    <table class="filters">
        <tr>
            <td><strong>Class:</strong> {{ $filters['class'] }}</td>
            <td><strong>Group:</strong> {{ $filters['group'] }}</td>
            <td><strong>Section:</strong> {{ $filters['section'] }}</td>
        </tr>
        <tr>
            <td><strong>Session:</strong> {{ $filters['session'] }}</td>
            <td><strong>Exam:</strong> {{ $filters['exam'] }}</td>
            <td><strong>Order:</strong> {{ $filters['sort_order'] === 'last' ? 'Last first' : 'First first' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="center">SL</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th class="center">Total Marks</th>
                <th class="center">Obtained Marks</th>
                <th class="center">Merit Serial</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td class="center">{{ $row['sl'] }}</td>
                    <td>{{ $row['student_id'] }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    <td class="center">{{ $row['total_marks'] }}</td>
                    <td class="center">{{ $row['obtained_marks'] }}</td>
                    <td class="center">{{ $row['merit_serial'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
