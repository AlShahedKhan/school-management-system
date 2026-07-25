@extends('layouts.school')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    html,
    body {
        max-width: 100vw;
        overflow-x: hidden !important;
        margin: 0;
        padding: 0;
    }

    .main-view-container {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        width: 100%;
        padding: .75rem;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .main-view-container {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    }

    .table-card {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 0;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        width: 100%;
        overflow: hidden;
    }

    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
        background: white !important;
        padding: 15px !important;
    }

    /* ================= Custom Scrollbar ================= */
    .table-responsive::-webkit-scrollbar {
        height: 6px !important;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8fafc !important;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 0px !important;
    }

    /* ================= Table Core ================= */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        border: 1px solid #d1d5db !important;
        font-size: 11px !important;
    }

    th {
        padding: 0 12px !important;
        height: 34px !important;
        line-height: 34px !important;
        white-space: nowrap !important;
        background: #f8fafc !important;
        color: #374151 !important;
        font-weight: 800 !important;
        vertical-align: middle !important;
        text-align: left !important;
        text-transform: capitalize !important;
        letter-spacing: 0.01em !important;
    }
    tr {
        height: 32px !important;
    }

    td {
        padding: 0 12px !important;
        vertical-align: middle !important;
        font-size: 11px !important;
        color: #4b5563 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }

    tbody tr:hover {
        background: #f9fafb !important;
    }

    .btn-outline-premium {
        background: transparent;
        border: 1.5px solid #2563eb;
        color: #2563eb;
        font-weight: 600;
        transition: all .2s ease;
        border-radius: 0;
        cursor: pointer;
    }

    .btn-outline-premium:hover {
        background: #2563eb;
        color: #fff;
    }

    .form-input-fixed {
        width: 100%;
        border: 1px solid #cbd5e1 !important;
        padding: .5rem .7rem;
        border-radius: 0;
        font-size: .85rem;
        background: #fff;
        outline: none;
    }

    .modal-content-sharp {
        border-radius: 0 !important;
    }

    .search-tab {
        cursor: pointer;
        padding: 12px 16px;
        font-size: 10px;
        font-weight: 800;
        text-transform: capitalize;
        border-bottom: 2px solid transparent;
        color: #94a3b8;
        transition: all 0.2s;
    }

    .search-tab.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background: #f8fafc;
    }

    .hidden {
        display: none !important;
    }

    /* ================= Transcript Specific Styling ================= */
    .a4-report {
        width: 210mm;
        min-height: 280mm;
        padding: 12mm;
        margin: 10px auto;
        background: white;
        border: 1px solid #d1d5db;
        color: #000;
        box-sizing: border-box;
    }

    .info-label {
        font-weight: 700;
        width: 110px;
        display: inline-block;
        font-size: 10px;
        text-transform: capitalize;
    }

    .info-value {
        font-weight: 400;
        font-size: 10px;
        text-transform: capitalize;
    }

    .equal-height-container {
        display: flex;
        align-items: stretch;
        gap: 1rem;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        /* Removes browser headers and footers (date, title, URL) */
        @page {
            margin: 0;
        }

        body {
            margin: 1.6cm;
        }

        #resultContainer,
        #resultContainer * {
            visibility: visible;
        }

        #resultContainer {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
        }

        .a4-report {
            border: none;
            box-shadow: none;
            margin: 0;
            width: 100%;
            min-height: auto;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

<div class="main-view-container">
    <div class="max-w-full mx-auto w-full">
        <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Academic
                        Result Management</h2>
                    <div class="flex items-center text-slate-400 text-[12px] mt-1">
                        <span style="text-transform: capitalize;">School</span>
                        <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                        <span id="pageTitle" class="text-slate-500" style="text-transform: capitalize;">Search &
                            Transcripts</span>
                    </div>
                </div>

                <div class="flex flex-row items-center gap-1">
                    <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                        class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                        style="text-transform: capitalize;">Export
                    </button>
                    <button onclick="openSearchModal()"
                        class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                        style="text-transform: capitalize;">Find
                        Result</button>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-responsive" id="resultContainer">
                <table class="w-full" id="mainResultTable">
                    <thead id="resultHeader">
                        <tr>
                            <th class="text-center py-10 text-gray-400 font-medium" style="text-transform: capitalize;">
                                Click "Find Result" to generate
                                academic reports or tabular sheets</th>
                        </tr>
                    </thead>
                    <tbody id="resultBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Universal Search Modal --}}
<div id="searchModal"
    class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md modal-content-sharp shadow-2xl flex flex-col border border-gray-100">
        <div class="flex border-b">
            <div onclick="switchSearchTab('single')" id="tab-single" class="search-tab active flex-1 text-center">Single
                Result</div>
            <div onclick="switchSearchTab('class')" id="tab-class" class="search-tab flex-1 text-center">Classwise
                Result</div>
        </div>

        <div class="p-6 bg-gray-50/30">
            {{-- Single Search Fields --}}
            <div id="single-fields" class="space-y-4">
                <div>
                    <label class="text-[10px] text-gray-500 capitalize font-bold block mb-1">Student ID Number</label>
                    <input type="text" id="s_student_id" class="form-input-fixed h-[36px]"
                        placeholder="Ex: 24012601">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 capitalize font-bold block mb-1">Admit Card Number</label>
                    <input type="text" id="s_admit_no" class="form-input-fixed h-[36px]" placeholder="Ex: 24951080">
                </div>
            </div>

            {{-- Classwise Search Fields --}}
            <div id="class-fields" class="space-y-3 hidden">
                <select id="c_class" onchange="handleClassChange()" class="form-input-fixed h-[36px]">
                    <option value="">Select Class</option>
                </select>
                <select id="c_group" onchange="handleGroupChange()" class="form-input-fixed h-[36px]">
                    <option value="">Select Group</option>
                </select>
                <select id="c_section" onchange="handleSectionChange()" class="form-input-fixed h-[36px]">
                    <option value="">Select Section</option>
                </select>
                <select id="c_session" onchange="handleSessionChange()" class="form-input-fixed h-[36px]">
                    <option value="">Select Session</option>
                </select>
                <select id="c_exam" class="form-input-fixed h-[36px]">
                    <option value="">Select Exam</option>
                </select>
            </div>
        </div>

        <div class="p-4 border-t bg-white flex gap-2">
            <button onclick="closeSearchModal()"
                class="flex-1 h-9 border text-[10px] font-bold capitalize">Cancel</button>
            <button onclick="executeFind()" class="flex-1 h-9 btn-outline-premium text-[10px] capitalize">Generate
            </button>
        </div>
    </div>
</div>

{{-- Export Modal --}}
<div id="exportModal"
    class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
    onclick="this.classList.add('hidden')">
    <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex flex-col gap-1.5">
            {{-- PDF Button --}}
            <button onclick="window.print()"
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                PDF
            </button>

            {{-- EXCEL Button --}}
            <button onclick="exportToExcel()"
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                EXCEL
            </button>

            {{-- PRINT Button --}}
            <button onclick="window.print()"
                class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                PRINT
            </button>

            {{-- CANCEL Button --}}
            <button id="closeExport" onclick="document.getElementById('exportModal').classList.add('hidden')"
                class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all tracking-tighter">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    let searchMode = 'single';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

    document.addEventListener('DOMContentLoaded', () => {
        fetchClasses();
    });

    function toTitleCase(str) {
        if (!str) return 'N/A';
        return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function fetchClasses() {
        axios.get('/api/get-school-classes').then(res => {
            const el = document.getElementById('c_class');
            el.innerHTML = '<option value="">Select Class</option>';
            res.data.data.forEach(item => {
                el.innerHTML +=
                    `<option value="${item.class_name}" data-id="${item.id}">${item.class_name}</option>`;
            });
        });
    }

    function handleClassChange() {
        const classSelect = document.getElementById('c_class');
        const classId = classSelect.options[classSelect.selectedIndex]?.getAttribute('data-id');
        ['c_group', 'c_section', 'c_session', 'c_exam'].forEach(id => document.getElementById(id).innerHTML =
            `<option value="">Select ${id.split('_')[1]}</option>`);

        if (!classId) return;
        axios.get(`/api/get-school-groups?class_id=${classId}`).then(res => {
            const el = document.getElementById('c_group');
            res.data.data.forEach(item => el.innerHTML +=
                `<option value="${item.group_name}" data-id="${item.id}">${item.group_name}</option>`);
            fetchSessions();
        });
    }

    function handleGroupChange() {
        const groupSelect = document.getElementById('c_group');
        const groupId = groupSelect.options[groupSelect.selectedIndex]?.getAttribute('data-id');
        ['c_section', 'c_session', 'c_exam'].forEach(id => document.getElementById(id).innerHTML =
            `<option value="">Select ${id.split('_')[1]}</option>`);
        if (!groupId) {
            fetchSessions();
            return;
        }
        axios.get(`/api/get-school-sections?group_id=${groupId}`).then(res => {
            const el = document.getElementById('c_section');
            res.data.data.forEach(item => el.innerHTML +=
                `<option value="${item.section_name}" data-id="${item.id}">${item.section_name}</option>`);
            fetchSessions();
        });
    }

    function handleSectionChange() {
        fetchSessions();
    }

    function fetchSessions() {
        const c = document.getElementById('c_class');
        const g = document.getElementById('c_group');
        const s = document.getElementById('c_section');
        const cId = c.options[c.selectedIndex]?.getAttribute('data-id') || '';
        const gId = g.options[g.selectedIndex]?.getAttribute('data-id') || '';
        const sId = s.options[s.selectedIndex]?.getAttribute('data-id') || '';

        if (!cId) return;
        axios.get(`/api/get-school-sessions?class_id=${cId}&group_id=${gId}&section_id=${sId}`).then(res => {
            const el = document.getElementById('c_session');
            el.innerHTML = '<option value="">Select Session</option>';
            res.data.data.forEach(item => {
                const val = item.session_year || item.session_name;
                el.innerHTML += `<option value="${val}">${val}</option>`;
            });
        });
    }

    function handleSessionChange() {
        const sess = document.getElementById('c_session').value;
        const cls = document.getElementById('c_class').value;
        if (!sess || !cls) return;
        axios.get(`/api/get-school-exams?session_name=${sess}&class_name=${cls}`).then(res => {
            const el = document.getElementById('c_exam');
            el.innerHTML = '<option value="">Select Exam</option>';
            res.data.data.forEach(item => el.innerHTML +=
                `<option value="${item.exam_name}">${item.exam_name}</option>`);
        });
    }

    function openSearchModal() {
        document.getElementById('searchModal').classList.remove('hidden');
    }

    function closeSearchModal() {
        document.getElementById('searchModal').classList.add('hidden');
    }

    function switchSearchTab(mode) {
        searchMode = mode;
        document.getElementById('tab-single').classList.toggle('active', mode === 'single');
        document.getElementById('tab-class').classList.toggle('active', mode === 'class');
        document.getElementById('single-fields').classList.toggle('hidden', mode !== 'single');
        document.getElementById('class-fields').classList.toggle('hidden', mode !== 'class');
    }

    function executeFind() {
        const payload = searchMode === 'single' ? {
            mode: 'single',
            student_id: document.getElementById('s_student_id').value,
            admit_no: document.getElementById('s_admit_no').value
        } : {
            mode: 'classwise',
            class: document.getElementById('c_class').value,
            group: document.getElementById('c_group').value,
            section: document.getElementById('c_section').value,
            session: document.getElementById('c_session').value,
            exam: document.getElementById('c_exam').value
        };

        axios.post('/api/school-find-results', payload).then(res => {
            if (searchMode === 'single') renderSingleResult(res.data);
            else renderClasswiseTable(res.data);
            closeSearchModal();
        }).catch(err => {
            Swal.fire('Error', err.response?.data?.message || 'Data Fetch Failed', 'error');
        });
    }

    function renderSingleResult(data) {
        const container = document.getElementById('resultContainer');
        const gradingScale = data.grading_scale || [];
        const groupedGrades = Object.values(gradingScale.reduce((acc, g) => {
            const gradeName = g.grade_name || g.grade || g.letter_name || 'N/A';
            const gradePoint = parseFloat(g.grade_point || 0).toFixed(2);
            const key = `${gradeName}|${gradePoint}`;
            const fullMark = Number(g.full_mark);
            if (!acc[key]) {
                acc[key] = {
                    grade_name: gradeName,
                    grade_point: gradePoint,
                    full_mark_50: '',
                    full_mark_100: '',
                };
            }
            if (fullMark === 50) {
                acc[key].full_mark_50 = `${Math.round(Number(g.mark_from || 0))}-${Math.round(Number(g.mark_to || 0))}`;
            }
            if (fullMark === 100) {
                acc[key].full_mark_100 = `${Math.round(Number(g.mark_from || 0))}-${Math.round(Number(g.mark_to || 0))}`;
            }
            return acc;
        }, {})).sort((a, b) => parseFloat(b.grade_point) - parseFloat(a.grade_point));

        const gradeScaleRows = groupedGrades.length
            ? groupedGrades.map(g => `
                <tr>
                    <td>${g.full_mark_50 || '-'}</td>
                    <td>${g.full_mark_100 || '-'}</td>
                    <td>${Number(g.grade_point).toFixed(2)}</td>
                    <td>${g.grade_name ?? 'N/A'}</td>
                </tr>`).join('')
            : '<tr><td colspan="4">No grade scale found</td></tr>';

        const formattedDate = data.publish_datetime || 'N/A';
        const address = [data.school_info?.village, data.school_info?.upazila, data.school_info?.district]
            .filter(Boolean)
            .join(', ');
        const schoolMobile = data.school_info?.mobile ? `Mobile: ${data.school_info.mobile}` : '';
        const positionText = data.position === 'N/A' ? 'N/A' : `${data.position}${getOrdinalSuffix(data.position)}`;

        container.innerHTML = `
        <style>
            @page { size: Legal; margin: 10mm; }
            body { margin: 0; padding: 0; }
            .transcript-page {
                width: 1000px;
                min-height: 1200px;
                margin: 0 auto;
                background: #fff;
                padding: 30px 40px 100px;
                border: 1px solid #000;
                box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
                position: relative;
                overflow: hidden;
            }
            .watermark { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); opacity: 0.03; width: 420px; pointer-events: none; z-index: 0; }
            .header { text-align: center; margin-bottom: 8px; }
            .school-name { font-family: 'Inter', sans-serif; font-weight: bold; font-size: 22px; color: #5B2C8F; margin: 0 0 2px 0; }
            .address, .transcript-title, .mobile { margin: 2px 0; }
            .address { font-size: 10px; }
            .transcript-title {
                font-size: 11px;
                display: inline-block;
                padding: 4px 15px;
                background: #efe3ff;
                border: 1px solid #5B2C8F;
            }
            .mobile { font-size: 10px; font-weight: bold; }
            .top-row { display: flex; gap: 80px; margin-bottom: 8px; }
            .box { flex: 1;}
            .box-header {
                background: #ededed;
                color: #5B2C8F;
                font-weight: bold;
                text-align: center;
                font-size: 11px;
                padding: 4px 4px;
                border: 1px solid #5B2C8F;
                border-bottom: none;
            }
            .info-table, .grade-table, .marksheet table { width: 100%; border-collapse: collapse; font-size: 10px; }
            .info-table td, .grade-table th, .grade-table td, .marksheet th, .marksheet td { border: 1px solid #5B2C8F; padding: 3px 4px; }
            .info-table td.label { font-weight: bold; width: 42%; }
            th:first-child, th:last-child {
                text-align: left !important;
            }
            .grade-table th { font-weight: bold; font-size: 11px; }
            .marksheet td.subject-name { text-align: left; }
            .total-row td.total-label { text-align: right; font-weight: bold; padding-right: 12px; }
            .total-row td.total-value { font-weight: bold; font-size: 15px; }
            .total-value.mark { color: #333; }
            .total-value.gpa { color: #1F4E9C; }
            .total-value.grade { color: #333; }
            .total-value.position { color: #1E8449; }
            .result-summary {
                border: 1px solid #5B2C8F;
                padding: 6px 8px;
                margin: 8px 0 10px;
                background: #fcfbff;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }
            .result-summary .summary-item {
                text-align: center;
            }
            .result-summary .summary-item .label {
                display: block;
                font-size: 10px;
                color: #374151;
                font-weight: 700;
                margin-bottom: 2px;
            }
            .result-summary .summary-item .value {
                display: block;
                font-size: 12px;
                color: #111827;
                font-weight: 700;
            }
            .footer {
                position: absolute;
                left: 40px;
                right: 40px;
                bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                gap: 18px;
            }
            .footer .date {
                font-weight: bold;
                font-size: 11px;
                margin-bottom: 6px;
            }
            .sig-block.left {
                text-align: left;
            }
            .signature-frame {
                min-height: 54px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 6px;
            }
            .signature-frame img {
                max-height: 58px;
                max-width: 180px;
                object-fit: contain;
            }
            .sig-line {
                border-top: 3px solid #5B2C8F;
                margin-top: 8px;
                padding-top: 6px;
                font-weight: bold;
                font-size: 11.5px;
                letter-spacing: 0.5px;
            }
            .subject-summary { margin: 10px 0 8px; display: grid; gap: 4px; font-size: 11px; color: #4b5563; }
            .subject-summary .summary-pill { display: inline-flex; gap: 6px; align-items: center; flex-wrap: wrap; }
            .subject-summary .summary-pill span { font-weight: 700; color: #111827; }
            @media print { body { background: #fff; padding: 0; } .transcript-page { box-shadow: none; border: none; width: auto; } }
        </style>

        <div class="transcript-page">
            ${data.school_info.logo ? `<img src="${data.school_info.logo}" class="watermark" alt="Watermark"/>` : ''}
            <div class="header">
                <div class="school-name">${data.school_info.school_name}</div>
                <div class="mobile">
                    ${data.school_info?.mobile ? data.school_info.mobile : ''}${data.school_info?.mobile && data.school_info?.email ? ' | ' : ''}${data.school_info?.email ? data.school_info.email : ''}
                </div>
                <div class="address">${address}</div>
                <div class="transcript-title">Academic Transcript </div>
            </div>

            <div class="top-row">
                <div class="box">
                    <table class="info-table">
                        <tr><td class="label">Student ID</td><td>${data.student_id_number}</td></tr>
                        <tr><td class="label">Student Name</td><td>${data.student_name}</td></tr>
                        <tr><td class="label">Admit Card No</td><td>${data.admit_card_number || 'N/A'}</td></tr>
                        <tr><td class="label">Class</td><td>${data.class_name}</td></tr>
                        <tr><td class="label">Group</td><td>${data.group_name || 'N/A'}</td></tr>
                        <tr><td class="label">Section</td><td>${data.section_name || 'N/A'}</td></tr>
                        <tr><td class="label">Session</td><td>${data.session_name}</td></tr>
                        <tr><td class="label">Exam Name</td><td>${data.exam_name} - ${data.session_name}</td></tr>
                    </table>
                </div>

                <div class="box">
                    <table class="grade-table">
                        <thead>
                            <tr>
                                <th>50 Marks</th>
                                <th>100 Marks</th>
                                <th>GPA</th>
                                <th>LG</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${gradeScaleRows}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="marksheet">
                <table>
                    <tr>
                        <th>Sl</th>
                        <th>Subject Name</th>
                        <th>Full Mark</th>
                        <th>Theory</th>
                        <th>Practical</th>
                        <th>Mark</th>
                        <th>Grade</th>
                        <th>Point</th>
                    </tr>
                    ${data.subjects.map((s, i) => `
                        <tr>
                            <td>${String(i + 1).padStart(2, '0')}</td>
                            <td class="subject-name">${s.name}</td>
                            <td>${s.full_mark ?? 100}</td>
                            <td>${s.theory_mark ?? 0}</td>
                            <td>${s.practical_mark ?? 0}</td>
                            <td>${s.mark ?? '0'}</td>
                            <td>${s.grade ?? '-'}</td>
                            <td>${parseFloat(s.point).toFixed(2)}</td>
                        </tr>`).join('')}
                        <tr>
                            <td class="text-right" colspan="7">Total Mark</td>
                            <td class="text-left">${data.total_marks}</td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="7">GPA Point</td>
                            <td class="text-left">${data.gpa}</td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="7">Letter Grade</td>
                            <td class="text-left">${data.grade}</td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="7">Position</td>
                            <td class="text-left">${positionText}</td>
                        </tr>
                </table>
            </div>

            <div class="footer">
                <div class="sig-block left">
                    <div class="date">Date & Time: ${formattedDate}</div>
                    <div class="sig-line">Publish Date & Time</div>
                </div>
                <div class="sig-block">
                    <div class="signature-frame">
                        ${data.school_info.principal_signature ? `<img src="${data.school_info.principal_signature}" alt="Principal Signature"/>` : '<div class="text-gray-400">No Signature</div>'}
                    </div>
                    <div class="sig-line">Principal Signature</div>
                </div>
            </div>
        </div>`;
    }

    function getOrdinalSuffix(rank) {
        const j = rank % 10,
              k = rank % 100;
        if (k === 11 || k === 12 || k === 13) return 'th';
        if (j === 1) return 'st';
        if (j === 2) return 'nd';
        if (j === 3) return 'rd';
        return 'th';
    }

    function renderClasswiseTable(data) {
        const container = document.getElementById('resultContainer');
        const gradingScale = data.grading_scale || [];
        const className = document.getElementById('c_class').value;
        const groupName = document.getElementById('c_group').value || 'N/A';
        const sectionName = document.getElementById('c_section').value || 'N/A';
        const sessionName = document.getElementById('c_session').value;
        const examName = document.getElementById('c_exam').value;

        container.innerHTML = `
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

            /* Clear browser artifacts and hide non-target elements */
            @media print { 
                @page { size: A4 landscape; margin: 0; } 
                html, body { height: 100%; margin: 0 !important; padding: 0 !important; overflow: hidden; background: white; }
                
                /* Strict Visibility Toggle: Hides everything except target */
                body * { visibility: hidden !important; }
                #resultContainer, #resultContainer * { visibility: visible !important; }
                
                #resultContainer { 
                    position: absolute; 
                    left: 0; 
                    top: 0; 
                    width: 100%; 
                    padding: 8mm; 
                    box-sizing: border-box; 
                    display: block !important;
                }
                
                .no-print { display: none !important; }
                .a4-landscape-print { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            }
            
            .a4-landscape-print { 
                background: white; 
                width: 100%; 
                font-family: 'Roboto', sans-serif; 
                color: #000;
            }
            
            /* Elite Subtle Grey Border Design - No Border Radius */
            .info-table, .grading-table, #mainResultTable, 
            #mainResultTable th, #mainResultTable td, 
            .info-table td, .grading-table td, 
            .grading-table th, .table-header-box { 
                border: 0.5px solid #d1d5db !important; 
                border-radius: 0 !important; 
            }

            .split-table-container { 
                display: flex; 
                justify-content: space-between; 
                align-items: stretch; 
                margin-bottom: 15px; 
                gap: 20px;
            }
            .info-container, .grading-container { flex: 1; display: flex; flex-direction: column; max-width: 350px; }
            
            .info-table, .grading-table { width: 100%; border-collapse: collapse; flex-grow: 1; }
            .info-table td, .grading-table td, .grading-table th { padding: 3px 6px; font-size: 10px; vertical-align: middle; }
            
            .table-header-box { 
                background: #f9fafb; 
                border-bottom: none !important; 
                padding: 4px; 
                text-align: center; 
                font-weight: 800; 
                font-size: 10px; 
                text-transform: capitalize; 
            }
            
            .vertical-header { padding: 8px 2px !important; vertical-align: bottom; text-align: center; background: #f9fafb; width: 30px; }
            .vertical-header div { 
                writing-mode: vertical-rl; 
                transform: rotate(180deg); 
                text-align: left; 
                max-height: 100px; 
                display: inline-block; 
                font-weight: 700; 
                font-size: 9px; 
                white-space: nowrap; 
            }
            
            #mainResultTable { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
            #mainResultTable td { font-size: 9px; padding: 4px 2px; text-align: center; overflow: hidden; }
            #mainResultTable th { font-size: 9px; font-weight: 700; padding: 4px 2px; background: #f9fafb; }
            
            .student-name-cell { text-align: left !important; padding-left: 8px !important; text-transform: capitalize; }
            .capitalize-all { text-transform: capitalize !important; }
        </style>
        
        <div class="a4-landscape-print">
            <div class="text-center mb-4">
                <h1 class="text-xl font-black mb-0 capitalize-all">${toTitleCase(data.school_name)}</h1>
                <p class="text-[10px] font-bold text-gray-500 mb-0.5 capitalize-all">${toTitleCase(data.location || '')}</p>
                <p class="text-[11px] font-black text-gray-800 capitalize-all tracking-widest">${toTitleCase(examName)} Result Sheet</p>
            </div>
            
            <div class="split-table-container">
                <div class="info-container">
                    <div class="table-header-box">Class Information</div>
                    <table class="info-table">
                        <tr><td width="90">Class</td><td class="font-bold capitalize-all">${toTitleCase(className)}</td></tr>
                        <tr><td>Group</td><td class="font-bold capitalize-all">${toTitleCase(groupName)}</td></tr>
                        <tr><td>Section</td><td class="font-bold capitalize-all">${toTitleCase(sectionName)}</td></tr>
                        <tr><td>Session</td><td class="font-bold">${sessionName}</td></tr>
                    </table>
                </div>
                <div class="grading-container">
                    <div class="table-header-box">Grading Scale</div>
                    <table class="grading-table text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-center">Marks</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${gradingScale.map(g => `
                                        <tr>
                                            <td>${Math.round(g.mark_from)}-${Math.round(g.mark_to)}</td>
                                            <td class="font-bold">${g.grade_name}</td>
                                            <td>${parseFloat(g.grade_point).toFixed(2)}</td>
                                        </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>

            <table id="mainResultTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th width="25">Sl</th>
                        <th width="55">ID</th>
                        <th class="student-name-cell" width="140">Student Name</th>
                        ${data.subjects_list.map(sub => `<th class="vertical-header"><div>${toTitleCase(sub)}</div></th>`).join('')}
                        <th width="40">Total</th>
                        <th width="40">GPA</th>
                        <th width="40">Grade</th>
                        <th width="35" class="no-print">Del</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.students.map((std, i) => `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${std.student_id}</td>
                                    <td class="student-name-cell font-bold truncate">${toTitleCase(std.name)}</td>
                                    ${data.subjects_list.map(sub => `<td>${std.marks[sub] || '0'}</td>`).join('')}
                                    <td class="font-bold">${std.total}</td>
                                    <td class="font-black text-blue-800">${std.gpa}</td>
                                    <td class="font-bold">${std.grade}</td>
                                    <td class="no-print">
                                        <button onclick="confirmDelete(${std.id})" class="text-red-500">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>`).join('')}
                </tbody>
            </table>
        </div>`;
    }

    function exportToExcel() {
        const table = document.getElementById('mainResultTable');
        if (!table || table.rows.length <= 1) return;
        const wb = XLSX.utils.table_to_book(table, {
            sheet: "Results"
        });
        XLSX.writeFile(wb, "Exam_Results.xlsx");
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete'
        }).then((res) => {
            if (res.isConfirmed) axios.delete(`/api/school-results/${id}`).then(() => executeFind());
        });
    }
</script>
@endsection