@extends('layouts.school')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

   

<div class="main-view-container fp-main-view">
    <div class="max-w-full mx-auto w-full">
        
        <div class="mb-3 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-blue-600">Fingerprint Attendance</div>
                <div class="mt-1 flex items-center gap-2 text-[10px] text-slate-400">
                    <span>School</span>
                    <i class="mdi mdi-chevron-right text-xs"></i>
                    <span class="font-semibold text-slate-600">Teacher Attendance</span>
                </div>
                <h1 class="mt-1 text-[18px] font-black text-slate-900">Teacher Attendance</h1>
                <p class="mt-1 text-[10px] text-slate-500">Monitor teacher fingerprint attendance records.</p>
            </div>

            <div class="flex items-center gap-2 text-[10px] font-semibold text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Fingerprint Device Online
            </div>
        </div>


        <div class="fp-shell">
            <div class="border-b border-slate-200 px-3 py-2">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-700">Teacher Attendance</div>
            </div>

            
        <div class="flex flex-col gap-2 border-b border-slate-200 px-3 py-2 lg:flex-row lg:items-center lg:justify-between">
            <div class="fp-search-wrap">
                <i class="mdi mdi-magnify"></i>
                <input id="fpSearch" type="text" class="fp-search-input" placeholder="Search...">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="fpRestoreBtn" class="fp-toolbar-btn">
                    <i class="mdi mdi-restore text-sm"></i>
                    Restore
                </button>

                <button type="button" id="fpFilterBtn" class="fp-toolbar-btn">
                    <i class="mdi mdi-filter-variant text-sm"></i>
                    Filter
                </button>

                <div class="relative">
                    <button type="button" id="fpStatusBtn" class="fp-toolbar-btn">
                        <i class="mdi mdi-list-status text-sm"></i>
                        <span id="fpStatusLabel">Status</span>
                        <i class="mdi mdi-chevron-down text-sm"></i>
                    </button>

                    <div id="fpStatusMenu" class="fp-status-menu">
                        <button class="fp-menu-btn" data-status="">All Status</button>
                        <button class="fp-menu-btn" data-status="present">Present</button>
                        <button class="fp-menu-btn" data-status="absent">Absent</button>
                        <button class="fp-menu-btn" data-status="late">Late</button>
                        <button class="fp-menu-btn" data-status="leave">Leave</button>
                        <button class="fp-menu-btn" data-status="holiday">Holiday</button>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" id="fpExportBtn" class="fp-toolbar-btn primary">
                        <i class="mdi mdi-export-variant text-sm"></i>
                        Export
                        <i class="mdi mdi-chevron-down text-sm"></i>
                    </button>

                    <div id="fpExportMenu" class="fp-export-menu">
                        <button class="fp-menu-btn"><i class="mdi mdi-file-excel-outline mr-1"></i> Export Excel</button>
                        <button class="fp-menu-btn"><i class="mdi mdi-file-pdf-box mr-1"></i> Export PDF</button>
                        <button class="fp-menu-btn"><i class="mdi mdi-printer-outline mr-1"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>

            
        <div id="fpFilterPanel" class="fp-filter-panel border-b border-slate-200 bg-slate-50/70 px-3 py-3">
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-12">
                
        <div class="xl:col-span-6">
            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Teacher</label>
            <select data-fp-filter class="fp-filter-select"><option>All Teachers</option><option>Abdul Hasan — T-1001</option><option>Sumaiya Rahman — T-1002</option><option>Mahmud Karim — T-1003</option></select>
        </div>

        <div class="xl:col-span-2">
            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Month</label>
            <select data-fp-filter class="fp-filter-select"><option>August</option><option>July</option><option>June</option></select>
        </div>

        <div class="xl:col-span-2">
            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500">Year</label>
            <select data-fp-filter class="fp-filter-select"><option>2026</option><option>2025</option></select>
        </div>

                <div class="flex items-end gap-2 xl:col-span-2">
                    <button type="button" id="fpFilterReset" class="fp-toolbar-btn w-full justify-center">Reset</button>
                    <button type="button" id="fpFilterApply" class="fp-toolbar-btn primary w-full justify-center">Apply</button>
                </div>
            </div>
        </div>

            
        <div class="fp-table-scroll">
            <table id="fpAttendanceTable" class="w-full border-collapse">
                <thead><tr><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">SL</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Date</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Teacher Name</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">ID Number</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Designation</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">In Time</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Late Minutes</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Out Time</th><th class="h-8 whitespace-nowrap border border-slate-300 bg-slate-100 px-3 text-left text-[9px] font-black uppercase tracking-wide text-slate-500">Status</th></tr></thead>
                <tbody id="fpAttendanceBody">
                    <tr data-status="present"><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">01</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">01-August-2026</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">
        <div class="flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-slate-100 text-[9px] font-black text-slate-600">AH</div>
            <div>
                <div class="text-[10px] font-bold text-slate-800">Abdul Hasan</div>
                <div class="text-[8px] text-slate-400">Teacher</div>
            </div>
        </div>
    </td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">T-1001</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">Senior Teacher</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">07:55 AM</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">0 min</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">03:05 PM</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 "><span class="fp-status-badge present">Present</span></td></tr><tr data-status="late"><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">02</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">02-August-2026</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">
        <div class="flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-slate-100 text-[9px] font-black text-slate-600">SR</div>
            <div>
                <div class="text-[10px] font-bold text-slate-800">Sumaiya Rahman</div>
                <div class="text-[8px] text-slate-400">Teacher</div>
            </div>
        </div>
    </td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">T-1002</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">Assistant Teacher</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">08:22 AM</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 "><span class="font-bold text-orange-600">7 min</span></td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">03:10 PM</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 "><span class="fp-status-badge late">Late</span></td></tr><tr data-status="absent"><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">03</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">03-August-2026</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">
        <div class="flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-slate-100 text-[9px] font-black text-slate-600">MK</div>
            <div>
                <div class="text-[10px] font-bold text-slate-800">Mahmud Karim</div>
                <div class="text-[8px] text-slate-400">Teacher</div>
            </div>
        </div>
    </td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">T-1003</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">Teacher</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">-</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">-</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 ">-</td><td class="h-9 whitespace-nowrap border border-slate-300 px-3 text-[10px] text-slate-700 "><span class="fp-status-badge absent">Absent</span></td></tr>
                </tbody>
            </table>
        </div>

        <div id="fpEmptyState" class="fp-empty hidden">
            No teacher attendance found.
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2">
            <div class="text-[9px] text-slate-400">
                Showing frontend demo records
            </div>
            <div class="flex items-center gap-1 text-[9px] text-slate-500">
                <button class="h-6 w-6 border border-slate-300 bg-white"><i class="mdi mdi-chevron-left"></i></button>
                <button class="h-6 w-6 border border-slate-900 bg-slate-900 text-white">1</button>
                <button class="h-6 w-6 border border-slate-300 bg-white"><i class="mdi mdi-chevron-right"></i></button>
            </div>
        </div>

        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('fpSearch');
            const filterBtn = document.getElementById('fpFilterBtn');
            const filterPanel = document.getElementById('fpFilterPanel');
            const restoreBtn = document.getElementById('fpRestoreBtn');
            const statusBtn = document.getElementById('fpStatusBtn');
            const statusMenu = document.getElementById('fpStatusMenu');
            const statusLabel = document.getElementById('fpStatusLabel');
            const exportBtn = document.getElementById('fpExportBtn');
            const exportMenu = document.getElementById('fpExportMenu');
            const empty = document.getElementById('fpEmptyState');

            let currentStatus = '';

            function applyClientFilter() {
                const q = (search?.value || '').toLowerCase().trim();
                let visible = 0;

                document.querySelectorAll('#fpAttendanceTable tbody tr').forEach(function (row) {
                    const matchSearch = row.innerText.toLowerCase().includes(q);
                    const rowStatus = (row.dataset.status || '').toLowerCase();
                    const matchStatus = !currentStatus || rowStatus === currentStatus;
                    const show = matchSearch && matchStatus;

                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                empty?.classList.toggle('hidden', visible !== 0);
            }

            search?.addEventListener('input', applyClientFilter);

            filterBtn?.addEventListener('click', function () {
                filterPanel?.classList.toggle('show');
            });

            restoreBtn?.addEventListener('click', function () {
                if (search) search.value = '';
                currentStatus = '';
                if (statusLabel) statusLabel.textContent = 'Status';
                document.querySelectorAll('[data-fp-filter]').forEach(el => el.selectedIndex = 0);
                filterPanel?.classList.remove('show');
                applyClientFilter();
            });

            statusBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                statusMenu?.classList.toggle('show');
                exportMenu?.classList.remove('show');
            });

            exportBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                exportMenu?.classList.toggle('show');
                statusMenu?.classList.remove('show');
            });

            document.querySelectorAll('[data-status]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    currentStatus = this.dataset.status || '';
                    if (statusLabel) statusLabel.textContent = this.textContent.trim();
                    statusMenu?.classList.remove('show');
                    applyClientFilter();
                });
            });

            document.getElementById('fpFilterApply')?.addEventListener('click', function () {
                filterPanel?.classList.remove('show');
                Toastify({
                    text: 'Filter applied',
                    duration: 1400,
                    gravity: 'top',
                    position: 'right',
                    style: { background: '#0f172a' }
                }).showToast();
            });

            document.getElementById('fpFilterReset')?.addEventListener('click', function () {
                document.querySelectorAll('[data-fp-filter]').forEach(el => el.selectedIndex = 0);
            });

            document.addEventListener('click', function () {
                statusMenu?.classList.remove('show');
                exportMenu?.classList.remove('show');
            });
        });
    </script>

@endsection