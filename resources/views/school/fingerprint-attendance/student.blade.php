@extends('layouts.school')

@section('content')

<style>
    .fp-page{
        --primary:#2563eb;
        --primary-soft:#eff6ff;
        --text:#0f172a;
        --muted:#64748b;
        --border:#e2e8f0;
        --bg:#f8fafc;
        --green:#16a34a;
        --red:#dc2626;
        --orange:#ea580c;
        --purple:#7c3aed;
        padding:24px;
        background:#f8fafc;
        min-height:100vh;
        font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    }
    .fp-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:20px}
    .fp-kicker{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--primary);margin-bottom:6px}
    .fp-title{margin:0;font-size:27px;font-weight:800;color:var(--text)}
    .fp-subtitle{margin:6px 0 0;font-size:12px;color:var(--muted)}
    .fp-device-chip{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid #bbf7d0;background:#f0fdf4;color:#15803d;border-radius:12px;font-size:11px;font-weight:800;white-space:nowrap}
    .fp-device-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.12)}
    .fp-summary{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:18px}
    .fp-stat{background:#fff;border:1px solid var(--border);border-radius:15px;padding:15px;display:flex;align-items:center;gap:11px;box-shadow:0 6px 20px rgba(15,23,42,.035)}
    .fp-stat-icon{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:15px;flex:0 0 40px}
    .fp-stat-icon.green{background:#ecfdf5;color:var(--green)}
    .fp-stat-icon.red{background:#fef2f2;color:var(--red)}
    .fp-stat-icon.orange{background:#fff7ed;color:var(--orange)}
    .fp-stat-icon.blue{background:#eff6ff;color:var(--primary)}
    .fp-stat-icon.purple{background:#faf5ff;color:var(--purple)}
    .fp-stat h3{margin:0;font-size:19px;font-weight:800;color:var(--text)}
    .fp-stat p{margin:4px 0 0;font-size:10px;font-weight:700;color:var(--muted)}
    .fp-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 7px 22px rgba(15,23,42,.035);overflow:hidden;margin-bottom:18px}
    .fp-card-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 18px;border-bottom:1px solid var(--border)}
    .fp-card-head h4{margin:0;font-size:14px;font-weight:800;color:var(--text)}
    .fp-card-head p{margin:3px 0 0;font-size:10px;color:var(--muted)}
    .fp-card-body{padding:18px}
    .fp-filter-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:12px;align-items:end}
    .col-6{grid-column:span 6}.col-4{grid-column:span 4}.col-3{grid-column:span 3}.col-2{grid-column:span 2}.col-1{grid-column:span 1}.col-12{grid-column:span 12}
    .fp-label{display:block;margin-bottom:6px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:#475569}
    .fp-input,.fp-select{width:100%;height:40px;border:1px solid #dbe3ed;border-radius:9px;background:#fff;padding:0 11px;font-size:11px;color:#334155;outline:none}
    .fp-input:focus,.fp-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
    .fp-btn{width:100%;height:40px;border:0;border-radius:9px;background:var(--primary);color:#fff;font-size:11px;font-weight:800;cursor:pointer}
    .fp-btn:hover{background:#1d4ed8}
    .fp-search{position:relative;width:235px}
    .fp-search i{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:11px;color:#94a3b8}
    .fp-search input{width:100%;height:36px;border:1px solid var(--border);border-radius:9px;padding:0 11px 0 32px;font-size:10px;outline:none}
    .fp-search input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
    .fp-table-wrap{overflow-x:auto}
    .fp-table{width:100%;border-collapse:collapse;min-width:950px}
    .fp-table th{padding:12px 13px;background:#f8fafc;border-bottom:1px solid var(--border);color:#64748b;font-size:8.5px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;text-align:left;white-space:nowrap}
    .fp-table td{padding:13px;border-bottom:1px solid #f1f5f9;color:#334155;font-size:10.5px;vertical-align:middle;white-space:nowrap}
    .fp-person{display:flex;align-items:center;gap:9px}
    .fp-avatar{width:34px;height:34px;border-radius:10px;background:#eff6ff;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800}
    .fp-person b{display:block;font-size:10.5px;color:var(--text)}
    .fp-person small{display:block;margin-top:2px;color:#94a3b8;font-size:8.5px}
    .fp-status{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border-radius:999px;font-size:8.5px;font-weight:800}
    .fp-status::before{content:"";width:5px;height:5px;border-radius:50%;background:currentColor}
    .present{background:#ecfdf5;color:#15803d}
    .absent{background:#fef2f2;color:#dc2626}
    .late{background:#fff7ed;color:#c2410c}
    .leave{background:#eff6ff;color:#1d4ed8}
    .holiday{background:#faf5ff;color:#7e22ce}
    .late-min{font-weight:800;color:#c2410c}
    .fp-note{display:flex;align-items:center;gap:7px;padding:11px 14px;background:#f8fafc;border-top:1px solid var(--border);font-size:9.5px;color:#64748b}
    @media(max-width:1100px){.fp-summary{grid-template-columns:repeat(3,1fr)}}
    @media(max-width:800px){
        .fp-page{padding:16px}
        .fp-header{flex-direction:column}
        .fp-summary{grid-template-columns:repeat(2,1fr)}
        .fp-card-head{align-items:flex-start;flex-direction:column}
        .fp-search{width:100%}
        .col-6,.col-4,.col-3,.col-2,.col-1{grid-column:span 12}
    }
    @media(max-width:480px){.fp-summary{grid-template-columns:1fr}}
</style>

<div class="fp-page">
    <div class="fp-header">
        <div>
            <div class="fp-kicker">Fingerprint Attendance</div>
            <h1 class="fp-title">Student Attendance</h1>
            <p class="fp-subtitle">Class-wise student fingerprint attendance, in/out time and attendance status.</p>
        </div>
        <div class="fp-device-chip"><span class="fp-device-dot"></span><i class="fas fa-microchip"></i> Device Online</div>
    </div>

    
<div class="fp-summary">
    <div class="fp-stat"><div class="fp-stat-icon green"><i class="fas fa-user-check"></i></div><div><h3>22</h3><p>Total Present</p></div></div>
    <div class="fp-stat"><div class="fp-stat-icon red"><i class="fas fa-user-times"></i></div><div><h3>3</h3><p>Total Absent</p></div></div>
    <div class="fp-stat"><div class="fp-stat-icon orange"><i class="fas fa-clock"></i></div><div><h3>4</h3><p>Total Late</p></div></div>
    <div class="fp-stat"><div class="fp-stat-icon blue"><i class="fas fa-calendar-check"></i></div><div><h3>1</h3><p>Total Leave</p></div></div>
    <div class="fp-stat"><div class="fp-stat-icon purple"><i class="fas fa-umbrella-beach"></i></div><div><h3>2</h3><p>Total Holiday</p></div></div>
</div>


    <div class="fp-card">
        <div class="fp-card-head">
            <div><h4><i class="fas fa-filter" style="color:#2563eb;margin-right:7px"></i>Attendance Filter</h4><p>Filter by Class, Group, Section, Session, Month and Year.</p></div>
        </div>
        <div class="fp-card-body">
            <div class="fp-filter-grid">
                <div class="col-2"><label class="fp-label">Class</label><select class="fp-select"><option>Class 8</option><option>Class 9</option><option>Class 10</option></select></div>
                <div class="col-2"><label class="fp-label">Group</label><select class="fp-select"><option>Science</option><option>Business Studies</option><option>Humanities</option></select></div>
                <div class="col-2"><label class="fp-label">Section</label><select class="fp-select"><option>A</option><option>B</option><option>C</option></select></div>
                <div class="col-2"><label class="fp-label">Session</label><select class="fp-select"><option>2026</option><option>2025</option></select></div>
                <div class="col-2"><label class="fp-label">Month</label><select class="fp-select"><option>August</option><option>July</option><option>June</option></select></div>
                <div class="col-1"><label class="fp-label">Year</label><select class="fp-select"><option>2026</option><option>2025</option></select></div>
                <div class="col-1"><button type="button" id="fpFilterButton" class="fp-btn"><i class="fas fa-search"></i></button></div>
            </div>
        </div>
    </div>

    <div class="fp-card">
        <div class="fp-card-head">
            <div><h4><i class="fas fa-fingerprint" style="color:#2563eb;margin-right:7px"></i>Student Attendance Report</h4><p>Class-wise fingerprint attendance report.</p></div>
            <div class="fp-search"><i class="fas fa-search"></i><input id="fpAttendanceSearch" type="text" placeholder="Search student or status..."></div>
        </div>
        <div class="fp-table-wrap">
            <table class="fp-table" id="fpAttendanceTable">
                <thead>
                    <tr><th>SL</th><th>Date</th><th>Class</th><th>Group</th><th>Section</th><th>Session</th><th>Student ID</th><th>Student Name</th><th>In Time</th><th>Late Minutes</th><th>Out Time</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <tr><td>01</td><td>01 Aug 2026</td><td>Class 8</td><td>Science</td><td>A</td><td>2026</td><td>ST-8001</td><td><div class="fp-person"><div class="fp-avatar">RA</div><div><b>Rahim Ahmed</b><small>Student</small></div></div></td><td>07:52 AM</td><td>0 min</td><td>01:35 PM</td><td><span class="fp-status present">Present</span></td></tr>
                    <tr><td>02</td><td>01 Aug 2026</td><td>Class 8</td><td>Science</td><td>A</td><td>2026</td><td>ST-8002</td><td><div class="fp-person"><div class="fp-avatar">SK</div><div><b>Sadika Khan</b><small>Student</small></div></div></td><td>08:12 AM</td><td><span class="late-min">12 min</span></td><td>01:32 PM</td><td><span class="fp-status late">Late</span></td></tr>
                    <tr><td>03</td><td>01 Aug 2026</td><td>Class 8</td><td>Science</td><td>A</td><td>2026</td><td>ST-8003</td><td><div class="fp-person"><div class="fp-avatar">MH</div><div><b>Mahin Hasan</b><small>Student</small></div></div></td><td>—</td><td>—</td><td>—</td><td><span class="fp-status absent">Absent</span></td></tr>
                    <tr><td>04</td><td>01 Aug 2026</td><td>Class 8</td><td>Science</td><td>A</td><td>2026</td><td>ST-8004</td><td><div class="fp-person"><div class="fp-avatar">NA</div><div><b>Nabila Akter</b><small>Student</small></div></div></td><td>—</td><td>—</td><td>—</td><td><span class="fp-status leave">Leave</span></td></tr>
                    <tr><td>05</td><td>05 Aug 2026</td><td>Class 8</td><td>Science</td><td>A</td><td>2026</td><td>ST-8001</td><td><div class="fp-person"><div class="fp-avatar">RA</div><div><b>Rahim Ahmed</b><small>Student</small></div></div></td><td>—</td><td>—</td><td>—</td><td><span class="fp-status holiday">Holiday</span></td></tr>
                </tbody>
            </table>
        </div>
        <div class="fp-note"><i class="fas fa-info-circle" style="color:#2563eb"></i> Frontend UI only. Device/database data is not connected yet.</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('fpAttendanceSearch');
    const rows = document.querySelectorAll('#fpAttendanceTable tbody tr');

    search?.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();

        rows.forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    document.getElementById('fpFilterButton')?.addEventListener('click', function () {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Filter Applied',
                text: 'Frontend demo filter is ready. Backend data can be connected later.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert('Filter Applied - Frontend UI Demo');
        }
    });
});
</script>

@endsection
