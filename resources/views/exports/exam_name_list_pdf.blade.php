<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Exam Names List - {{ $school->school_name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            color: #2563eb;
        }

        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }

        .report-info {
            margin-bottom: 20px;
            width: 100%;
        }

        .report-info td {
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            text-align: left;
            padding: 10px 5px;
            border: 1px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }

        .signature {
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            float: right;
            padding-top: 5px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>{{ $school->school_name }}</h2>
        <p>{{ $school->address ?? 'School Management System' }}</p>
        <p>Mobile: {{ $school->mobile ?? '' }} | Email: {{ $school->email ?? '' }}</p>
    </div>

    <table class="report-info">
        <tr>
            <td><strong>REPORT:</strong> EXAM NAMES LIST</td>
            <td class="text-right"><strong>PRINT DATE:</strong> {{ $date }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>Class</th>
                <th>Group</th>
                <th>Section</th>
                <th>Session</th>
                <th>Exam Name</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->class_name }}</td>
                    <td>{{ $record->group_name }}</td>
                    <td>{{ $record->section_name }}</td>
                    <td>{{ $record->session_name }}</td>
                    <td>{{ $record->exam_name }}</td>
                    <td>{{ $record->exam_start_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $record->exam_end_date?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
