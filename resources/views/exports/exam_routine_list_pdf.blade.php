<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Exam Routines List - {{ $school->school_name }}</title>
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
            <td><strong>REPORT:</strong> EXAM ROUTINES LIST</td>
            <td class="text-right"><strong>PRINT DATE:</strong> {{ $date }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Class</th>
                <th>Group</th>
                <th>Section</th>
                <th>Session</th>
                <th>Exam</th>
                <th>Subject</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->exam_date }}</td>
                    <td>{{ $record->day_name }}</td>
                    <td>{{ $record->start_time }}</td>
                    <td>{{ $record->end_time }}</td>
                    <td>{{ $record->schoolClass?->class_name ?? '-' }}</td>
                    <td>{{ $record->schoolGroup?->group_name ?? '-' }}</td>
                    <td>{{ $record->schoolSection?->section_name ?? '-' }}</td>
                    <td>{{ $record->schoolSession?->session_year ?? '-' }}</td>
                    <td>{{ $record->schoolExam?->exam_name ?? '-' }}</td>
                    <td>{{ $record->schoolSubject?->subject_name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
