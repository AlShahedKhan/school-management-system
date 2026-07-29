@extends('layouts.school')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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

    .pagination-btn {
        height: 26px !important;
        min-width: 26px !important;
        padding: 0 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        color: #64748b !important;
        border-radius: 0 !important;
        text-transform: uppercase !important;
        transition: all 0.1s ease !important;
    }

    .pagination-btn.active {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
    }

    .pagination-btn:hover:not(:disabled):not(.active) {
        border-color: #2563eb !important;
        color: #2563eb !important;
    }

    .pagination-btn:disabled {
        opacity: 0.3 !important;
        cursor: not-allowed !important;
        background: #f1f5f9 !important;
    }

    #paginationInfo {
        font-size: 9px !important;
        font-weight: 800 !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }

    .step-hidden {
        display: none;
    }

    .mark-entry-table th,
    .mark-entry-table td {
        height: 32px;
        border: 1px solid #d1d5db;
        padding: 0 12px;
        white-space: nowrap;
        text-align: left;
        vertical-align: middle;
    }

    .mark-entry-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 10px;
        font-weight: 600;
    }

    .mark-entry-table td {
        color: #4b5563;
        font-size: 11px;
    }

    .mark-entry-input {
        height: 28px;
        width: 100%;
        border: 1px solid #cbd5e1;
        background: #fff;
        padding: 0 8px;
        font-size: 11px;
        outline: none;
    }

    .mark-entry-input:focus {
        border-color: #2563eb;
    }
</style>

<div class="main-view-container">
    <div class="max-w-full mx-auto w-full">
        <x-school.list-header title="Mark Submit" breadcrumb-current="Mark Submit" keep-title>
            <x-slot:search>
                <form class="flex items-center gap-2" onsubmit="event.preventDefault(); fetchTable(1);">
                    <x-input.search
                        id="header_search"
                        placeholder="Student ID/Name..."
                        class="w-72"
                        oninput="document.getElementById('header_search_mobile').value = this.value"
                    />
                    <x-button.secondary type="button" onclick="restoreMarkSearch()">
                        Restore
                    </x-button.secondary>
                </form>
            </x-slot:search>

            <x-slot:actions>
                <x-button.secondary type="button" onclick="toggleFilterModal()" class="w-full">
                    Filter
                </x-button.secondary>

                <x-dropdown button-id="btnMarkExport" menu-id="markExportDropdown" label="Export" align="full">
                    <x-dropdown.item onclick="exportData('pdf')">PDF</x-dropdown.item>
                    <x-dropdown.item onclick="exportData('excel')">Excel</x-dropdown.item>
                    <x-dropdown.item onclick="window.print()">Print</x-dropdown.item>
                </x-dropdown>

                <x-button.primary type="button" onclick="openMarkModal()" class="w-full">
                    Add Mark
                </x-button.primary>
            </x-slot:actions>

            <x-slot:mobile-search>
                <form class="col-span-3 grid grid-cols-3 gap-2" onsubmit="event.preventDefault(); fetchTable(1);">
                    <x-input.search
                        id="header_search_mobile"
                        placeholder="Student ID/Name..."
                        class="col-span-2 min-w-0"
                        oninput="document.getElementById('header_search').value = this.value"
                    />
                    <x-button.secondary type="button" onclick="restoreMarkSearch()" class="w-full">
                        Restore
                    </x-button.secondary>
                </form>
            </x-slot:mobile-search>
        </x-school.list-header>

        {{-- Filter Modal --}}
        <x-modal.form
            id="filterModal"
            form-id="markFilterForm"
            title="Mark Filter"
            close-button-id="closeMarkFilterModal"
            title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
            onsubmit="event.preventDefault(); applyFilters();"
        >
            <div class="relative">
                <x-input.dropdown-select id="f_class" placeholder="Select Class" :options="[]" />
                <x-input.floating-label for="f_class" :floating="false">Class</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="f_exam" placeholder="Select Exam" :options="[]" />
                <x-input.floating-label for="f_exam" :floating="false">Exam</x-input.floating-label>
            </div>

            <div class="relative md:col-span-2">
                <x-input.dropdown-select id="f_subject" placeholder="Select Subject" :options="[]" />
                <x-input.floating-label for="f_subject" :floating="false">Subject</x-input.floating-label>
            </div>

            <x-slot:footer>
                <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                    <x-button.secondary type="button" onclick="resetFilters()" class="w-full">Reset</x-button.secondary>
                    <x-button.primary type="submit" class="w-full">Apply</x-button.primary>
                </div>
            </x-slot:footer>
        </x-modal.form>

        <x-school.data-table
            :empty="false"
            :empty-colspan="10"
            empty-message="No marks found."
            show-footer="true"
            min-width="1068px"
            tbody-id="markTableBody"
        >
            <x-slot:columns>
                <colgroup>
                    <col style="width:45px;">
                    <col style="width:90px;">
                    <col style="width:130px;">
                    <col style="width:125px;">
                    <col style="width:145px;">
                    <col style="width:170px;">
                    <col style="width:85px;">
                    <col style="width:80px;">
                    <col style="width:70px;">
                    <col style="width:100px;">
                </colgroup>
            </x-slot:columns>

            <x-slot:head>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Sl</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Subject</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Exam</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student ID</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Name</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Mark</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Grade</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Point</x-table.th>
                <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
            </x-slot:head>

            <x-slot:footer>
                <div class="flex items-center justify-between px-3" id="paginationArea">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500" id="paginationInfo">0 of 0</div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </x-slot:footer>
        </x-school.data-table>
    </div>
</div>

{{-- Mark Submit Modal --}}
<x-modal.form
    id="markModal"
    form-id="markForm"
    title="Exam Configuration"
    close-button-id="closeMarkModalButton"
    panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[720px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
    panel-style="border-radius:4px; max-height:min(620px, calc(100dvh - 2.5rem));"
    title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    fields-class="block"
    onsubmit="event.preventDefault();"
>
    <input type="hidden" id="edit_id">
    <input type="hidden" id="action_type" value="exam configure" name="action_type">

    <div id="step1">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <div class="relative">
                <x-input.dropdown-select id="m_class" placeholder="Select Class" :options="[]" />
                <x-input.floating-label for="m_class" :floating="false">Class</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="m_group" placeholder="Select Group" :options="[]" />
                <x-input.floating-label for="m_group" :floating="false">Group</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="m_section" placeholder="Select Section" :options="[]" />
                <x-input.floating-label for="m_section" :floating="false">Section</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="m_session" placeholder="Select Session" :options="[]" />
                <x-input.floating-label for="m_session" :floating="false">Session</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="m_exam" placeholder="Select Exam" :options="[]" />
                <x-input.floating-label for="m_exam" :floating="false">Exam Name</x-input.floating-label>
            </div>

            <div class="relative">
                <x-input.dropdown-select id="m_subject" placeholder="Select Subject" :options="[]" />
                <x-input.floating-label for="m_subject" :floating="false">Subject</x-input.floating-label>
            </div>
        </div>
    </div>

    <div id="step2" class="step-hidden">
        <div class="max-h-[360px] overflow-auto border border-gray-200 bg-white">
            <table class="mark-entry-table w-full min-w-[600px] border-collapse text-left">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th style="width:120px;">Mark</th>
                        <th>Grade</th>
                        <th>Point</th>
                    </tr>
                </thead>
                <tbody id="studentMarkList"></tbody>
            </table>
        </div>
    </div>

    <x-slot:footer>
        <div id="step1Actions" class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
            <x-button.secondary type="button" onclick="closeMarkModal()" class="w-full">Cancel</x-button.secondary>
            <x-button.primary type="button" id="nextBtn" onclick="goToStep2()" class="w-full">Next</x-button.primary>
        </div>

        <div id="step2Actions" class="hidden grid grid-cols-3 gap-3 bg-white px-6 pb-4 pt-3">
            <x-button.secondary type="button" id="backBtn" onclick="goToStep1()" class="w-full">Back</x-button.secondary>
            <x-button.secondary type="button" onclick="submitMarks('draft')" class="w-full text-blue-600">Draft</x-button.secondary>
            <x-button.primary type="button" onclick="submitMarks('published')" class="w-full">Submit</x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

<script>
    let gradingSystem = [];
    let studentsForEntry = [];
    let isEditMode = false;
    let currentPage = 1;
    let selectedSubjectType = 1;

    window.addEventListener('load', () => {
        initializeMarkDropdownEvents();
        loadInitialDropdowns();
        fetchGradingRules();
        fetchTable();
    });

    function markDropdownParts(id) {
        const input = document.getElementById(id);
        const root = input?.closest('[data-dropdown-select]');

        return {
            input,
            root,
            label: root?.querySelector('[data-dropdown-select-label]'),
            menu: root?.querySelector('[data-dropdown-select-menu]'),
        };
    }

    function selectedMarkDropdownOption(id) {
        const { input, menu } = markDropdownParts(id);

        return Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(input?.value || ''));
    }

    function selectedMarkDropdownId(id) {
        return selectedMarkDropdownOption(id)?.dataset.optionId || '';
    }

    function setMarkDropdownValue(id, value = '', label = null, shouldNotify = false) {
        const parts = markDropdownParts(id);
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

    function markDropdownPlaceholder(id, field) {
        const configured = markDropdownParts(id).label?.dataset.placeholder;
        return configured || `Select ${field.split('_')[0]}`;
    }

    function fillOptions(id, data, field, filterId = null) {
        const parts = markDropdownParts(id);
        if (!parts.menu) return;

        parts.menu.innerHTML = '';

        (data || []).forEach(item => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className =
                'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
            option.dataset.value = String(item[field] ?? '');
            option.dataset.optionId = String(item.id ?? '');
            option.setAttribute('data-dropdown-select-option', '');
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');
            option.textContent = item[field] ?? '';

            if (item.subject_type) option.dataset.type = String(item.subject_type);
            if (item.grade_id) option.dataset.gradeId = String(item.grade_id);
            if (item.marks) {
                option.dataset.marks = typeof item.marks === 'object'
                    ? JSON.stringify(item.marks)
                    : String(item.marks);
            }

            option.addEventListener('click', () => {
                setMarkDropdownValue(id, option.dataset.value, option.textContent.trim());
                parts.menu.classList.add('hidden');
                parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                parts.input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            parts.menu.appendChild(option);
        });

        setMarkDropdownValue(id, '', markDropdownPlaceholder(id, field));

        if (filterId) {
            fillOptions(filterId, data, field);
        }
    }

    function initializeMarkDropdownEvents() {
        const cascades = {
            m_class: 'm_group',
            m_group: 'm_section',
            m_section: 'm_session',
        };

        Object.entries(cascades).forEach(([id, next]) => {
            document.getElementById(id)?.addEventListener('change', event => {
                handleCascade(event.currentTarget, next);
            });
        });

        document.getElementById('f_class')?.addEventListener('change', event => {
            const classId = selectedMarkDropdownId('f_class');
            refreshSubjects(classId);
            refreshExams(event.currentTarget.value);
        });
    }

    function loadInitialDropdowns() {
        axios.get('/api/get-school-classes').then(res => {
            fillOptions('m_class', res.data.data, 'class_name', 'f_class');
        });
        axios.get('/api/get-school-exams').then(res => fillOptions('m_exam', res.data.data, 'exam_name', 'f_exam'));
        axios.get('/api/get-school-subjects').then(res => fillOptions('m_subject', res.data.data, 'subject_name',
            'f_subject'));
    }

    function fetchGradingRules() {
        axios.get('/api/get-grading-systems').then(res => {
            // We'll fetch the detailed grades separately or just fetch all
            axios.get('/api/school-exam-grades?all=1').then(res => {
                gradingSystem = res.data.data;
                console.log("Grade system loaded", gradingSystem);
            });
        });
    }

    async function handleCascade(el, next) {
        const id = selectedMarkDropdownId(el.id);
        const val = el.value;

        if (!id && !val) return;

        let url = '';
        let fld = '';
        let params = {};

        // Cascade Logic based on your Controller
        if (next.includes('group')) {
            url = `/api/get-school-groups`;
            params = {
                class_id: id
            };
            fld = 'group_name';

            // Also refresh Subjects and Exams for this Class immediately
            await Promise.all([
                refreshSubjects(id),
                refreshExams(val),
            ]);

        } else if (next.includes('section')) {
            url = `/api/get-school-sections`;
            params = {
                group_id: id
            };
            fld = 'section_name';

            // Refresh Subjects for this Group
            const classId = selectedMarkDropdownId('m_class');
            await refreshSubjects(classId, id);

        } else if (next.includes('session')) {
            const isModal = next.startsWith('m_');
            const classId = selectedMarkDropdownId(isModal ? 'm_class' : 'f_class');
            const groupId = selectedMarkDropdownId(isModal ? 'm_group' : 'f_group');

            url = `/api/get-school-sessions`;
            params = {
                section_id: id,
                class_id: classId,
                group_id: groupId
            };
            fld = 'session_year';
        }

        const res = await axios.get(url, {
            params
        });
        const tid = next.startsWith('m_') ? next : 'f_' + next.split('_')[1];
        fillOptions(tid, res.data.data, fld);
        return res.data.data;
    }

    // New specific refreshers for Subject and Exam based on selection
    function refreshSubjects(classId, groupId = null, sectionId = null) {
        return axios.get('/api/get-school-subjects', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId
            }
        }).then(res => fillOptions('m_subject', res.data.data, 'subject_name', 'f_subject'));
    }

    function refreshExams(className, sessionName = null) {
        return axios.get('/api/get-school-exams', {
            params: {
                class_name: className,
                session_name: sessionName
            }
        }).then(res => fillOptions('m_exam', res.data.data, 'exam_name', 'f_exam'));
    }

    function escapeMarkHtml(value) {
        return String(value ?? '-').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function markTableCell(value, alignment = 'text-left', extraClass = '') {
        const content = escapeMarkHtml(value);

        return `
            <td class="h-8 border border-gray-300 px-3 ${alignment}">
                <div class="school-data-table-cell-scroll ${extraClass}" title="${content}">${content}</div>
            </td>`;
    }

    function fetchTable(page = 1) {
        currentPage = page;
        const params = {
            page,
            class_name: document.getElementById('f_class').value,
            exam_name: document.getElementById('f_exam').value,
            subject_name: document.getElementById('f_subject').value,
            search: document.getElementById('header_search').value
        };
        axios.get('/api/school-exam-marks', {
            params
        }).then(res => {
            const body = document.getElementById('markTableBody');
            body.innerHTML = '';
            if (!res.data.data || res.data.data.length === 0) {
                body.innerHTML =
                    '<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No marks found.</td></tr>';
                renderPagination(res.data);
                return;
            }
            res.data.data.forEach((item, i) => {
                body.innerHTML += `<tr class="hover:bg-gray-50">
                    ${markTableCell(res.data.from + i, 'text-center')}
                    ${markTableCell(item.class_name)}
                    ${markTableCell(item.subject_name)}
                    ${markTableCell(item.exam_name)}
                    ${markTableCell(item.student_id_number, 'text-left', 'font-mono')}
                    ${markTableCell(item.student_name)}
                    ${markTableCell(item.mark)}
                    ${markTableCell(item.letter_name)}
                    ${markTableCell(item.point)}
                    <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                        <div class="mx-auto flex h-8 items-center justify-center space-x-1">
                            <button type="button" title="Edit mark" aria-label="Edit mark" onclick="editMark(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1">
                                <i class="far fa-edit text-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" title="Delete mark" aria-label="Delete mark" onclick="deleteMark(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1">
                                <i class="far fa-trash-alt text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            renderPagination(res.data);
        });
    }

    function renderPagination(meta) {
        const controls = document.getElementById('paginationControls');
        const info = document.getElementById('paginationInfo');
        if (!controls || !info) return;

        info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
        controls.innerHTML = '';
        const btnClass = "pagination-btn";

        controls.innerHTML += `
            <button class="${btnClass}" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchTable(${meta.current_page - 1})">
                <i class="mdi mdi-chevron-left"></i>
            </button>`;

        for (let i = 1; i <= meta.last_page; i++) {
            controls.innerHTML += `
                <button class="${btnClass} ${meta.current_page === i ? 'active' : ''}" onclick="fetchTable(${i})">
                    ${i}
                </button>`;
        }

        controls.innerHTML += `
            <button class="${btnClass}" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTable(${meta.current_page + 1})">
                <i class="mdi mdi-chevron-right"></i>
            </button>`;
    }

    function goToStep2() {
        const classEl = document.getElementById('m_class');
        const groupEl = document.getElementById('m_group');
        const sectionEl = document.getElementById('m_section');
        const sessionEl = document.getElementById('m_session');
        const actionType = document.getElementById('action_type').value;

        const classId = selectedMarkDropdownId('m_class');
        const groupId = selectedMarkDropdownId('m_group');
        const sectionId = selectedMarkDropdownId('m_section');
        const sessionId = selectedMarkDropdownId('m_session');

        const exm = document.getElementById('m_exam').value;
        const subEl = document.getElementById('m_subject');
        const sub = subEl.value;
        selectedSubjectType = selectedMarkDropdownOption('m_subject')?.dataset.type || 1;

        if (!classId || !sessionId || !exm || !sub) {
            return Swal.fire('Error', 'Please configure Class, Session, Exam and Subject', 'error');
        }

        if (isEditMode) {
            renderStudentRows();
            showStep(2);
            return;
        }

        // Fetch students using the IDs matching your Controller logic
        axios.get('/api/get-school-students', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId,
                session_id: sessionId,
                action_type: actionType
            }
        }).then(res => {
            if (res.data.status === 'no_admit_card') {
                return Swal.fire('Admit Card Missing', res.data.message, 'warning');
            }

            studentsForEntry = res.data.data;
            if (!studentsForEntry || studentsForEntry.length === 0) {
                return Swal.fire('No Students', 'No students found for this specific selection', 'info');
            }
            renderStudentRows();
            showStep(2);
        }).catch(err => {
            const msg = err.response?.data?.message || 'Failed to fetch students.';
            Swal.fire('Error', msg, 'error');
        });
    }

    function renderStudentRows() {
        const tbody = document.getElementById('studentMarkList');
        const thead = document.querySelector('#step2 thead tr');

        // Get full mark and mark distribution for placeholders
        const selectedSub = selectedMarkDropdownOption('m_subject');
        const gradeId = selectedSub?.dataset.gradeId;
        const referenceRule = gradingSystem.find(g => g.id == gradeId);
        const fullMark = referenceRule ? referenceRule.full_mark : 100;

        let theoryMax = '';
        let practicalMax = '';
        const marksDataAttr = selectedSub?.getAttribute('data-marks');
        if (marksDataAttr) {
            try {
                const marks = typeof marksDataAttr === 'string' ? JSON.parse(marksDataAttr) : marksDataAttr;
                theoryMax = marks.theory_marks || '';
                practicalMax = marks.practical_marks || '';
            } catch (e) {}
        }

        // Update Header based on subject type
        let headerHtml = `
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">ID</th>
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Name</th>`;

        if (selectedSubjectType == 3) {
            headerHtml += `
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Theory</th>
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Practical</th>
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Total Mark</th>`;
        } else {
            headerHtml += `<th width="120" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Mark</th>`;
        }

        headerHtml += `
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Grade</th>
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Point</th>`;

        thead.innerHTML = headerHtml;

        tbody.innerHTML = '';
        studentsForEntry.forEach((s, idx) => {

            let markInputs = '';
            if (selectedSubjectType == 3) {
                const theoryPlaceholder = theoryMax ? `0 - ${theoryMax}` : 'Min: 0';
                const practicalPlaceholder = practicalMax ? `0 - ${practicalMax}` : 'Min: 0';
                markInputs = `
                        <td><input type="number" placeholder="${theoryPlaceholder}" class="mark-entry-input theory-input" value="${s.theory_mark || ''}" onkeyup="calculateGrade(this, ${idx}, 'theory')" data-idx="${idx}"></td>
                        <td><input type="number" placeholder="${practicalPlaceholder}" class="mark-entry-input practical-input" value="${s.practical_mark || ''}" onkeyup="calculateGrade(this, ${idx}, 'practical')" data-idx="${idx}"></td>
                        <td><input type="number" placeholder="Total" class="mark-entry-input mark-input bg-gray-50" value="${s.mark || ''}" readonly id="total_mark_${idx}"></td>`;
            } else {
                const fullMarkPlaceholder = `0 - ${fullMark}`;
                markInputs = `<td><input type="number" placeholder="${fullMarkPlaceholder}" class="mark-entry-input mark-input" value="${s.mark || ''}" min="0" max="${fullMark}" onkeyup="calculateGrade(this, ${idx}, 'total')" data-idx="${idx}"></td>`;
            }

            tbody.innerHTML += `
            <tr>
                <td class="font-mono text-[10px]">${escapeMarkHtml(s.student_id_number)}</td>
                <td class="font-bold uppercase">${escapeMarkHtml(s.student_name)}</td>
                ${markInputs}
                <td id="grade_${idx}" class="font-black text-blue-600">${escapeMarkHtml(s.letter_name || '-')}</td>
                <td id="point_${idx}" class="font-bold">${escapeMarkHtml(s.point || '-')}</td>
            </tr>`;
        });
    }

    function showStep(step) {
        const title = document.getElementById('markModalTitle');
        const step1Actions = document.getElementById('step1Actions');
        const step2Actions = document.getElementById('step2Actions');

        if (step === 1) {
            document.getElementById('step2').classList.add('step-hidden');
            document.getElementById('step1').classList.remove('step-hidden');
            step1Actions.classList.remove('hidden');
            step1Actions.classList.add('grid');
            step2Actions.classList.add('hidden');
            step2Actions.classList.remove('grid');
            title.innerText = isEditMode ? 'Edit Mark Configuration' : 'Exam Configuration';
        } else {
            document.getElementById('step1').classList.add('step-hidden');
            document.getElementById('step2').classList.remove('step-hidden');
            step1Actions.classList.add('hidden');
            step1Actions.classList.remove('grid');
            step2Actions.classList.remove('hidden');
            step2Actions.classList.add('grid');
            title.innerText = isEditMode ? 'Update Mark' : 'Enter Student Marks';
        }
    }

    function goToStep1() {
        showStep(1);
    }

    function calculateGrade(input, idx, type) {
        let theory = 0;
        let practical = 0;
        let total = 0;

        const gradeId = selectedMarkDropdownOption('m_subject')?.dataset.gradeId;
        const referenceRule = gradingSystem.find(g => g.id == gradeId);
        const fullMark = referenceRule ? referenceRule.full_mark : null;

        if (selectedSubjectType == 3) {
            const tr = input.closest('tr');
            theory = parseFloat(tr.querySelector('.theory-input').value) || 0;
            practical = parseFloat(tr.querySelector('.practical-input').value) || 0;
            total = theory + practical;

            if (fullMark && total > fullMark) {
                Swal.fire('Invalid Mark', `Total mark (${total}) cannot exceed full marks (${fullMark})`, 'warning');
                input.value = '';
                total = 0; // Reset for calculation
            }

            studentsForEntry[idx].theory_mark = theory;
            studentsForEntry[idx].practical_mark = practical;
            studentsForEntry[idx].mark = total;
            document.getElementById(`total_mark_${idx}`).value = total;
        } else {
            total = parseFloat(input.value) || 0;

            if (fullMark && total > fullMark) {
                Swal.fire('Invalid Mark', `Mark cannot exceed full marks (${fullMark})`, 'warning');
                input.value = '';
                total = 0;
            }

            studentsForEntry[idx].mark = total;
        }

        if (total === 0 && input.value === '') {
            document.getElementById(`grade_${idx}`).innerText = '-';
            document.getElementById(`point_${idx}`).innerText = '-';
            return;
        }

        const res = gradingSystem.find(g =>
            (fullMark ? g.full_mark == fullMark : true) &&
            total >= g.mark_from && total <= g.mark_to
        );
        if (res) {
            document.getElementById(`grade_${idx}`).innerText = res.grade_name;
            document.getElementById(`point_${idx}`).innerText = res.grade_point;
            studentsForEntry[idx].letter_name = res.grade_name;
            studentsForEntry[idx].point = res.grade_point;
        } else {
            document.getElementById(`grade_${idx}`).innerText = '-';
            document.getElementById(`point_${idx}`).innerText = '-';
        }
    }

    function submitMarks(status) {
        const editId = document.getElementById('edit_id').value;
        const payload = {
            class_name: document.getElementById('m_class').value,
            group_name: document.getElementById('m_group').value,
            section_name: document.getElementById('m_section').value,
            session_name: document.getElementById('m_session').value,
            exam_name: document.getElementById('m_exam').value,
            subject_name: document.getElementById('m_subject').value,
            status: status,
            marks_data: studentsForEntry.map(s => ({
                student_id_number: s.student_id_number,
                student_name: s.student_name,
                roll_no: s.roll_no || null,
                mark: s.mark,
                theory_mark: s.theory_mark || 0,
                practical_mark: s.practical_mark || 0,
                letter_name: s.letter_name,
                point: s.point
            }))
        };

        const request = isEditMode ? axios.put(`/api/school-exam-marks/${editId}`, payload) : axios.post(
            '/api/school-exam-marks', payload);

        request.then(res => {
            Toastify({
                text: isEditMode ? "Mark Updated!" : "Marks Submitted!",
                style: {
                    background: "#10b981"
                }
            }).showToast();
            closeMarkModal();
            fetchTable(currentPage);
        }).catch(err => {
            if (err.response?.status === 422) {
                Swal.fire('Error', err.response.data.message, 'warning');
            }
        });
    }

    async function editMark(id) {
        isEditMode = true;
        try {
            const res = await axios.get(`/api/school-exam-marks/${id}`);
            const data = res.data;

            document.getElementById('edit_id').value = id;
            setMarkDropdownValue('m_class', data.class_name, data.class_name);

            await handleCascade(document.getElementById('m_class'), 'm_group');
            setMarkDropdownValue('m_group', data.group_name, data.group_name);

            await handleCascade(document.getElementById('m_group'), 'm_section');
            setMarkDropdownValue('m_section', data.section_name, data.section_name);

            await handleCascade(document.getElementById('m_section'), 'm_session');
            setMarkDropdownValue('m_session', data.session_name, data.session_name);

            setMarkDropdownValue('m_exam', data.exam_name, data.exam_name);
            setMarkDropdownValue('m_subject', data.subject_name, data.subject_name);

            studentsForEntry = [{
                student_id_number: data.student_id_number,
                student_name: data.student_name,
                roll_no: data.roll_no,
                mark: data.mark,
                theory_mark: data.theory_mark,
                practical_mark: data.practical_mark,
                letter_name: data.letter_name,
                point: data.point
            }];

            document.getElementById('markModal').classList.remove('hidden');
            showStep(1);
        } catch (error) {
            Swal.fire('Error', 'Failed to fetch details', 'error');
        }
    }

    function deleteMark(id) {
        Swal.fire({
            title: 'Delete Entry?',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then(r => {
            if (r.isConfirmed) axios.delete(`/api/school-exam-marks/${id}`).then(() => fetchTable(currentPage));
        });
    }

    function openMarkModal() {
        isEditMode = false;
        document.getElementById('edit_id').value = '';
        studentsForEntry = [];
        ['m_class', 'm_group', 'm_section', 'm_session', 'm_exam', 'm_subject'].forEach(id => {
            setMarkDropdownValue(id, '');
        });
        document.getElementById('markModal').classList.remove('hidden');
        showStep(1);
    }

    function closeMarkModal() {
        document.getElementById('markModal').classList.add('hidden');
    }

    function toggleFilterModal() {
        document.getElementById('filterModal').classList.toggle('hidden');
    }

    function applyFilters() {
        fetchTable(1);
        toggleFilterModal();
    }

    function restoreMarkSearch() {
        const desktopSearch = document.getElementById('header_search');
        const mobileSearch = document.getElementById('header_search_mobile');

        if (desktopSearch) desktopSearch.value = '';
        if (mobileSearch) mobileSearch.value = '';

        fetchTable(1);
    }

    function resetFilters() {
        ['f_class', 'f_exam', 'f_subject'].forEach(id => setMarkDropdownValue(id, ''));
        restoreMarkSearch();
        toggleFilterModal();
    }
</script>
@endsection
