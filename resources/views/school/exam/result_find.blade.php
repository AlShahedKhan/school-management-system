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
    .main-view-container {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        width: 100%;
        padding: .75rem;
        box-sizing: border-box;
    }

    .search-tab {
        cursor: pointer;
        padding: 8px 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: capitalize;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        transition: all 0.2s;
        background: #fff;
    }

    .search-tab.active {
        color: #2563eb;
        border-color: #2563eb;
        background: #eff6ff;
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
        <x-school.list-header
            title="Academic Result Management"
            breadcrumb-current="Search & Transcripts"
            actions-class="grid w-full grid-cols-2 gap-2 lg:flex lg:w-auto"
            keep-title
        >
            <x-slot:actions>
                <x-dropdown button-id="btnResultExport" menu-id="resultExportDropdown" label="Export" align="full">
                    <x-dropdown.item onclick="window.print()">PDF</x-dropdown.item>
                    <x-dropdown.item onclick="exportToExcel()">Excel</x-dropdown.item>
                    <x-dropdown.item onclick="window.print()">Print</x-dropdown.item>
                </x-dropdown>

                <x-button.primary type="button" onclick="openSearchModal()" class="w-full">
                    Find Result
                </x-button.primary>
            </x-slot:actions>
        </x-school.list-header>

        <div id="resultContainer" class="w-full overflow-x-auto">
            <x-school.data-table
                :empty="false"
                :empty-colspan="1"
                empty-message="Click Find Result to generate academic reports."
                :show-footer="false"
                min-width="720px"
            >
                <x-slot:columns>
                    <colgroup>
                        <col style="width:100%;">
                    </colgroup>
                </x-slot:columns>

                <x-slot:head>
                    <x-table.th unstyled class="h-12 border border-gray-300 px-3 text-center font-normal text-gray-500">
                        Click "Find Result" to generate academic reports or tabular sheets
                    </x-table.th>
                </x-slot:head>
            </x-school.data-table>
        </div>
    </div>
</div>

{{-- Universal Search Modal --}}
<x-modal.form
    id="searchModal"
    form-id="resultSearchForm"
    title="Find Result"
    close-button-id="closeResultSearchModal"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[480px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    panel-style="border-radius:4px; max-height:min(520px, calc(100dvh - 2.5rem));"
    title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="block"
    onsubmit="event.preventDefault(); executeFind();"
>
    <div class="mb-4 grid grid-cols-2 gap-2">
        <button type="button" onclick="switchSearchTab('single')" id="tab-single" class="search-tab active text-center">
            Single Result
        </button>
        <button type="button" onclick="switchSearchTab('class')" id="tab-class" class="search-tab text-center">
            Classwise Result
        </button>
    </div>

    <div id="single-fields" class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <div class="relative">
            <x-input.control id="s_student_id" class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="s_student_id">Student ID Number</x-input.floating-label>
        </div>
        <div class="relative">
            <x-input.control id="s_admit_no" class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="s_admit_no">Admit Card Number</x-input.floating-label>
        </div>
    </div>

    <div id="class-fields" class="hidden grid grid-cols-1 gap-3 md:grid-cols-2">
        <div class="relative">
            <x-input.dropdown-select id="c_class" placeholder="Select Class" :options="[]" />
            <x-input.floating-label for="c_class" :floating="false">Class</x-input.floating-label>
        </div>
        <div class="relative">
            <x-input.dropdown-select id="c_group" placeholder="Select Group" :options="[]" />
            <x-input.floating-label for="c_group" :floating="false">Group</x-input.floating-label>
        </div>
        <div class="relative">
            <x-input.dropdown-select id="c_section" placeholder="Select Section" :options="[]" />
            <x-input.floating-label for="c_section" :floating="false">Section</x-input.floating-label>
        </div>
        <div class="relative">
            <x-input.dropdown-select id="c_session" placeholder="Select Session" :options="[]" />
            <x-input.floating-label for="c_session" :floating="false">Session</x-input.floating-label>
        </div>
        <div class="relative md:col-span-2">
            <x-input.dropdown-select id="c_exam" placeholder="Select Exam" :options="[]" />
            <x-input.floating-label for="c_exam" :floating="false">Exam</x-input.floating-label>
        </div>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
            <x-button.secondary type="button" onclick="closeSearchModal()" class="w-full">Cancel</x-button.secondary>
            <x-button.primary type="submit" class="w-full">Generate</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

<script>
    let searchMode = 'single';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.withCredentials = true;

    axios.interceptors.response.use(response => {
        sessionStorage.removeItem('result-find-csrf-refresh');
        return response;
    }, error => {
        if (error.response?.status === 419 && !sessionStorage.getItem('result-find-csrf-refresh')) {
            sessionStorage.setItem('result-find-csrf-refresh', '1');
            window.location.reload();
        }

        return Promise.reject(error);
    });

    document.addEventListener('DOMContentLoaded', () => {
        initializeResultDropdownEvents();
        fetchClasses();
    });

    function toTitleCase(str) {
        if (!str) return 'N/A';
        return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function escapeResultHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function formatResultNumber(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '0';
        return Number.isInteger(number)
            ? String(number)
            : number.toFixed(2).replace(/\.?0+$/, '');
    }

    function formatResultComponent(value) {
        const number = Number(value);
        return Number.isFinite(number) && number !== 0 ? formatResultNumber(number) : '-';
    }

    function resultDropdownParts(id) {
        const input = document.getElementById(id);
        const root = input?.closest('[data-dropdown-select]');

        return {
            input,
            root,
            label: root?.querySelector('[data-dropdown-select-label]'),
            menu: root?.querySelector('[data-dropdown-select-menu]'),
        };
    }

    function selectedResultOption(id) {
        const { input, menu } = resultDropdownParts(id);

        return Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(input?.value || ''));
    }

    function selectedResultId(id) {
        return selectedResultOption(id)?.dataset.optionId || '';
    }

    function setResultDropdownValue(id, value = '', label = null, shouldNotify = false) {
        const parts = resultDropdownParts(id);
        if (!parts.input) return;

        const selected = Array.from(parts.menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(value || ''));
        const placeholder = parts.label?.dataset.placeholder || 'Select...';

        parts.input.value = value || '';
        if (parts.label) {
            parts.label.textContent = label ?? selected?.textContent.trim() ?? placeholder;
        }

        parts.menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            const isSelected = option === selected;
            option.classList.toggle('bg-slate-100', isSelected);
            option.classList.toggle('text-slate-900', isSelected);
            option.classList.toggle('text-slate-800', !isSelected);
            option.setAttribute('aria-selected', String(isSelected));
        });

        if (shouldNotify) {
            parts.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function fillResultOptions(id, data, valueField, labelField = valueField) {
        const parts = resultDropdownParts(id);
        if (!parts.menu) return;

        parts.menu.innerHTML = '';

        (data || []).forEach(item => {
            const value = item[valueField] ?? '';
            const label = item[labelField] ?? value;
            const option = document.createElement('button');
            option.type = 'button';
            option.className =
                'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
            option.dataset.value = String(value);
            option.dataset.optionId = String(item.id ?? '');
            option.setAttribute('data-dropdown-select-option', '');
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');
            option.textContent = label;

            option.addEventListener('click', () => {
                setResultDropdownValue(id, option.dataset.value, option.textContent.trim());
                parts.menu.classList.add('hidden');
                parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                parts.input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            parts.menu.appendChild(option);
        });

        setResultDropdownValue(id, '');
    }

    function clearResultDropdowns(ids) {
        ids.forEach(id => fillResultOptions(id, [], ''));
    }

    function initializeResultDropdownEvents() {
        document.getElementById('c_class')?.addEventListener('change', handleClassChange);
        document.getElementById('c_group')?.addEventListener('change', handleGroupChange);
        document.getElementById('c_section')?.addEventListener('change', handleSectionChange);
        document.getElementById('c_session')?.addEventListener('change', handleSessionChange);
    }

    function fetchClasses() {
        return axios.get('/api/get-school-classes').then(res => {
            fillResultOptions('c_class', res.data.data, 'class_name');
        });
    }

    function handleClassChange() {
        const classId = selectedResultId('c_class');
        clearResultDropdowns(['c_group', 'c_section', 'c_session', 'c_exam']);

        if (!classId) return;
        return axios.get(`/api/get-school-groups?class_id=${classId}`).then(async res => {
            fillResultOptions('c_group', res.data.data, 'group_name');
            await fetchSessions();
        });
    }

    function handleGroupChange() {
        const groupId = selectedResultId('c_group');
        clearResultDropdowns(['c_section', 'c_session', 'c_exam']);

        if (!groupId) {
            return fetchSessions();
        }

        return axios.get(`/api/get-school-sections?group_id=${groupId}`).then(async res => {
            fillResultOptions('c_section', res.data.data, 'section_name');
            await fetchSessions();
        });
    }

    function handleSectionChange() {
        return fetchSessions();
    }

    function fetchSessions() {
        const cId = selectedResultId('c_class');
        const gId = selectedResultId('c_group');
        const sId = selectedResultId('c_section');

        if (!cId) return;

        return axios.get(`/api/get-school-sessions?class_id=${cId}&group_id=${gId}&section_id=${sId}`).then(res => {
            const sessions = (res.data.data || []).map(item => ({
                ...item,
                session_value: item.session_year || item.session_name,
            }));
            fillResultOptions('c_session', sessions, 'session_value');
        });
    }

    function handleSessionChange() {
        const sess = document.getElementById('c_session').value;
        const cls = document.getElementById('c_class').value;
        clearResultDropdowns(['c_exam']);

        if (!sess || !cls) return;

        return axios.get(`/api/get-school-exams?session_name=${sess}&class_name=${cls}`).then(res => {
            fillResultOptions('c_exam', res.data.data, 'exam_name');
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
            .reference-marksheet {
                width: 100%;
                margin-top: 10px;
                overflow-x: auto;
            }
            .reference-result-table {
                width: 100%;
                min-width: 760px;
                table-layout: fixed;
                border-collapse: collapse;
                border: 1px solid #777;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 10px;
                line-height: 1.05;
                color: #111;
            }
            .reference-result-table th,
            .reference-result-table td {
                height: 25px;
                border: 1px solid #777;
                padding: 3px 5px;
                text-align: center;
                vertical-align: middle;
            }
            .reference-result-table thead tr:first-child th {
                background: #f2d5a3;
                font-weight: 700;
            }
            .reference-result-table thead .component-heading {
                background: #dce8bf;
                color: #2f6c42;
                font-weight: 700;
            }
            .reference-result-table .subject-cell {
                padding-left: 7px;
                text-align: left;
                font-weight: 500;
            }
            .reference-result-table tbody tr:nth-child(even) td {
                background: #fafafa;
            }
            .reference-result-table tfoot td {
                background: #dcebc4;
                color: #397349;
                font-weight: 700;
            }
            .reference-result-table tfoot .exam-total-label {
                background: #d5d6e7;
                color: #111;
                text-align: center;
            }
            .reference-result-table .subject-column { width: 28%; }
            .reference-result-table .mark-column { width: 8%; }
            .reference-result-table .component-column { width: 6%; }
            .reference-result-table .result-column { width: 8%; }
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

            <div class="reference-marksheet">
                <table class="reference-result-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="subject-column">Name of Subjects</th>
                            <th rowspan="2" class="mark-column">Full<br>Marks</th>
                            <th rowspan="2" class="mark-column">Highest<br>Marks</th>
                            <th colspan="4">Obtaining Marks</th>
                            <th rowspan="2" class="result-column">Total<br>Marks</th>
                            <th rowspan="2" class="result-column">Letter<br>Grade</th>
                            <th rowspan="2" class="result-column">Grade<br>Point</th>
                        </tr>
                        <tr>
                            <th class="component-heading component-column">TU</th>
                            <th class="component-heading component-column">MCQ</th>
                            <th class="component-heading component-column">WR</th>
                            <th class="component-heading component-column">PR</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.subjects.map(subject => `
                            <tr>
                                <td class="subject-cell">${escapeResultHtml(subject.name || '-')}</td>
                                <td>${formatResultNumber(subject.full_mark ?? 0)}</td>
                                <td>${formatResultNumber(subject.highest_mark ?? subject.mark ?? 0)}</td>
                                <td>${formatResultComponent(subject.tutorial_mark)}</td>
                                <td>${formatResultComponent(subject.mcq_mark)}</td>
                                <td>${formatResultComponent(subject.writing_mark ?? subject.theory_mark)}</td>
                                <td>${formatResultComponent(subject.practical_mark)}</td>
                                <td>${formatResultNumber(subject.mark ?? 0)}</td>
                                <td>${escapeResultHtml(subject.grade ?? '-')}</td>
                                <td>${formatResultNumber(subject.point ?? 0)}</td>
                            </tr>`).join('')}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="exam-total-label">Total Exam Marks</td>
                            <td>${formatResultNumber(data.subjects.reduce((total, subject) => total + Number(subject.full_mark || 0), 0))}</td>
                            <td colspan="5">Obtained Marks &amp; GPA</td>
                            <td>${formatResultNumber(data.total_marks)}</td>
                            <td>${escapeResultHtml(data.grade)}</td>
                            <td>${formatResultNumber(data.gpa)}</td>
                        </tr>
                    </tfoot>
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
                .class-result-table-frame { overflow: visible !important; border: 0 !important; }
                #mainResultTable { width: 100% !important; min-width: 0 !important; table-layout: fixed !important; }
                #mainResultTable .sticky-column { position: static !important; }
                #mainResultTable .subject-header {
                    width: 30px !important;
                    min-width: 30px !important;
                    height: 104px !important;
                    padding: 5px 2px !important;
                    vertical-align: bottom !important;
                }
                #mainResultTable .subject-header div {
                    display: inline-block;
                    max-height: 94px;
                    writing-mode: vertical-rl;
                    transform: rotate(180deg);
                    white-space: nowrap;
                }
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
            
            .class-result-table-frame {
                width: 100%;
                margin-top: 10px;
                overflow-x: auto;
                border: 1px solid #d1d5db;
                background: #fff;
                scrollbar-width: thin;
                scrollbar-color: #94a3b8 #f1f5f9;
            }
            .class-result-table-frame::-webkit-scrollbar { height: 8px; }
            .class-result-table-frame::-webkit-scrollbar-track { background: #f1f5f9; }
            .class-result-table-frame::-webkit-scrollbar-thumb { background: #94a3b8; }

            .subject-header {
                width: 96px;
                min-width: 96px;
                height: 56px;
                padding: 6px 5px !important;
                text-align: center;
                vertical-align: middle;
                background: #f8fafc;
            }
            .subject-header div {
                display: -webkit-box;
                overflow: hidden;
                font-size: 9px;
                font-weight: 700;
                line-height: 1.25;
                white-space: normal;
                overflow-wrap: anywhere;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
            }
            
            #mainResultTable {
                width: max-content;
                min-width: 100%;
                border: 0 !important;
                border-collapse: collapse;
                table-layout: fixed;
                margin: 0;
            }
            #mainResultTable td {
                height: 34px;
                font-size: 9px;
                padding: 5px 6px;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
            }
            #mainResultTable th { font-size: 9px; font-weight: 700; padding: 6px; background: #f8fafc; }
            #mainResultTable tbody tr:nth-child(even) td { background: #f8fafc; }
            #mainResultTable tbody tr:hover td { background: #eff6ff; }

            #mainResultTable .sticky-column {
                position: sticky;
                z-index: 2;
                background: #fff;
            }
            #mainResultTable thead .sticky-column {
                z-index: 4;
                background: #f8fafc;
            }
            #mainResultTable .serial-column { left: 0; width: 42px; min-width: 42px; }
            #mainResultTable .id-column { left: 42px; width: 112px; min-width: 112px; }
            #mainResultTable .name-column {
                left: 154px;
                width: 180px;
                min-width: 180px;
                box-shadow: 2px 0 0 #d1d5db;
            }
            
            .student-name-cell {
                text-align: left !important;
                padding-left: 10px !important;
                text-transform: capitalize;
                text-overflow: ellipsis;
            }
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

            <div class="class-result-table-frame">
                <table id="mainResultTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="sticky-column serial-column">Sl</th>
                            <th class="sticky-column id-column">ID</th>
                            <th class="sticky-column name-column student-name-cell">Student Name</th>
                            ${data.subjects_list.map(sub => `<th class="subject-header" title="${escapeResultHtml(toTitleCase(sub))}"><div>${escapeResultHtml(toTitleCase(sub))}</div></th>`).join('')}
                            <th width="72">Total</th>
                            <th width="64">GPA</th>
                            <th width="64">Grade</th>
                            <th width="48" class="no-print">Del</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.students.map((std, i) => `
                                    <tr>
                                        <td class="sticky-column serial-column">${i + 1}</td>
                                        <td class="sticky-column id-column font-mono">${escapeResultHtml(std.student_id)}</td>
                                        <td class="sticky-column name-column student-name-cell font-bold" title="${escapeResultHtml(toTitleCase(std.name))}">${escapeResultHtml(toTitleCase(std.name))}</td>
                                        ${data.subjects_list.map(sub => `<td>${formatResultNumber(std.marks[sub] ?? 0)}</td>`).join('')}
                                        <td class="font-bold">${formatResultNumber(std.total)}</td>
                                        <td class="font-black text-blue-800">${formatResultNumber(std.gpa)}</td>
                                        <td class="font-bold">${escapeResultHtml(std.grade)}</td>
                                        <td class="no-print">
                                            <button type="button" title="Delete result" aria-label="Delete result" onclick="confirmDelete(${Number(std.id)})" class="mx-auto flex h-7 w-7 items-center justify-center text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600">
                                                <i class="far fa-trash-alt" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>`).join('')}
                    </tbody>
                </table>
            </div>
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
