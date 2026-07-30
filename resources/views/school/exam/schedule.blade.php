@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

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
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <x-school.list-header title="Exam Schedule" breadcrumb-current="Exam Schedule" keep-title>
                <x-slot:search>
                    <div class="flex items-center gap-2">
                        <x-input.search
                            id="tableSearch"
                            placeholder="Search schedule..."
                            class="w-72"
                            oninput="document.getElementById('tableSearchMobile').value = this.value; searchTable();"
                        />
                        <x-button.secondary type="button" onclick="restoreScheduleSearch()">Restore</x-button.secondary>
                    </div>
                </x-slot:search>

                <x-slot:actions>
                    <x-button.secondary type="button" onclick="toggleFilterModal()" class="w-full">Filter</x-button.secondary>

                    <x-dropdown button-id="btnScheduleExport" menu-id="scheduleExportDropdown" label="Export" align="full">
                        <x-dropdown.item onclick="exportData('pdf')">PDF</x-dropdown.item>
                        <x-dropdown.item onclick="exportData('excel')">Excel</x-dropdown.item>
                        <x-dropdown.item onclick="window.print()">Print</x-dropdown.item>
                    </x-dropdown>

                    <x-button.primary type="button" onclick="openModal()" class="w-full">Create Schedule</x-button.primary>
                </x-slot:actions>

                <x-slot:mobile-search>
                    <div class="col-span-3 grid grid-cols-3 gap-2">
                        <x-input.search
                            id="tableSearchMobile"
                            placeholder="Search schedule..."
                            class="col-span-2 min-w-0"
                            oninput="document.getElementById('tableSearch').value = this.value; searchTable();"
                        />
                        <x-button.secondary type="button" onclick="restoreScheduleSearch()" class="w-full">Restore</x-button.secondary>
                    </div>
                </x-slot:mobile-search>
            </x-school.list-header>

            {{-- Filter Modal --}}
            <x-modal.form
                id="filterModal"
                form-id="scheduleFilterForm"
                title="Schedule Filter"
                close-button-id="closeScheduleFilterModal"
                title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
                onsubmit="event.preventDefault(); applyFilters();"
            >
                <div class="relative">
                    <x-input.dropdown-select id="f_class" placeholder="Select Class" :options="[]" />
                    <x-input.floating-label for="f_class" :floating="false">Class</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="f_group" placeholder="Select Group" :options="[]" />
                    <x-input.floating-label for="f_group" :floating="false">Group</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="f_section" placeholder="Select Section" :options="[]" />
                    <x-input.floating-label for="f_section" :floating="false">Section</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="f_session" placeholder="Select Session" :options="[]" />
                    <x-input.floating-label for="f_session" :floating="false">Session</x-input.floating-label>
                </div>
                <div class="relative md:col-span-2">
                    <x-input.dropdown-select id="f_exam" placeholder="Select Exam" :options="[]" />
                    <x-input.floating-label for="f_exam" :floating="false">Exam Name</x-input.floating-label>
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
                :empty-colspan="12"
                empty-message="No schedules found."
                show-footer="true"
                min-width="1250px"
                tbody-id="scheduleTableBody"
            >
                <x-slot:columns>
                    <colgroup>
                        <col style="width:45px;">
                        <col style="width:90px;">
                        <col style="width:90px;">
                        <col style="width:90px;">
                        <col style="width:90px;">
                        <col style="width:125px;">
                        <col style="width:90px;">
                        <col style="width:95px;">
                        <col style="width:95px;">
                        <col style="width:105px;">
                        <col style="width:95px;">
                        <col style="width:100px;">
                    </colgroup>
                </x-slot:columns>

                <x-slot:head>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Exam</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Total Sub</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Submitted</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Remaining</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Date</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Time</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
                </x-slot:head>

                <x-slot:footer>
                    <div class="flex items-center justify-between px-3">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500" id="paginationInfo">0 of 0</div>
                        <div class="flex items-center gap-1" id="paginationControls"></div>
                    </div>
                </x-slot:footer>
            </x-school.data-table>
        </div>
    </div>

    {{-- Schedule Main Modal --}}
    <x-modal.form
        id="mainModal"
        form-id="scheduleForm"
        title="New Exam Schedule"
        close-button-id="closeScheduleModalButton"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[520px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
        panel-style="border-radius:4px; max-height:min(520px, calc(100dvh - 2.5rem));"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
        onsubmit="event.preventDefault(); saveSchedule();"
    >
        <input type="hidden" id="edit_id">

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
        <div class="relative md:col-span-2">
            <x-input.dropdown-select id="m_exam" placeholder="Select Exam" :options="[]" />
            <x-input.floating-label for="m_exam" :floating="false">Exam Name</x-input.floating-label>
        </div>

        <div class="grid grid-cols-3 gap-2 border border-slate-200 bg-slate-50 p-2 md:col-span-2">
            <div class="border border-slate-200 bg-white p-2 text-center">
                <span class="block text-[9px] font-bold text-gray-400">Total</span>
                <span id="c_total" class="text-sm font-black text-blue-600">0</span>
            </div>
            <div class="border border-slate-200 bg-white p-2 text-center">
                <span class="block text-[9px] font-bold text-gray-400">Submitted</span>
                <span id="c_submitted" class="text-sm font-black text-green-600">0</span>
            </div>
            <div class="border border-slate-200 bg-white p-2 text-center">
                <span class="block text-[9px] font-bold text-gray-400">Remaining</span>
                <span id="c_remaining" class="text-sm font-black text-red-600">0</span>
            </div>
        </div>

        <div class="relative">
            <x-input.control type="date" id="m_date" class="peer" />
            <x-input.floating-label for="m_date" :floating="false">Publish Date</x-input.floating-label>
        </div>
        <div class="relative">
            <x-input.control type="time" id="m_time" class="peer" />
            <x-input.floating-label for="m_time" :floating="false">Publish Time</x-input.floating-label>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary type="button" onclick="closeModal()" class="w-full">Cancel</x-button.secondary>
                <x-button.primary type="submit" class="w-full">Save</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>



   <script>
    let isEdit = false;
    let currentPage = 1;

    window.addEventListener('load', () => {
        initializeScheduleDropdownEvents();
        loadInitialData();
        fetchTable();
    });

    function searchTable() {
        const input = document.getElementById('tableSearch');
        const filter = input.value.toUpperCase();
        const rows = document.getElementById('scheduleTableBody')?.querySelectorAll('tr') || [];

        rows.forEach(row => {
            const textContent = row.textContent || row.innerText;
            row.style.display = textContent.toUpperCase().includes(filter) ? '' : 'none';
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
    }

    function formatTime(timeStr) {
        if (!timeStr) return 'N/A';
        let [hours, minutes] = timeStr.split(':');
        hours = parseInt(hours);
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    function scheduleDropdownParts(id) {
        const input = document.getElementById(id);
        const root = input?.closest('[data-dropdown-select]');

        return {
            input,
            root,
            label: root?.querySelector('[data-dropdown-select-label]'),
            menu: root?.querySelector('[data-dropdown-select-menu]'),
        };
    }

    function selectedScheduleOption(id) {
        const { input, menu } = scheduleDropdownParts(id);

        return Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
            .find(option => String(option.dataset.value || '') === String(input?.value || ''));
    }

    function selectedScheduleId(id) {
        return selectedScheduleOption(id)?.dataset.optionId || '';
    }

    function setScheduleDropdownValue(id, value = '', label = null, shouldNotify = false) {
        const parts = scheduleDropdownParts(id);
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

    function fillOptions(id, data, field) {
        const parts = scheduleDropdownParts(id);
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

            option.addEventListener('click', () => {
                setScheduleDropdownValue(id, option.dataset.value, option.textContent.trim());
                parts.menu.classList.add('hidden');
                parts.root?.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                parts.input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            parts.menu.appendChild(option);
        });

        setScheduleDropdownValue(id, '');
    }

    function initializeScheduleDropdownEvents() {
        const cascades = {
            m_class: 'm_group',
            m_group: 'm_section',
            m_section: 'm_session',
            f_class: 'f_group',
            f_group: 'f_section',
            f_section: 'f_session',
        };

        Object.entries(cascades).forEach(([id, next]) => {
            document.getElementById(id)?.addEventListener('change', event => {
                handleCascade(event.currentTarget, next);
            });
        });

        document.getElementById('m_session')?.addEventListener('change', async () => {
            await fetchFilteredExams(false);
            fetchSubjectCounts();
        });
        document.getElementById('m_exam')?.addEventListener('change', fetchSubjectCounts);
        document.getElementById('f_session')?.addEventListener('change', () => fetchFilteredExams(true));
    }

    function loadInitialData() {
        axios.get('/api/get-school-classes').then(res => {
            fillOptions('m_class', res.data.data, 'class_name');
            fillOptions('f_class', res.data.data, 'class_name');
        });
        
        // Load initial exams (will be refined by cascade)
        fetchFilteredExams(false);
        fetchFilteredExams(true);
    }

    /**
     * Refreshes the Exam dropdown based on selected hierarchy
     */
    function fetchFilteredExams(isFilter = false) {
        const prefix = isFilter ? 'f_' : 'm_';
        const params = {
            class_name: document.getElementById(`${prefix}class`).value,
            group_name: document.getElementById(`${prefix}group`).value,
            section_name: document.getElementById(`${prefix}section`).value,
            session_name: document.getElementById(`${prefix}session`).value,
        };

        return axios.get('/api/get-school-exams', { params }).then(res => {
            fillOptions(`${prefix}exam`, res.data.data, 'exam_name');
        });
    }

    async function handleCascade(el, nextId) {
        const id = selectedScheduleId(el.id);
        const isFilter = el.id.startsWith('f_');
        
        if (!id) return;

        let url = '';
        let fld = '';

        if (nextId.includes('group')) {
            url = `/api/get-school-groups?class_id=${id}`;
            fld = 'group_name';
        } else if (nextId.includes('section')) {
            url = `/api/get-school-sections?group_id=${id}`;
            fld = 'section_name';
        } else if (nextId.includes('session')) {
            const classSelect = isFilter ? 'f_class' : 'm_class';
            const classId = selectedScheduleId(classSelect);
            url = `/api/get-school-sessions?section_id=${id}&class_id=${classId}`;
            fld = 'session_year';
        }

        const res = await axios.get(url);
        fillOptions(nextId, res.data.data, fld);

        // Sync exams every time the hierarchy changes
        await fetchFilteredExams(isFilter);

        // Trigger subject count if we are in the main modal
        if (!isFilter) fetchSubjectCounts();
    }

    function fetchSubjectCounts() {
        const clsEl = document.getElementById('m_class');
        const cls = clsEl.value;
        const clsId = selectedScheduleId('m_class');
        const exm = document.getElementById('m_exam').value;
        const ses = document.getElementById('m_session').value;

        if (!cls || !exm || !ses) return;

        axios.get('/api/school-exam-schedules/counts', {
            params: {
                class_name: cls,
                class_id: clsId,
                exam_name: exm,
                session_name: ses
            }
        }).then(res => {
            document.getElementById('c_total').innerText = res.data.total;
            document.getElementById('c_submitted').innerText = res.data.submitted;
            document.getElementById('c_remaining').innerText = res.data.remaining;
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
        <button class="${btnClass}" ${meta.current_page === 1 ? 'disabled' : ''} 
            onclick="fetchTable(${meta.current_page - 1})">
            <i class="mdi mdi-chevron-left"></i>
        </button>`;

        for (let i = 1; i <= meta.last_page; i++) {
            controls.innerHTML += `
            <button class="${btnClass} ${meta.current_page === i ? 'active' : ''}" 
                onclick="fetchTable(${i})">
                ${i}
            </button>`;
        }

        controls.innerHTML += `
        <button class="${btnClass}" ${meta.current_page === meta.last_page ? 'disabled' : ''} 
            onclick="fetchTable(${meta.current_page + 1})">
            <i class="mdi mdi-chevron-right"></i>
        </button>`;
    }

    function escapeScheduleHtml(value) {
        return String(value ?? 'N/A').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function scheduleTableCell(value, alignment = 'text-left', extraClass = '') {
        const content = escapeScheduleHtml(value);

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
            group_name: document.getElementById('f_group').value,
            section_name: document.getElementById('f_section').value,
            session_name: document.getElementById('f_session').value,
            exam_name: document.getElementById('f_exam').value
        };

        axios.get('/api/school-exam-schedules', {
            params
        }).then(res => {
            const body = document.getElementById('scheduleTableBody');
            body.innerHTML = '';

            if (res.data.data.length === 0) {
                body.innerHTML =
                    '<tr><td colspan="12" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No schedules found.</td></tr>';
                renderPagination(res.data);
                return;
            }

            res.data.data.forEach((item, i) => {
                body.innerHTML += `<tr class="transition hover:bg-gray-50">
                    ${scheduleTableCell(res.data.from + i, 'text-center')}
                    ${scheduleTableCell(item.class_name)}
                    ${scheduleTableCell(item.group_name)}
                    ${scheduleTableCell(item.section_name)}
                    ${scheduleTableCell(item.session_name)}
                    ${scheduleTableCell(item.exam_name)}
                    ${scheduleTableCell(item.total_subject)}
                    ${scheduleTableCell(item.submitted_subject)}
                    ${scheduleTableCell(item.remaining_subject)}
                    ${scheduleTableCell(formatDate(item.publish_date))}
                    ${scheduleTableCell(formatTime(item.publish_time))}
                    <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                        <div class="mx-auto flex h-8 items-center justify-center space-x-1">
                            <button type="button" title="Edit schedule" aria-label="Edit schedule" onclick="editItem(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1">
                                <i class="far fa-edit text-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" title="Delete schedule" aria-label="Delete schedule" onclick="deleteItem(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1">
                                <i class="far fa-trash-alt text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            renderPagination(res.data);
        });
    }

    function saveSchedule() {
        const data = {
            class_name: document.getElementById('m_class').value,
            group_name: document.getElementById('m_group').value,
            section_name: document.getElementById('m_section').value,
            session_name: document.getElementById('m_session').value,
            exam_name: document.getElementById('m_exam').value,
            total_subject: document.getElementById('c_total').innerText,
            submitted_subject: document.getElementById('c_submitted').innerText,
            remaining_subject: document.getElementById('c_remaining').innerText,
            publish_date: document.getElementById('m_date').value,
            publish_time: document.getElementById('m_time').value,
            status: 'Active'
        };

        const id = document.getElementById('edit_id').value;
        const request = isEdit ? axios.put(`/api/school-exam-schedules/${id}`, data) : axios.post(
            '/api/school-exam-schedules', data);

        request.then(() => {
            Toastify({
                text: "Operation Successful",
                style: { background: "#2563eb" }
            }).showToast();
            closeModal();
            fetchTable(currentPage);
        }).catch(err => {
            if (err.response?.status === 422) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Exists',
                    text: err.response.data.message,
                    confirmButtonColor: '#2563eb'
                });
            }
        });
    }

    async function editItem(id) {
        isEdit = true;
        document.getElementById('mainModalTitle').innerText = 'Edit Exam Schedule';
        const res = await axios.get(`/api/school-exam-schedules/${id}`);
        const d = res.data;

        document.getElementById('edit_id').value = id;

        const classEl = document.getElementById('m_class');
        setScheduleDropdownValue('m_class', d.class_name, d.class_name);
        await handleCascade(classEl, 'm_group');

        const groupEl = document.getElementById('m_group');
        setScheduleDropdownValue('m_group', d.group_name, d.group_name);
        await handleCascade(groupEl, 'm_section');

        const sectionEl = document.getElementById('m_section');
        setScheduleDropdownValue('m_section', d.section_name, d.section_name);
        await handleCascade(sectionEl, 'm_session');

        setScheduleDropdownValue('m_session', d.session_name, d.session_name);
        await fetchFilteredExams(false);
        setScheduleDropdownValue('m_exam', d.exam_name, d.exam_name);
        
        document.getElementById('m_date').value = d.publish_date;
        document.getElementById('m_time').value = d.publish_time;

        document.getElementById('c_total').innerText = d.total_subject;
        document.getElementById('c_submitted').innerText = d.submitted_subject;
        document.getElementById('c_remaining').innerText = d.remaining_subject;

        document.getElementById('mainModal').classList.remove('hidden');
    }

    function deleteItem(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(r => {
            if (r.isConfirmed) axios.delete(`/api/school-exam-schedules/${id}`).then(() => fetchTable(currentPage));
        });
    }

    function openModal() {
        isEdit = false;
        document.getElementById('mainModalTitle').innerText = 'New Exam Schedule';
        document.getElementById('edit_id').value = '';
        ['m_class', 'm_group', 'm_section', 'm_session', 'm_exam'].forEach(id => {
            setScheduleDropdownValue(id, '');
        });
        ['m_group', 'm_section', 'm_session', 'm_exam'].forEach(id => {
            fillOptions(id, [], '');
        });
        document.getElementById('m_date').value = '';
        document.getElementById('m_time').value = '';
        document.getElementById('c_total').innerText = '0';
        document.getElementById('c_submitted').innerText = '0';
        document.getElementById('c_remaining').innerText = '0';
        document.getElementById('mainModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('mainModal').classList.add('hidden');
    }

    function toggleFilterModal() {
        document.getElementById('filterModal').classList.toggle('hidden');
    }

    function resetFilters() {
        ['f_class', 'f_group', 'f_section', 'f_session', 'f_exam'].forEach(id => {
            setScheduleDropdownValue(id, '');
        });
        ['f_group', 'f_section', 'f_session', 'f_exam'].forEach(id => {
            fillOptions(id, [], '');
        });
        fetchTable(1);
        toggleFilterModal();
    }

    function applyFilters() {
        fetchTable(1);
        toggleFilterModal();
    }

    function restoreScheduleSearch() {
        const desktopSearch = document.getElementById('tableSearch');
        const mobileSearch = document.getElementById('tableSearchMobile');

        if (desktopSearch) desktopSearch.value = '';
        if (mobileSearch) mobileSearch.value = '';

        searchTable();
    }
</script>
@endsection
