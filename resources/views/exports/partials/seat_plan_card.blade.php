<div class="seat-card">
    @if($logo)<img class="watermark" src="{{ $logo }}" alt="">@endif
    <div class="card-content">
        <h1 class="school-name">{{ $school->school_name }}</h1>
        <p class="school-meta">{{ $school->mobile }}{{ $school->mobile && $school->email ? ' | ' : '' }}{{ $school->email }}</p>
        <p class="school-meta">{{ collect([$school->upazila, $school->district, $school->division])->filter()->implode(', ') }}</p>
        <div class="card-title">SEAT NUMBER</div>
        <div class="rule"></div>
        <table class="details">
            <tr><td class="label">ID Number</td><td class="colon">:</td><td class="value">{{ $item['student_id_number'] ?: '-' }}</td><td class="label">Class</td><td class="colon">:</td><td class="value">{{ $item['class_name'] ?: '-' }}</td></tr>
            <tr><td class="label">Student Name</td><td class="colon">:</td><td class="value">{{ $item['student_name'] ?: '-' }}</td><td class="label">Group</td><td class="colon">:</td><td class="value">{{ $item['group_name'] ?: '-' }}</td></tr>
            <tr><td class="label">Exam Name</td><td class="colon">:</td><td class="value">{{ $item['exam_name'] ?: '-' }}</td><td class="label">Section</td><td class="colon">:</td><td class="value">{{ $item['section_name'] ?: '-' }}</td></tr>
            <tr><td class="label">Seat Number</td><td class="colon">:</td><td class="value seat-value">{{ $item['seat_number'] ?: '-' }}</td><td class="label">Session</td><td class="colon">:</td><td class="value">{{ $item['session_name'] ?: '-' }}</td></tr>
        </table>
    </div>
</div>
