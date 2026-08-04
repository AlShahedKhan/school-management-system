<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: Arial, sans-serif; color: #17345f; font-size: 7px; }
        h1 { margin: 0 0 4px; text-align: center; font-size: 13px; }
        .filters { margin-bottom: 10px; text-align: center; color: #475569; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #a9b9cc; padding: 2px 3px; vertical-align: top; overflow-wrap: anywhere; white-space: normal; }
        th:nth-child(1) { width: 3%; } th:nth-child(2) { width: 7%; } th:nth-child(3) { width: 7%; }
        th:nth-child(4) { width: 7%; } th:nth-child(5) { width: 7%; } th:nth-child(6) { width: 10%; }
        th:nth-child(7) { width: 9%; } th:nth-child(8) { width: 13%; } th:nth-child(9) { width: 7%; }
        th:nth-child(10) { width: 20%; } th:nth-child(11) { width: 10%; }
        th { background: #e2e8f0; font-weight: 700; text-align: center; }
        td.center { text-align: center; }
    </style>
</head>
<body>
    <h1>Fail List</h1>
    <div class="filters">
        {{ $filters['class'] }} / {{ $filters['group'] }} / {{ $filters['section'] }} /
        {{ $filters['session'] }} / {{ $filters['exam'] }}
    </div>
    <table>
        <thead>
            <tr>
                <th>SL</th><th>Class</th><th>Group</th><th>Section</th><th>Session</th><th>Exam</th>
                <th>Student ID</th><th>Student Name</th><th>Failed Subjects</th><th>Subject Name</th><th>Required to Pass</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td class="center">{{ $row['sl'] }}</td>
                    <td>{{ $row['class'] }}</td><td>{{ $row['group'] }}</td><td>{{ $row['section'] }}</td>
                    <td>{{ $row['session'] }}</td><td>{{ $row['exam'] }}</td><td>{{ $row['student_id'] }}</td>
                    <td>{{ $row['student_name'] }}</td><td class="center">{{ $row['failed_subjects'] }}</td>
                    <td>@foreach($row['subject_details'] as $subject){{ $subject['name'] }} ({{ $subject['mark'] }})@if(!$loop->last)<br>@endif @endforeach</td>
                    <td>@foreach($row['subject_details'] as $subject){{ $subject['required_to_pass'] }}@if(!$loop->last)<br>@endif @endforeach</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
