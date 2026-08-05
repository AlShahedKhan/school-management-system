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

        .grade-table th,
        .grade-table td {
            height: 10px !important;
            padding: 0 6px !important;
            line-height: 20px;
            vertical-align: middle;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <x-school.list-header title="Academic Result Management" breadcrumb-current="Search & Transcripts"
                actions-class="grid w-full grid-cols-2 gap-2 lg:flex lg:w-auto" keep-title>
                <x-slot:actions>
                    <x-dropdown button-id="btnResultExport" menu-id="resultExportDropdown" label="Export" align="full">
                        <x-dropdown.item onclick="exportResultPdf()">PDF</x-dropdown.item>
                        <x-dropdown.item onclick="exportToExcel()">Excel</x-dropdown.item>
                        <x-dropdown.item onclick="printResult()">Print</x-dropdown.item>
                    </x-dropdown>

                    <x-button.primary type="button"
                        onclick="document.getElementById('searchModal')?.classList.remove('hidden')" class="w-full">
                        Find Result
                    </x-button.primary>
                </x-slot:actions>
            </x-school.list-header>

            <div id="resultContainer" class="w-full overflow-x-auto">
                <x-school.data-table :empty="false" :empty-colspan="1"
                    empty-message="Click Find Result to generate academic reports." :show-footer="false" min-width="720px">
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
    <x-modal.form id="searchModal" form-id="resultSearchForm" title="Find Result" close-button-id="closeResultSearchModal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[480px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
        panel-style="border-radius:4px; max-height:min(520px, calc(100dvh - 2.5rem));"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
        fields-class="block" onsubmit="event.preventDefault(); executeFind();">
        <div id="single-fields" class="grid grid-cols-1 gap-3">
            <div class="relative">
                <x-input.control id="s_admit_no" class="peer placeholder:text-transparent" placeholder=" " />
                <x-input.floating-label for="s_admit_no">Admit Card Number</x-input.floating-label>
            </div>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary type="button" onclick="document.getElementById('searchModal')?.classList.add('hidden')"
                    class="w-full">Cancel</x-button.secondary>
                <x-button.primary type="submit" class="w-full">Generate</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @push('scripts')
        <script>
            window.openSearchModal = function() {
                document.getElementById('searchModal')?.classList.remove('hidden');
            };
            window.closeSearchModal = function() {
                document.getElementById('searchModal')?.classList.add('hidden');
            };

            let currentResultData = null;
            const pdfResultData = @json($pdfResultData ?? null);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
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
                return Number.isInteger(number) ?
                    String(number) :
                    number.toFixed(2).replace(/\.?0+$/, '');
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
                const {
                    input,
                    menu
                } = resultDropdownParts(id);

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
                    parts.input.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
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
                        parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute(
                            'aria-expanded', 'false');
                        parts.input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
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

            function executeFind() {
                const payload = {
                    mode: 'single',
                    admit_no: document.getElementById('s_admit_no').value,
                };

                axios.post('/api/school-find-results', payload).then(res => {
                    renderSingleResult(res.data);
                    closeSearchModal();
                }).catch(err => {
                    Swal.fire('Error', err.response?.data?.message || 'Data Fetch Failed', 'error');
                });
            }

            function renderSingleResult(data) {
                currentResultData = data;
                const tableSubjects = [...(data.subjects || [])];
                const missingSubjectRows = Math.max(0, 18 - tableSubjects.length);
                const container = document.getElementById('resultContainer');
                container.removeAttribute('data-pdf-ready');
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
                        acc[key].full_mark_50 =
                            `${Math.round(Number(g.mark_from || 0))}-${Math.round(Number(g.mark_to || 0))}`;
                    }
                    if (fullMark === 100) {
                        acc[key].full_mark_100 =
                            `${Math.round(Number(g.mark_from || 0))}-${Math.round(Number(g.mark_to || 0))}`;
                    }
                    return acc;
                }, {})).sort((a, b) => parseFloat(b.grade_point) - parseFloat(a.grade_point));

                const gradeScaleRows = groupedGrades.length ?
                    groupedGrades.map(g => `
                <tr>
                    <td>${g.full_mark_50 || '-'}</td>
                    <td>${g.full_mark_100 || '-'}</td>
                    <td>${Number(g.grade_point).toFixed(2)}</td>
                    <td>${g.grade_name ?? 'N/A'}</td>
                </tr>`).join('') :
                    '<tr><td colspan="4">No grade scale found</td></tr>';

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
            .transcript-header {
                display: grid;
                grid-template-columns: 140px minmax(0, 1fr) 140px;
                align-items: center;
                min-height: 128px;
                gap: 24px;
                margin-bottom: 12px;
                padding: 0 6px 12px;
            }
            .header { text-align: center; margin: 0; }
            .school-logo-frame,
            .student-photo-frame {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 108px;
                overflow: hidden;
                background: #fff;
            }
            .school-logo-frame {
                width: 140px;
                border: 0;
                border-radius: 0;
            }
            .school-logo-frame img {
                max-width: 126px;
                max-height: 94px;
                object-fit: contain;
            }
            .student-photo-frame {
                width: 108px;
                height: 108px;
                justify-self: end;
                border: 2px solid #1e3a6d;
                border-radius: 9999px;
            }
            .student-photo-frame img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .student-photo-fallback {
                color: #9ca3af;
                font-size: 9px;
                text-align: center;
            }
            .exam-meta {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: center;
                gap: 8px;
                min-height: 32px;
                margin: 0 0 10px;
                padding: 5px 12px;
                border: 1px solid #8ba0bb;
                background: #fbfdff;
                color: #17345f;
                font-size: 10px;
                font-weight: 600;
                text-align: center;
            }
            .exam-meta > div {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .exam-meta strong {
                color: #102f63;
                font-weight: 800;
            }
            @media (max-width: 640px) {
                .exam-meta {
                    grid-template-columns: 1fr;
                }
            }
            .school-name {
                margin: 0 0 4px;
                color: #102f63;
                font-family: 'Inter', sans-serif;
                font-size: 30px;
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: 0;
                text-transform: uppercase;
            }
            .address, .transcript-title, .mobile { margin: 3px 0; }
            .address { color: #294a79; font-size: 14px; font-weight: 600; }
            .transcript-title {
                display: inline-block;
                position: relative;
                width: 210px;
                height: 30px;
                min-height: 30px;
                margin-top: 8px;
                padding: 0;
                border: 0;
                border-radius: 9999px;
                background: #123a78;
                color: #fff;
                font-size: 13px;
                font-weight: 700;
                line-height: normal;
                letter-spacing: .02em;
                text-transform: uppercase;
            }
            .transcript-title > span {
                display: block;
                position: absolute;
                top: 50%;
                right: 0;
                left: 0;
                width: 100%;
                line-height: 14px;
                text-align: center;
                transform: translateY(-50%);
            }
            .mobile { color: #294a79; font-size: 14px; font-weight: 700; }
            .top-row {
                display: grid;
                grid-template-columns: minmax(0, 431fr) minmax(0, 352fr) minmax(0, 404fr);
                align-items: start;
                gap: 22px;
                margin-bottom: 14px;
            }
            .box { min-width: 0; }
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
            .info-table, .grade-table, .marksheet table { width: 100%; border-collapse: collapse; }
            .info-table { color: #17345f; font-size: 13px; }
            .info-table td {
                height: 42px;
                border: 1px solid #8ba0bb;
                padding: 7px 13px;
                vertical-align: middle;
            }
            .info-table td.label {
                color: #102f63;
                font-weight: 800;
                white-space: nowrap;
            }
            .student-info-box .info-table td.label { width: 49.5%; }
            .academic-info-box .info-table td.label { width: 45%; }
            th:first-child, th:last-child {
                text-align: left !important;
            }
            .grade-table { color: #17345f; font-size: 11px; text-align: center; }
            .grade-table th,
            .grade-table td {
                height: 20px !important;
                border: 1px solid #8ba0bb;
                padding: 0 6px !important;
                line-height: 20px;
                text-align: center !important;
                vertical-align: middle;
            }
            .grade-table tbody tr {
                height: 18px !important;
                line-height: 18px !important;
            }

            .grade-table tbody td {
                height: 20px !important;
                min-height: 20px !important;
                max-height: 20px !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                line-height: 20px !important;
            }
            .grade-table th {
                height: 18px !important;
                min-height: 18px !important;
                max-height: 18px !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                line-height: 18px !important;
                background: #f4f7fb;
                color: #102f63;
                font-size: 11px;
                font-weight: 800;
            }
            .marksheet { font-size: 10px; }
            .marksheet th, .marksheet td { border: 1px solid #5B2C8F; padding: 3px 4px; }
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
            .transcript-summary-cards {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
                margin: 14px 0 18px;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .transcript-summary-card {
                min-height: 142px;
                border: 1px solid #c8d3e0;
                background: #fff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 12px 8px 10px;
                text-align: center;
                box-shadow: 0 1px 2px rgba(23, 52, 95, 0.08);
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .transcript-summary-card .card-label {
                margin-bottom: 9px;
                color: #17345f;
                font-size: 10px;
                font-weight: 800;
                text-transform: uppercase;
            }
            .transcript-summary-card .card-value {
                color: #17345f;
                font-size: 24px;
                font-weight: 800;
                line-height: 1;
            }
            .transcript-summary-card .card-note {
                margin-top: 8px;
                color: #263b5e;
                font-size: 9px;
                font-weight: 600;
                line-height: 1.3;
            }
            .transcript-summary-card .card-icon {
                width: 64px;
                height: 64px;
                margin-bottom: 1px;
                border: 3px solid #30a765;
                border-radius: 9999px;
                color: #087b3f;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #fff;
                font-size: 20px;
                font-weight: 800;
            }
            .transcript-summary-card.attendance .card-icon {
                border-color: #ef5555;
                color: #dc2626;
            }
            .transcript-summary-card.working-days .card-icon {
                width: 54px;
                height: 54px;
                border: 0;
                border-radius: 3px;
                background: #d8ebfb;
                color: #0877b9;
                font-size: 28px;
            }
            .transcript-evaluation-cards {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
                margin: 0 0 18px;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .transcript-evaluation-card {
                min-height: 142px;
                overflow: hidden;
                border: 1px solid #c8d3e0;
                background: #fff;
                box-shadow: 0 1px 2px rgba(23, 52, 95, 0.08);
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .transcript-evaluation-card .evaluation-heading {
                min-height: 26px;
                padding: 6px 7px;
                color: #fff;
                font-size: 9px;
                font-weight: 800;
                letter-spacing: 0;
                text-align: center;
                text-transform: uppercase;
            }
            .transcript-evaluation-card.behavior .evaluation-heading { background: #08724f; }
            .transcript-evaluation-card.activities .evaluation-heading { background: #0b3478; }
            .transcript-evaluation-card.failed .evaluation-heading { background: #d51f2a; }
            .transcript-evaluation-card.verify .evaluation-heading { background: #0b3478; }
            .evaluation-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                min-height: 28px;
                padding: 4px 9px;
                border-bottom: 1px solid #dbe3ed;
                color: #17345f;
                font-size: 10px;
                font-weight: 600;
            }
            .evaluation-row:last-child { border-bottom: 0; }
            .evaluation-stars { color: #087b62; font-size: 12px; letter-spacing: 1px; white-space: nowrap; }
            .evaluation-stars .muted { color: #cbd5e1; }
            .evaluation-qr {
                display: flex;
                min-height: 116px;
                align-items: center;
                justify-content: center;
                padding: 8px;
            }
            .evaluation-qr .qr-image {
                width: 88px;
                height: 88px;
                object-fit: contain;
            }
            .failed-summary {
                display: flex;
                min-height: 116px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 11px 10px;
                text-align: center;
            }
            .failed-summary .failed-count {
                color: #17345f;
                font-size: 16px;
                font-weight: 800;
                text-transform: uppercase;
            }
            .failed-summary .failed-list {
                color: #263b5e;
                font-size: 10px;
                font-weight: 600;
                line-height: 1.35;
            }
            @media (max-width: 760px) {
                .transcript-summary-cards {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
                .transcript-evaluation-cards {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media print {
                .transcript-summary-cards { grid-template-columns: repeat(4, minmax(0, 1fr)); }
                .transcript-summary-card { min-height: 112px; }
                .transcript-evaluation-cards { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            }
            .footer {
                position: absolute;
                left: 40px;
                right: 40px;
                bottom: 34px;
                display: flex;
                justify-content: flex-end;
                align-items: flex-end;
                z-index: 3;
            }
            .footer-wave {
                position: absolute;
                right: 0;
                bottom: 0;
                left: 0;
                height: 58px;
                overflow: hidden;
                pointer-events: none;
                z-index: 1;
            }
            .footer-wave::before,
            .footer-wave::after {
                content: '';
                position: absolute;
                border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            }
            .footer-wave::before {
                bottom: -35px;
                left: -70px;
                width: 850px;
                height: 78px;
                background: #8ebcf2;
            }
            .footer-wave::after {
                bottom: -40px;
                left: -82px;
                width: 825px;
                height: 74px;
                background: #2d6fd4;
            }
            .footer-wave span {
                position: absolute;
                right: 0;
                bottom: 0;
                left: 0;
                height: 6px;
                background: #0b2f65;
                z-index: 2;
            }
            .signature-frame {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 2px;
            }
            .signature-frame img {
                max-height: 48px;
                max-width: 160px;
                object-fit: contain;
            }
            .sig-line {
                min-width: 168px;
                border-top: 1px solid #17345f;
                margin-top: 2px;
                padding-top: 4px;
                color: #17345f;
                font-weight: 800;
                font-size: 10px;
                letter-spacing: 0;
                text-align: center;
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
                border: 1px solid #9fb2c9;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                line-height: 1.2;
                color: #172b4d;
            }
            .reference-result-table th,
            .reference-result-table td {
                height: 30px;
                border: 1px solid #a9b9cc;
                padding: 5px 7px;
                text-align: center;
                vertical-align: middle;
            }
            .reference-result-table thead tr:first-child th {
                height: 40px;
                background: #073b78;
                color: #fff;
                font-size: 11px;
                font-weight: 800;
                line-height: 1.25;
            }
            .reference-result-table thead .component-heading {
                height: 28px;
                background: #4f7dab;
                color: #fff;
                font-weight: 800;
            }
            .reference-result-table .subject-cell {
                padding-left: 12px;
                text-align: left;
                font-weight: 600;
            }
            .reference-result-table tbody tr:nth-child(even) td {
                background: #f8fafc;
            }
            .reference-result-table tbody tr.blank-subject-row td {
                border-color: transparent;
                background: transparent;
            }
            .reference-result-table tbody tr.blank-subject-row:last-child td {
                border-bottom-color: #9fb2cc;
            }
            .reference-result-table tfoot td {
                height: 32px;
                background: #e8eef6;
                color: #17345f;
                font-weight: 800;
            }
            .reference-result-table tfoot .exam-total-label {
                background: #dce6f2;
                color: #17345f;
                text-align: center;
            }
            .reference-result-table .subject-column {
                width: 26%;
                padding-left: 12px;
                text-align: left;
            }
            .reference-result-table .mark-column { width: 7.5%; }
            .reference-result-table .component-column { width: 7%; }
            .reference-result-table .result-column { width: 8%; }
            @media print { body { background: #fff; padding: 0; } .transcript-page { box-shadow: none; border: none; width: auto; } }
        </style>

        <div class="transcript-page">
            ${data.school_info.logo ? `<img src="${data.school_info.logo}" class="watermark" alt="Watermark"/>` : ''}
            <div class="transcript-header">
                <div class="school-logo-frame">
                    ${data.school_info.logo ? `<img src="${escapeResultHtml(data.school_info.logo)}" alt="${escapeResultHtml(data.school_info.school_name)} logo">` : '<span class="student-photo-fallback">No logo</span>'}
                </div>
                <div class="header">
                    <div class="school-name">${escapeResultHtml(data.school_info.school_name)}</div>
                    <div class="address">${escapeResultHtml(address)}</div>
                    <div class="mobile">
                        ${data.school_info?.mobile ? escapeResultHtml(data.school_info.mobile) : ''}${data.school_info?.mobile && data.school_info?.email ? ' | ' : ''}${data.school_info?.email ? escapeResultHtml(data.school_info.email) : ''}
                    </div>
                    <div class="transcript-title"><span>Academic Transcript</span></div>
                </div>
                <div class="student-photo-frame">
                    ${data.student_image ? `<img src="${escapeResultHtml(data.student_image)}" alt="${escapeResultHtml(data.student_name)} photo">` : '<span class="student-photo-fallback">No photo</span>'}
                </div>
            </div>
            <div class="exam-meta">
                <div><strong>Exam Title:</strong><span>Academic Transcript</span></div>
                <div><strong>Exam Name:</strong><span>${escapeResultHtml(data.exam_name || 'N/A')}</span></div>
                <div><strong>Published:</strong><span>${escapeResultHtml(formattedDate)}</span></div>
            </div>

            <div class="top-row">
                <div class="box student-info-box">
                    <table class="info-table">
                        <tr><td class="label">Student ID</td><td>${data.student_id_number}</td></tr>
                        <tr><td class="label">Student Name</td><td>${data.student_name}</td></tr>
                        <tr><td class="label">Father's Name</td><td>${data.father_name || 'N/A'}</td></tr>
                        <tr><td class="label">Mother's Name</td><td>${data.mother_name || 'N/A'}</td></tr>
                    </table>
                </div>

                <div class="box academic-info-box">
                    <table class="info-table">
                        <tr><td class="label">Class</td><td>${data.class_name || 'N/A'}</td></tr>
                        <tr><td class="label">Group</td><td>${data.group_name || 'N/A'}</td></tr>
                        <tr><td class="label">Section</td><td>${data.section_name || 'N/A'}</td></tr>
                        <tr><td class="label">Session Year</td><td>${data.session_name || 'N/A'}</td></tr>
                    </table>
                </div>


                <div class="box grade-info-box">
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
                        ${tableSubjects.map(subject => `
                                    <tr>
                                        <td class="subject-cell">${subject ? escapeResultHtml(subject.name || '-') : ''}</td>
                                        <td>${subject ? formatResultNumber(subject.full_mark ?? 0) : ''}</td>
                                        <td>${subject ? formatResultNumber(subject.highest_mark ?? subject.mark ?? 0) : ''}</td>
                                        <td>${subject ? formatResultComponent(subject.tutorial_mark) : ''}</td>
                                        <td>${subject ? formatResultComponent(subject.mcq_mark) : ''}</td>
                                        <td>${subject ? formatResultComponent(subject.writing_mark ?? subject.theory_mark) : ''}</td>
                                        <td>${subject ? formatResultComponent(subject.practical_mark) : ''}</td>
                                        <td>${subject ? formatResultNumber(subject.mark ?? 0) : ''}</td>
                                        <td>${subject ? escapeResultHtml(subject.grade ?? '-') : ''}</td>
                                        <td>${subject ? formatResultNumber(subject.point ?? 0) : ''}</td>
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
                <div class="reference-result-spacer" style="height:${missingSubjectRows * 30}px"></div>
            </div>

            <div class="transcript-summary-cards">
                <div class="transcript-summary-card">
                    <div class="card-label">GPA (Without 4th)</div>
                    <div class="card-icon">${formatResultNumber(data.gpa_without_fourth ?? data.gpa)}</div>
                    <div class="card-note">Grade Point Average</div>
                </div>
                <div class="transcript-summary-card">
                    <div class="card-label">Position</div>
                    <div class="card-icon">${escapeResultHtml(data.position ?? 'N/A')}</div>
                    <div class="card-note">Out of ${formatResultNumber(data.position_total)} in Section</div>
                </div>
                <div class="transcript-summary-card attendance">
                    <div class="card-label">Attendance</div>
                    <div class="card-icon">${formatResultNumber(data.attendance?.percentage)}%</div>
                    <div class="card-note">Present: ${formatResultNumber(data.attendance?.present_days)} Days<br>Absent: ${formatResultNumber(data.attendance?.absent_days)} Days</div>
                </div>
                <div class="transcript-summary-card working-days">
                    <div class="card-label">Working Days</div>
                    <div class="card-icon"><i class="far fa-calendar-alt" aria-hidden="true"></i></div>
                    <div class="card-value">${formatResultNumber(data.attendance?.working_days)}</div>
                    <div class="card-note">Class days excluding holidays</div>
                </div>
            </div>

            <div class="transcript-evaluation-cards">
                <div class="transcript-evaluation-card behavior">
                    <div class="evaluation-heading">Moral &amp; Behavior Evaluation</div>
                    ${[
                        ['Excellent', 5],
                        ['Good', 4],
                        ['Average', 3],
                        ['Poor', 2],
                    ].map(([label, rating]) => `<div class="evaluation-row"><span>${label}</span><span class="evaluation-stars">${'★'.repeat(rating)}<span class="muted">${'★'.repeat(5 - rating)}</span></span></div>`).join('')}
                </div>
                <div class="transcript-evaluation-card activities">
                    <div class="evaluation-heading">Co-curricular Activities</div>
                    ${[
                        ['Sports', 5],
                        ['Cultural Function', 4],
                        ['Scout/BNCC', 3],
                        ['Math Olympiad', 3],
                    ].map(([label, rating]) => `<div class="evaluation-row"><span>${label}</span><span class="evaluation-stars">${'★'.repeat(rating)}<span class="muted">${'★'.repeat(5 - rating)}</span></span></div>`).join('')}
                </div>
                <div class="transcript-evaluation-card verify">
                    <div class="evaluation-heading">Verify Result</div>
                    <div class="evaluation-qr">
                        ${data.qr_code ? `<img class="qr-image" src="${escapeResultHtml(data.qr_code)}" alt="Scan to verify result">` : '<div class="card-note">QR unavailable</div>'}
                    </div>
                </div>
                <div class="transcript-evaluation-card failed">
                    <div class="evaluation-heading">Failed Subject(s)</div>
                    ${(() => {
                        const failedSubjects = (data.subjects || []).filter(subject => Number(subject.mark ?? 0) < Number(subject.fail_mark ?? 0));
                        return `<div class="failed-summary"><div class="failed-count">${failedSubjects.length ? `${failedSubjects.length} Subject${failedSubjects.length === 1 ? '' : 's'} Failed` : 'No Subject Failed'}</div><div class="failed-list">${failedSubjects.length ? failedSubjects.map(subject => `${escapeResultHtml(subject.name)} (${formatResultNumber(subject.mark)})`).join(', ') : 'All subjects passed'}</div></div>`;
                    })()}
                </div>
            </div>

            <div class="footer-wave" aria-hidden="true"><span></span></div>
            <div class="footer">
                <div class="sig-block">
                    <div class="signature-frame">
                        ${data.school_info.principal_signature ? `<img src="${data.school_info.principal_signature}" alt="Principal Signature"/>` : ''}
                    </div>
                    <div class="sig-line">Principal Signature</div>
                </div>
            </div>
        </div>`;
                if (pdfResultData) {
                    document.body.replaceChildren(container);
                    document.body.style.margin = '0';
                    document.body.style.background = '#ffffff';
                }

                container.dataset.pdfReady = 'true';
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
                .class-result-table-frame,
                .class-result-table-frame .school-data-table-scroll {
                    overflow: visible !important;
                    border: 0 !important;
                }
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
                position: relative;
            }

            .class-result-watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                z-index: 0;
                width: min(36%, 360px);
                transform: translate(-50%, -50%);
                opacity: 0.055;
                pointer-events: none;
                object-fit: contain;
            }

            .a4-landscape-print > *:not(.class-result-watermark) {
                position: relative;
                z-index: 1;
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
                width: 100%;
                min-width: 1100px;
                border: 0 !important;
                border-collapse: collapse;
                table-layout: fixed;
                margin: 0;
            }
            #mainResultTable td {
                height: 32px;
                font-size: 12px;
                padding: 8px 12px;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
            }
            #mainResultTable th { height: 32px; font-size: 12px; font-weight: 600; padding: 8px 12px; background: #f3f4f6; }
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
            ${data.school_logo ? `<img src="${escapeResultHtml(data.school_logo)}" class="class-result-watermark" alt="School logo watermark">` : ''}
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

            <div class="school-data-table-frame class-result-table-frame border border-gray-200 bg-white p-2.5 shadow-md sm:p-4">
                <div class="school-data-table-scroll">
                <table id="mainResultTable" class="school-data-table border-collapse border border-gray-300 text-xs">
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
                                            <tr class="transition-colors hover:bg-gray-50">
                                                <td class="sticky-column serial-column h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${i + 1}</td>
                                                <td class="sticky-column id-column h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll font-mono" title="${escapeResultHtml(std.student_id)}">${escapeResultHtml(std.student_id)}</div></td>
                                                <td class="sticky-column name-column h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll student-name-cell font-bold" title="${escapeResultHtml(toTitleCase(std.name))}">${escapeResultHtml(toTitleCase(std.name))}</div></td>
                                                ${data.subjects_list.map(sub => `<td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${formatResultNumber(std.marks[sub] ?? 0)}</td>`).join('')}
                                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-bold">${formatResultNumber(std.total)}</td>
                                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-black text-blue-800">${formatResultNumber(std.gpa)}</td>
                                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-bold">${escapeResultHtml(std.grade)}</td>
                                                <td class="no-print h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                                    <button type="button" title="Delete result" aria-label="Delete result" onclick="confirmDelete(${Number(std.id)})" class="mx-auto flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1">
                                                        <i class="far fa-trash-alt" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>`).join('')}
                    </tbody>
                </table>
                </div>
            </div>
        </div>`;
            }

            function exportToExcel() {
                if (typeof XLSX === 'undefined') {
                    Swal.fire('Export failed', 'The Excel export library could not be loaded. Please refresh and try again.',
                        'error');
                    return;
                }

                if (currentResultData?.subjects?.length) {
                    const data = currentResultData;
                    const rows = [
                        ['Academic Result'],
                        ['Student', data.student_name || 'N/A', 'Student ID', data.student_id_number || 'N/A'],
                        ['Admit Card', data.admit_card_number || 'N/A', 'Exam', data.exam_name || 'N/A'],
                        ['Class', data.class_name || 'N/A', 'Session', data.session_name || 'N/A'],
                        [],
                        ['Subject', 'Full Mark', 'Highest Mark', 'Tutorial', 'MCQ', 'Writing', 'Practical', 'Mark', 'Grade',
                            'Point'
                        ],
                        ...data.subjects.map(subject => [
                            subject.name || 'N/A',
                            subject.full_mark ?? 0,
                            subject.highest_mark ?? subject.mark ?? 0,
                            subject.tutorial_mark ?? 0,
                            subject.mcq_mark ?? 0,
                            subject.writing_mark ?? subject.theory_mark ?? 0,
                            subject.practical_mark ?? 0,
                            subject.mark ?? 0,
                            subject.grade || '-',
                            subject.point ?? 0,
                        ]),
                        [],
                        ['Total Marks', data.total_marks ?? 0, 'GPA', data.gpa ?? '0.00', 'Grade', data.grade || 'N/A',
                            'Position', data.position || 'N/A'
                        ],
                    ];
                    const worksheet = XLSX.utils.aoa_to_sheet(rows);
                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, 'Academic Result');
                    XLSX.writeFile(workbook, `academic-result-${data.admit_card_number || 'report'}.xlsx`);
                    return;
                }

                const table = document.getElementById('mainResultTable');
                if (!table || table.rows.length <= 1) {
                    Swal.fire('Find a result first', 'Generate an academic result before exporting it.', 'info');
                    return;
                }
                const wb = XLSX.utils.table_to_book(table, {
                    sheet: "Results"
                });
                XLSX.writeFile(wb, "Exam_Results.xlsx");
            }

            function printResult() {
                const resultContainer = document.getElementById('resultContainer');

                if (!currentResultData || !resultContainer?.querySelector('.transcript-page')) {
                    Swal.fire('Find a result first', 'Generate an academic result before printing it.', 'info');
                    return;
                }

                const printWindow = window.open('', '_blank', 'width=1200,height=900');

                if (!printWindow) {
                    Swal.fire('Print blocked', 'Allow pop-ups for this site, then try printing again.', 'warning');
                    return;
                }

                const styles = Array.from(document.querySelectorAll('style'))
                    .map(style => style.outerHTML)
                    .join('');

                printWindow.document.write(`
            <!doctype html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Academic Result</title>
                    ${styles}
                    <style>
                        body { margin: 0; background: #fff; }
                        .no-print { display: none !important; }
                    </style>
                <\/head>
                <body>${resultContainer.innerHTML}<\/body>
            </html>
        `);
                printWindow.document.close();

                printWindow.addEventListener('load', () => {
                    printWindow.focus();
                    printWindow.print();
                    printWindow.addEventListener('afterprint', () => printWindow.close(), {
                        once: true
                    });
                }, {
                    once: true
                });
            }

            async function exportResultPdf() {
                const admitNumber = document.getElementById('s_admit_no')?.value?.trim();

                if (!admitNumber || !currentResultData) {
                    Swal.fire('Find a result first', 'Enter an admit card number before exporting the PDF.', 'info');
                    return;
                }

                try {
                    Swal.fire({
                        title: 'Generating PDF',
                        text: 'Preparing the academic transcript...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    const response = await axios.post('/api/school-find-results/export-pdf', {
                        mode: 'single',
                        admit_no: admitNumber,
                    }, {
                        responseType: 'blob',
                    });

                    const blob = new Blob([response.data], { type: 'application/pdf' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `academic-result-${admitNumber}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    URL.revokeObjectURL(url);
                    Swal.close();
                } catch (error) {
                    let message = 'Unable to export the academic result.';

                    if (error.response?.data instanceof Blob) {
                        try {
                            const payload = JSON.parse(await error.response.data.text());
                            message = payload.message || message;
                        } catch (_) {
                            // Keep the generic message for non-JSON server responses.
                        }
                    } else {
                        message = error.response?.data?.message || error?.message || message;
                    }

                    Swal.fire('PDF export failed', message, 'error');
                }
            }

            function initResultExportDropdown() {
                const button = document.getElementById('btnResultExport');
                const menu = document.getElementById('resultExportDropdown');
                if (!button || !menu || button.dataset.dropdownReady === 'true') return;

                button.dataset.dropdownReady = 'true';

                const closeMenu = () => {
                    menu.classList.add('hidden');
                    button.setAttribute('aria-expanded', 'false');
                };

                button.addEventListener('click', event => {
                    event.stopPropagation();
                    const open = menu.classList.contains('hidden');

                    menu.classList.toggle('hidden', !open);
                    button.setAttribute('aria-expanded', String(open));
                });

                menu.addEventListener('click', closeMenu);
                document.addEventListener('click', event => {
                    if (!menu.contains(event.target) && event.target !== button) closeMenu();
                });
                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape') closeMenu();
                });
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

            window.openSearchModal = openSearchModal;
            window.closeSearchModal = closeSearchModal;
            window.executeFind = executeFind;
            window.exportToExcel = exportToExcel;
            window.printResult = printResult;
            window.exportResultPdf = exportResultPdf;
            window.confirmDelete = confirmDelete;

            initResultExportDropdown();

            if (pdfResultData) {
                renderSingleResult(pdfResultData);
            }
        </script>
    @endpush
@endsection
