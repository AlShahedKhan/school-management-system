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

        @media (max-width: 768px) {
            .pagination-btn {
                height: 24px !important;
                min-width: 24px !important;
            }
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <x-school.list-header title="Admit Card" breadcrumb-current="Admit Card" keep-title>
                <x-slot:search>
                    <form class="flex items-center gap-2" onsubmit="event.preventDefault(); fetchTable(1);">
                        <x-input.search
                            id="header_search"
                            name="search"
                            placeholder="Search ID or Name..."
                            class="w-full lg:w-72"
                            oninput="document.getElementById('header_search_mobile').value = this.value"
                            onkeydown="if(event.key === 'Enter') fetchTable(1);"
                        />
                        <x-button.secondary id="btnRestoreDesktop" type="button" onclick="restoreAdmitSearch()">
                            Restore
                        </x-button.secondary>
                    </form>
                </x-slot:search>

                <x-slot:actions>
                    <x-button.secondary type="button" onclick="toggleFilterModal()" class="w-full">
                        Filter
                    </x-button.secondary>

                    <x-dropdown button-id="btnAdmitExport" menu-id="admitExportDropdown" label="Export" align="full">
                        <x-dropdown.item onclick="exportData('pdf-mobile')">PDF</x-dropdown.item>
                        <x-dropdown.item onclick="exportData('excel')">Excel</x-dropdown.item>
                        <x-dropdown.item onclick="exportData('pdf')">Print</x-dropdown.item>
                    </x-dropdown>

                    <x-button.primary type="button" onclick="openAdmitModal()" class="w-full">
                        Admit Card
                    </x-button.primary>
                </x-slot:actions>

                <x-slot:mobile-search>
                    <form class="col-span-3 grid grid-cols-3 gap-2" onsubmit="event.preventDefault(); fetchTable(1);">
                        <x-input.search
                            id="header_search_mobile"
                            name="search"
                            placeholder="Search ID or Name..."
                            class="col-span-2 min-w-0"
                            oninput="document.getElementById('header_search').value = this.value"
                            onkeydown="if(event.key === 'Enter') { document.getElementById('header_search').value = this.value; fetchTable(1); }"
                        />
                        <x-button.secondary id="btnRestoreMobile" type="button" onclick="restoreAdmitSearch()" class="w-full">
                            Restore
                        </x-button.secondary>
                    </form>
                </x-slot:mobile-search>
            </x-school.list-header>

            <x-school.data-table
                :empty="false"
                :empty-colspan="10"
                empty-message="No admit cards found."
                show-footer="true"
                min-width="1100px"
                tbody-id="admitTableBody"
            >
                <x-slot:columns>
                    <colgroup>
                        <col style="width:45px;">
                        <col style="width:105px;">
                        <col style="width:95px;">
                        <col style="width:95px;">
                        <col style="width:95px;">
                        <col style="width:125px;">
                        <col style="width:140px;">
                        <col style="width:170px;">
                        <col style="width:125px;">
                        <col style="width:110px;">
                    </colgroup>
                </x-slot:columns>

                <x-slot:head>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Exam Name</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student ID</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Student Name</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Admit Number</x-table.th>
                    <x-table.th unstyled class="h-8 min-w-[110px] whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
                </x-slot:head>

                <x-slot:footer>
                    <div class="pagination-container flex w-full items-center justify-between px-2">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500" id="paginationInfo"></div>
                        <div class="flex items-center gap-1" id="paginationControls"></div>
                    </div>
                </x-slot:footer>
            </x-school.data-table>
        </div>
    </div>

    @include('school.exam.partials.admit-filter-modal')

    {{-- Admit Card Modal --}}
    <x-modal.form
        id="admitModal"
        form-id="admitForm"
        title="Bulk Admit Card Generator"
        close-button-id="closeAdmitModal"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    >
        <input type="hidden" id="admit_edit_id">

        <div class="relative">
            <x-input.dropdown-select
                id="class_name"
                placeholder="Select Class"
                :options="[]"
                add-button-id="openClassFromAdmitForm"
                add-button-label="Add class"
                add-button-target="classModal"
            />
            <x-input.floating-label for="class_name" :floating="false">Class</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="group_name"
                placeholder="Select Group"
                :options="[]"
                add-button-id="openGroupFromAdmitForm"
                add-button-label="Add group"
                add-button-target="groupModal"
            />
            <x-input.floating-label for="group_name" :floating="false">Group</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="section_name"
                placeholder="Select Section"
                :options="[]"
                add-button-id="openSectionFromAdmitForm"
                add-button-label="Add section"
                add-button-target="sectionModal"
            />
            <x-input.floating-label for="section_name" :floating="false">Section</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="session_name"
                placeholder="Select Session"
                :options="[]"
                add-button-id="openSessionFromAdmitForm"
                add-button-label="Add session"
                add-button-target="sessionModal"
            />
            <x-input.floating-label for="session_name" :floating="false">Session</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="exam_name"
                placeholder="Select Exam"
                :options="[]"
                add-button-id="openExamFromAdmitForm"
                add-button-label="Add exam"
                add-button-target="examModal"
            />
            <x-input.floating-label for="exam_name" :floating="false">Exam Name</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                id="generate_type_display"
                value="All Students"
                readonly
                aria-readonly="true"
                class="cursor-default bg-white text-slate-800"
            />
            <x-input.floating-label for="generate_type_display" :floating="false">Generate For</x-input.floating-label>
        </div>

        <div
            id="admitPrerequisiteWarning"
            class="hidden border border-amber-200 bg-amber-50 px-3 py-2 text-[10px] leading-4 text-amber-700 md:col-span-2"
        >
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5 shrink-0 text-[10px]" aria-hidden="true"></i>
                <span>Please create the Exam and Exam Routine before generating the Admit Card.</span>
            </div>
        </div>

        <div
            id="studentStatusBox"
            class="border border-dashed border-slate-300 bg-white px-3 py-2 md:col-span-2"
        >
            <label class="block text-[9px] leading-3 text-gray-400">
                Student Status
            </label>
            <div id="studentCountDisplay" class="font-mono text-[10px] font-bold leading-4 tracking-tighter text-blue-600">
                0 Students Identified
            </div>
            <div class="text-[8px] leading-3 text-gray-400">
                Admit card number will be auto-generated sequentially.
            </div>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary
                    id="closeAdmitModal"
                    type="button"
                    onclick="document.getElementById('admitModal').classList.add('hidden')"
                    class="w-full"
                >
                    Cancel
                </x-button.secondary>
                <x-button.primary
                    type="submit"
                    id="submitBtn"
                    disabled
                    class="w-full disabled:opacity-50"
                >
                    <span id="btnSpinner" class="hidden">
                        <svg class="mr-1.5 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span id="btnText">Generate</span>
                </x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    {{-- Quick-create modals shared with the Exam Routine form. --}}
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.exam.exam_name.partials.exam-modal')

    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.exam.exam_name.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.exam.exam_name.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.exam.exam_name.partials.js.error-validation')


    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let studentsList = [];

        function populateDropdown(menuId, data, valueField, labelField) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            menu.innerHTML = '';
            data.forEach(item => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                option.dataset.value = String(item[valueField] ?? '');
                option.dataset.optionId = String(item.id ?? '');
                option.textContent = item[labelField] ?? '';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.setAttribute('data-dropdown-select-option', '');

                option.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root?.querySelector('[data-dropdown-select-input]');
                    const label = root?.querySelector('[data-dropdown-select-label]');
                    if (!root || !input || !label) return;

                    input.value = this.dataset.value || '';
                    label.textContent = this.textContent.trim();
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const selected = item === this;
                        item.classList.toggle('bg-slate-100', selected);
                        item.classList.toggle('text-slate-900', selected);
                        item.classList.toggle('text-slate-800', !selected);
                        item.setAttribute('aria-selected', String(selected));
                    });
                    menu.classList.add('hidden');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                menu.appendChild(option);
            });
        }

        function setDropdownValue(dropdownId, value, label) {
            const input = document.getElementById(dropdownId);
            const labelElement = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            const menu = document.getElementById(`${dropdownId}Menu`);

            if (input) input.value = value || '';
            if (labelElement) labelElement.textContent = label || labelElement.dataset.placeholder || 'Select...';

            menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                const selected = String(option.dataset.value || '') === String(value || '');
                option.classList.toggle('bg-slate-100', selected);
                option.classList.toggle('text-slate-900', selected);
                option.classList.toggle('text-slate-800', !selected);
                option.setAttribute('aria-selected', String(selected));
            });
        }

        function getSelectedDataId(elementOrId) {
            const input = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
            if (!input) return '';

            if (input.matches?.('[data-dropdown-select-input]')) {
                const menu = document.getElementById(`${input.id}Menu`);
                const selected = menu?.querySelector(`[data-value="${CSS.escape(input.value)}"]`);
                return selected?.dataset.optionId || '';
            }

            return input.options?.[input.selectedIndex]?.getAttribute('data-id') || '';
        }

        function selectAdmitOption(id, recordId) {
            const menu = document.getElementById(`${id}Menu`);
            if (!menu || !recordId) return false;

            const option = Array.from(menu.querySelectorAll('[data-dropdown-select-option]'))
                .find(item => String(item.dataset.optionId) === String(recordId));
            if (!option) return false;

            setDropdownValue(id, option.dataset.value, option.textContent.trim());
            return true;
        }

        function getDropdownSelectSelectedLabel(id) {
            const input = document.getElementById(id);
            const menu = document.getElementById(`${id}Menu`);
            const selected = menu?.querySelector(`[data-value="${CSS.escape(input?.value || '')}"]`);
            return selected?.textContent?.trim() || '';
        }

        async function restoreAdmitSelection({ classId = null, groupId = null, sectionId = null, sessionId = null } = {}) {
            await loadInitial();

            if (classId && selectAdmitOption('class_name', classId)) {
                await handleCascade(document.getElementById('class_name'), 'group');
            }
            if (groupId && selectAdmitOption('group_name', groupId)) {
                await handleCascade(document.getElementById('group_name'), 'section');
            }
            if (sectionId && selectAdmitOption('section_name', sectionId)) {
                await handleCascade(document.getElementById('section_name'), 'session');
            }
            if (sessionId && selectAdmitOption('session_name', sessionId)) {
                await fetchFilteredExams();
                fetchStudentCount();
            }
        }

        document.addEventListener('school:class-saved', async event => {
            const { classItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'admitModal' || !isNew || !classItem?.id) return;

            event.preventDefault();
            await restoreAdmitSelection({ classId: classItem.id });
            document.getElementById('admitModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:group-saved', async event => {
            const { groupItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'admitModal' || !isNew || !groupItem?.id) return;

            event.preventDefault();
            await restoreAdmitSelection({ classId: groupItem.class_id, groupId: groupItem.id });
            document.getElementById('admitModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:section-saved', async event => {
            const { sectionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'admitModal' || !isNew || !sectionItem?.id) return;

            event.preventDefault();
            await restoreAdmitSelection({
                classId: sectionItem.class_id,
                groupId: sectionItem.group_id,
                sectionId: sectionItem.id,
            });
            document.getElementById('admitModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:session-saved', async event => {
            const { sessionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'admitModal' || !isNew || !sessionItem?.id) return;

            event.preventDefault();
            await restoreAdmitSelection({
                classId: sessionItem.class_id,
                groupId: sessionItem.group_id,
                sectionId: sessionItem.section_id,
                sessionId: sessionItem.id,
            });
            document.getElementById('admitModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:exam-saved', async event => {
            const { examItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'admitModal' || !isNew || !examItem?.id) return;

            await fetchFilteredExams();
            selectAdmitOption('exam_name', examItem.id);
            checkPrerequisiteStatus();
            document.getElementById('admitModal')?.classList.remove('hidden');
        });

        async function loadInitial() {
            await axios.get('/api/get-school-classes').then(res => {
                populateDropdown('class_nameMenu', res.data.data || [], 'class_name', 'class_name');
                setDropdownValue('class_name', '', 'Select Class');
                populateDropdown('filter_class_nameMenu', res.data.data || [], 'class_name', 'class_name');
                setDropdownValue('filter_class_name', '', 'Select Class');
            });
            await fetchFilteredExams();
        }

        function fetchFilteredExams(isFilter = false) {
            const prefix = isFilter ? 'filter_' : '';
            const params = {
                class_name: document.getElementById(`${prefix}class_name`).value,
                group_name: document.getElementById(`${prefix}group_name`).value,
                section_name: document.getElementById(`${prefix}section_name`).value,
                session_name: document.getElementById(`${prefix}session_name`).value,
            };

            return axios.get('/api/get-school-exams', {
                params
            }).then(res => {
                if (isFilter) {
                    populateDropdown('filter_exam_nameMenu', res.data.data || [], 'exam_name', 'exam_name');
                    setDropdownValue('filter_exam_name', '', 'Select Exam');
                } else {
                    populateDropdown('exam_nameMenu', res.data.data || [], 'exam_name', 'exam_name');
                    setDropdownValue('exam_name', '', 'Select Exam');
                }
                if (!isFilter) {
                    checkPrerequisiteStatus();
                }
            });
        }

        async function handleCascade(el, next, callback = null) {
            const id = getSelectedDataId(el);
            const isFilter = el.id.startsWith('filter_');
            if (!id) return;

            if (next.includes('group')) {
                await fetchFill(`/api/get-school-groups?class_id=${id}`, isFilter ? 'filter_group_name' : 'group_name',
                    'Group', 'group_name');
            } else if (next.includes('section')) {
                await fetchFill(`/api/get-school-sections?group_id=${id}`, isFilter ? 'filter_section_name' :
                    'section_name', 'Section', 'section_name');
            } else if (next.includes('session')) {
                const classId = getSelectedDataId(isFilter ? 'filter_class_name' : 'class_name');
                await fetchFill(`/api/get-school-sessions?section_id=${id}&class_id=${classId}`, isFilter ?
                    'filter_session_name' : 'session_name', 'Session', 'session_year');
            }

            await fetchFilteredExams(isFilter);
            if (!isFilter) fetchStudentCount();
            if (callback) callback();
        }

        function fetchFill(url, tid, lbl, fld) {
            return axios.get(url).then(res => {
                const target = document.getElementById(tid);
                if (target?.matches?.('[data-dropdown-select-input]')) {
                    populateDropdown(`${tid}Menu`, res.data.data || [], fld, fld);
                    setDropdownValue(tid, '', `Select ${lbl}`);
                    return;
                }

                let o = `<option value="">Select ${lbl}</option>`;
                res.data.data.forEach(i => o += `<option value="${i[fld]}" data-id="${i.id}">${i[fld]}</option>`);
                target.innerHTML = o;
            });
        }

        let admitPrerequisitesValid = false;

        function updateAdmitSubmitState() {
            const btn = document.getElementById('submitBtn');
            const hasStudents = studentsList.length > 0;
            const classSelected = document.getElementById('class_name').value;
            const sessionSelected = document.getElementById('session_name').value;
            const examSelected = document.getElementById('exam_name').value;

            const enable = admitPrerequisitesValid &&
                classSelected &&
                sessionSelected &&
                examSelected &&
                hasStudents;

            btn.disabled = !enable;
        }

        function setPrerequisiteWarning(valid, message = '') {
            admitPrerequisitesValid = valid;
            const warningEl = document.getElementById('admitPrerequisiteWarning');
            if (!warningEl) return;

            if (!valid && message) {
                warningEl.classList.remove('hidden');
                warningEl.innerText = message;
            } else {
                warningEl.classList.add('hidden');
                warningEl.innerText = '';
            }

            updateAdmitSubmitState();
        }

        function checkPrerequisiteStatus() {
            const className = document.getElementById('class_name').value;
            const groupName = document.getElementById('group_name').value;
            const sectionName = document.getElementById('section_name').value;
            const sessionName = document.getElementById('session_name').value;
            const examName = document.getElementById('exam_name').value;

            if (!className || !sessionName || !examName) {
                setPrerequisiteWarning(false, '');
                return;
            }

            axios.get('/api/check-admit-card-prerequisites', {
                params: {
                    class_name: className,
                    group_name: groupName,
                    section_name: sectionName,
                    session_name: sessionName,
                    exam_name: examName
                }
            }).then(res => {
                if (res.data.valid) {
                    setPrerequisiteWarning(true, '');
                } else {
                    setPrerequisiteWarning(false, res.data.message || 'Please create the Exam and Exam Routine before generating the Admit Card.');
                }
            }).catch(err => {
                const message = err.response?.data?.message || 'Unable to validate prerequisites at this time.';
                setPrerequisiteWarning(false, message);
            });
        }

        function fetchStudentCount() {
            const params = {
                class_id: getSelectedDataId('class_name'),
                group_id: getSelectedDataId('group_name'),
                section_id: getSelectedDataId('section_name'),
                session_id: getSelectedDataId('session_name')
            };

            if (!params.class_id || !params.session_id) {
                document.getElementById('studentCountDisplay').innerText = `0 Students Identified`;
                document.getElementById('submitBtn').disabled = true;
                setPrerequisiteWarning(false, '');
                return;
            }

            axios.get('/api/get-school-students', {
                params
            }).then(res => {
                studentsList = res.data.data;
                document.getElementById('studentCountDisplay').innerText =
                    `${studentsList.length} Students Identified`;
                updateAdmitSubmitState();
            });
        }

        function fetchTable(page = 1) {
            const params = {
                page,
                class_name: document.getElementById('filter_class_name').value,
                group_name: document.getElementById('filter_group_name').value,
                section_name: document.getElementById('filter_section_name').value,
                session_name: document.getElementById('filter_session_name').value,
                exam_name: document.getElementById('filter_exam_name').value,
                search: document.getElementById('header_search').value
            };

            axios.get('/api/school-exam-admit-cards', {
                params
            }).then(res => {
                const meta = res.data;
                const body = document.getElementById('admitTableBody');
                body.innerHTML = '';
                meta.data.forEach((item, i) => {
                    const itemJson = JSON.stringify(item).replaceAll("'", '&#39;');
                    const cell = value => escapeAdmitCardHtml(value ?? '-');

                    body.innerHTML += `<tr class="transition-colors hover:bg-gray-50">
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${meta.from + i}</td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.class_name)}">${cell(item.class_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.group_name)}">${cell(item.group_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.section_name)}">${cell(item.section_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.session_name)}">${cell(item.session_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.exam_name)}">${cell(item.exam_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll font-mono" title="${cell(item.student_id_number)}">${cell(item.student_id_number)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll capitalize" title="${cell(item.student_name)}">${cell(item.student_name)}</div></td>
                        <td class="h-8 border border-gray-300 px-3"><div class="school-data-table-cell-scroll" title="${cell(item.admit_card_number)}">${cell(item.admit_card_number)}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                            <div class="flex h-8 items-center justify-center space-x-1 mx-auto">
                                <button type="button" title="Edit admit card" aria-label="Edit admit card" onclick='editAdmit(${itemJson})' class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1"><i class="far fa-edit text-sm" aria-hidden="true"></i></button>
                                <button type="button" title="Delete admit card" aria-label="Delete admit card" onclick="deleteAdmit(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1"><i class="far fa-trash-alt text-sm" aria-hidden="true"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                renderPagination(meta);
            });
        }

        function exportData(type) {
            const params = {
                class_name: document.getElementById('filter_class_name').value,
                group_name: document.getElementById('filter_group_name').value,
                section_name: document.getElementById('filter_section_name').value,
                session_name: document.getElementById('filter_session_name').value,
                exam_name: document.getElementById('filter_exam_name').value,
                search: document.getElementById('header_search').value,
                export: type
            };

            if (type === 'excel') {
                axios.get('/api/school-exam-admit-cards', {
                    params: {
                        ...params,
                        per_page: 500
                    }
                }).then(res => {
                    const cards = res.data.data || [];

                    if (!cards.length) {
                        Swal.fire('Info', 'No admit cards found for current filters.', 'info');
                        return;
                    }

                    const columns = [
                        ['Class', 'class_name'],
                        ['Group', 'group_name'],
                        ['Section', 'section_name'],
                        ['Session', 'session_name'],
                        ['Exam Name', 'exam_name'],
                        ['Student ID', 'student_id_number'],
                        ['Student Name', 'student_name'],
                        ['Admit Number', 'admit_card_number'],
                    ];
                    const csvValue = value => `"${String(value ?? '').replaceAll('"', '""')}"`;
                    const csv = [
                        columns.map(([label]) => csvValue(label)).join(','),
                        ...cards.map(card => columns.map(([, key]) => csvValue(card[key] || '-')).join(',')),
                    ].join('\n');
                    const downloadUrl = URL.createObjectURL(new Blob([`\uFEFF${csv}`], {
                        type: 'text/csv;charset=utf-8;'
                    }));
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = 'admit-cards.csv';
                    link.click();
                    URL.revokeObjectURL(downloadUrl);
                }).catch(() => {
                    Swal.fire('Error', 'Failed to export admit cards. Please try again.', 'error');
                });

                return;
            }

            if (type === 'pdf' || type === 'pdf-mobile') {
                // Open window immediately to preserve user interaction context
                const previewWindow = window.open('', '_blank');
                if (!previewWindow) {
                    Swal.fire('Error', 'Popup blocked! Please allow popups for this site.', 'error');
                    return;
                }

                // Show basic loader in the new window
                previewWindow.document.write(`
                    <html>
                        <head><title>Preparing Admit Cards...</title><\/head>
                        <body style="display:flex;justify-content:center;align-items:center;height:100vh;margin:0;font-family:sans-serif;background:#f8fafc;">
                            <div style="text-align:center;">
                                <div style="border:4px solid #f3f3f3;border-top:4px solid #2563eb;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:0 auto 15px;"></div>
                                <div style="color:#64748b;font-size:14px;font-weight:600;">Generating Admit Cards...</div>
                            </div>
                            <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
                        <\/body>
                    </html>
                `);

                axios.get('/api/school-exam-admit-cards', {
                    params: {
                        ...params,
                        per_page: 500
                    }
                }).then(res => {
                    if (!res.data.data || res.data.data.length === 0) {
                        previewWindow.close();
                        Swal.fire('Info', 'No admit cards found for current filters.', 'info');
                        return;
                    }

                    if (type === 'pdf-mobile') {
                        generateMobilePreview(res.data.data, res.data.school_info, res.data.routines || [], previewWindow, params);
                    } else {
                        generatePrintLayout(res.data.data, res.data.school_info, res.data.routines || [], previewWindow);
                    }
                }).catch(err => {
                    previewWindow.close();
                    console.error(err);
                    Swal.fire('Error', 'Failed to fetch data. Please try again.', 'error');
                });
            }
        }

        function getRoutinesForCard(card, allRoutines) {
            const norm = (v) => {
                if (!v || v === '-' || v === 'null' || v === 'undefined') return '';
                return v.toString().trim().toLowerCase();
            };

            const cardClass = norm(card.class_name);
            const cardSession = norm(card.session_name);
            const cardExam = norm(card.exam_name);
            const cardGroup = norm(card.group_name);
            const cardSection = norm(card.section_name);

            // Filter routines by class, session, and exam name
            let candidates = allRoutines.filter(r =>
                norm(r.class_name) === cardClass &&
                norm(r.session_name) === cardSession &&
                norm(r.exam_name) === cardExam
            );

            if (candidates.length === 0) return [];

            // Try to match specific group AND section
            let matchGroupAndSection = candidates.filter(r =>
                norm(r.group_name) === cardGroup &&
                norm(r.section_name) === cardSection
            );
            if (matchGroupAndSection.length > 0) return matchGroupAndSection;

            // Try to match specific group and ANY section (meaning routine section is empty/null/'-')
            let matchGroupAnySection = candidates.filter(r =>
                norm(r.group_name) === cardGroup &&
                (norm(r.section_name) === '')
            );
            if (matchGroupAnySection.length > 0) return matchGroupAnySection;

            // Try to match ANY group and specific section
            let matchAnyGroupSpecificSection = candidates.filter(r =>
                (norm(r.group_name) === '') &&
                norm(r.section_name) === cardSection
            );
            if (matchAnyGroupSpecificSection.length > 0) return matchAnyGroupSpecificSection;

            // Fallback to general class routine (group and section are empty/null/'-')
            let generalClassRoutine = candidates.filter(r =>
                (norm(r.group_name) === '') &&
                (norm(r.section_name) === '')
            );
            if (generalClassRoutine.length > 0) return generalClassRoutine;

            return candidates;
        }

        function escapeAdmitCardHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getAdmitCardPrintData(card, school, address) {
            const rawStudentImage = card.student_image
                ? (String(card.student_image).startsWith('http')
                    ? String(card.student_image)
                    : `${window.location.origin}/storage/${card.student_image}`)
                : '';

            return {
                logo: escapeAdmitCardHtml(school?.logo || ''),
                schoolName: escapeAdmitCardHtml(school?.school_name || 'School Name'),
                mobile: escapeAdmitCardHtml(school?.mobile || ''),
                email: escapeAdmitCardHtml(school?.email || ''),
                address: escapeAdmitCardHtml(address || ''),
                studentImage: escapeAdmitCardHtml(rawStudentImage),
                principalSignature: escapeAdmitCardHtml(school?.principal_signature || ''),
                studentName: escapeAdmitCardHtml(card.student_name || '-'),
                studentId: escapeAdmitCardHtml(card.student_id_number || '-'),
                fatherName: escapeAdmitCardHtml(card.father_name || '-'),
                admitCardNumber: escapeAdmitCardHtml(card.admit_card_number || '-'),
                className: escapeAdmitCardHtml(card.class_name || '-'),
                groupName: escapeAdmitCardHtml(card.group_name || '-'),
                sectionName: escapeAdmitCardHtml(card.section_name || '-'),
                sessionName: escapeAdmitCardHtml(card.session_name || '-'),
                examName: escapeAdmitCardHtml(card.exam_name || '-'),
            };
        }

        function getAdmitCardUtilityStyles() {
            return `
                .relative { position: relative; }
                .absolute { position: absolute; }
                .inset-0 { inset: 0; }
                .flex { display: flex; }
                .flex-col { flex-direction: column; }
                .flex-grow { flex-grow: 1; min-width: 0; }
                .flex-shrink-0 { flex-shrink: 0; }
                .items-center { align-items: center; }
                .items-start { align-items: flex-start; }
                .items-end { align-items: flex-end; }
                .items-baseline { align-items: baseline; }
                .justify-between { justify-content: space-between; }
                .justify-center { justify-content: center; }
                .justify-end { justify-content: flex-end; }
                .justify-start { justify-content: flex-start; }
                .h-full { height: 100%; }
                .h-9 { height: 36px; }
                .h-14 { height: 56px; }
                .h-16 { height: 64px; }
                .w-full { width: 100%; }
                .w-16 { width: 64px; }
                .w-32 { width: 128px; }
                .w-64 { width: 256px; }
                .w-\\[48\\%\\] { width: 48%; }
                .max-h-9 { max-height: 36px; }
                .max-w-\\[95px\\] { max-width: 95px; }
                .gap-1 { gap: 4px; }
                .gap-4 { gap: 16px; }
                .space-y-1 > * + * { margin-top: 4px; }
                .p-1 { padding: 4px; }
                .p-1\\.5 { padding: 6px; }
                .p-5 { padding: 20px; }
                .px-2 { padding-left: 8px; padding-right: 8px; }
                .px-4 { padding-left: 16px; padding-right: 16px; }
                .py-0\\.5 { padding-top: 2px; padding-bottom: 2px; }
                .pb-2 { padding-bottom: 8px; }
                .pt-0\\.5 { padding-top: 2px; }
                .mb-0\\.5 { margin-bottom: 2px; }
                .mb-3 { margin-bottom: 12px; }
                .mt-0\\.5 { margin-top: 2px; }
                .mt-2 { margin-top: 8px; }
                .mt-4 { margin-top: 16px; }
                .text-center { text-align: center; }
                .text-left { text-align: left; }
                .text-xl { font-size: 20px; }
                .text-xs { font-size: 12px; }
                .text-gray-900 { color: #111827; }
                .text-gray-800 { color: #1f2937; }
                .text-gray-650 { color: #475569; }
                .text-gray-600 { color: #4b5563; }
                .text-gray-500 { color: #6b7280; }
                .text-gray-400 { color: #9ca3af; }
                .text-slate-800 { color: #1e293b; }
                .text-slate-700 { color: #334155; }
                .text-slate-500 { color: #64748b; }
                .text-blue-900 { color: #1e3a8a; }
                .font-bold { font-weight: 700; }
                .font-semibold { font-weight: 600; }
                .font-medium { font-weight: 500; }
                .font-mono { font-family: Consolas, 'Courier New', monospace; }
                .uppercase { text-transform: uppercase; }
                .capitalize { text-transform: capitalize; }
                .tracking-wide { letter-spacing: .025em; }
                .tracking-wider { letter-spacing: .05em; }
                .leading-tight { line-height: 1.25; }
                .leading-relaxed { line-height: 1.625; }
                .whitespace-nowrap { white-space: nowrap; }
                .overflow-hidden { overflow: hidden; }
                .pointer-events-none { pointer-events: none; }
                .z-0 { z-index: 0; }
                .z-10 { z-index: 10; }
                .opacity-\\[0\\.06\\] { opacity: .06; }
                .object-cover { object-fit: cover; }
                .object-contain { object-fit: contain; }
                .border { border: 1px solid #d1d5db; }
                .border-b { border-bottom-width: 1px; border-bottom-style: solid; }
                .border-t { border-top-width: 1px; border-top-style: solid; }
                .border-gray-800 { border-color: #1f2937; }
                .border-gray-400 { border-color: #9ca3af; }
                .border-gray-300 { border-color: #d1d5db; }
                .border-slate-800 { border-color: #1e293b; }
                .border-dashed { border-style: dashed; }
                .bg-gray-100 { background-color: #f3f4f6; }
                .bg-gray-50 { background-color: #f9fafb; }
                .shrink-0 { flex-shrink: 0; }
                .routine-table { width: 100%; table-layout: fixed; border-collapse: collapse; }
                .routine-table th, .routine-table td { border: 1px solid #1e293b; padding: 6px; }
                .routine-table th { background: #f3f4f6; font-weight: 700; white-space: nowrap; }
                .routine-table .routine-date { width: 18%; text-align: center; white-space: nowrap; }
                .routine-table .routine-time { width: 27%; text-align: center; white-space: nowrap; }
                .routine-table .routine-duration { width: 20%; text-align: center; white-space: nowrap; }
                .routine-table .routine-subject { width: 35%; text-align: left; overflow-wrap: anywhere; word-break: break-word; }
                .flex.items-baseline > span:last-child { min-width: 0; overflow-wrap: anywhere; word-break: break-word; }
                [class~="text-[9px]"] { font-size: 9px; }
                [class~="text-[9.5px]"] { font-size: 9.5px; }
                [class~="text-[10px]"] { font-size: 10px; }
                [class~="text-[11px]"] { font-size: 11px; }
            `;
        }

        function buildRoutineTableRows(cardRoutines) {
            let rowsHtml = '';
            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = d.getDate();
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
                const month = months[d.getMonth()];
                const year = d.getFullYear().toString().substring(2);
                return `${day}-${month}-${year}`;
            };
            const formatTime = (timeStr) => {
                if (!timeStr) return '-';

                const rawTime = String(timeStr).trim();
                if (/\b(?:AM|PM)\b/i.test(rawTime)) return rawTime;

                const [rawHour, rawMinute] = rawTime.split(':');
                const hour = Number(rawHour);
                const minute = Number(rawMinute);
                if (!Number.isInteger(hour) || !Number.isInteger(minute)) return rawTime;

                const period = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${String(minute).padStart(2, '0')} ${period}`;
            };

            if (!cardRoutines || cardRoutines.length === 0) {
                return `
                                                                                                                <tr>
                                                                                                                    <td class="border border-slate-800 p-1.5 font-mono text-center">-</td>
                                                                                                                    <td class="border border-slate-800 p-1.5 font-mono text-center">-</td>
                                                                                                                     <td class="border border-slate-800 p-1.5 font-mono text-center">-</td>
                                                                                                                    <td class="border border-slate-800 p-1.5 text-left px-2 font-semibold">-</td>
                                                                                                                </tr>
                                                                                                            `;
            }

            for (const item of cardRoutines) {
                const date = escapeAdmitCardHtml(formatDate(item.exam_date));
                const time = escapeAdmitCardHtml(`${formatTime(item.start_time)} - ${formatTime(item.end_time)}`);
                const duration = escapeAdmitCardHtml(item.total_hours || '-');
                const subject = escapeAdmitCardHtml(item.subject_name || '-');
                rowsHtml += `
                    <tr>
                        <td class="routine-date font-mono">${date}</td>
                        <td class="routine-time font-mono">${time}</td>
                        <td class="routine-duration font-mono">${duration}</td>
                        <td class="routine-subject font-semibold">${subject}</td>
                    </tr>
                `;
            }

            return rowsHtml;
        }



        function generateMobilePreview(admitCards, school, routines, previewWindow, exportParams = {}) {
            if (!previewWindow) previewWindow = window.open('', '_blank');
            const address = school?.full_address || [school?.village, school?.upazila, school?.district, school?.division]
                .filter(Boolean)
                .join(', ');

            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = today.toLocaleString('default', { month: 'short' });
            const yyyy = today.getFullYear();
            const currentDate = `${dd}-${mm}-${yyyy}`;
            const pdfQuery = new URLSearchParams(Object.entries(exportParams).filter(([, value]) => value !== '' && value != null));

            let html = `<!DOCTYPE html><html><head><title>Admit Card PDF</title>
                <style>${getAdmitCardUtilityStyles()}</style>
                <style>
                    @page { size: A4 portrait; margin: 0; }
                    * { border-radius: 0 !important; font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; }
                    body { margin: 0; padding: 0; background: #f3f4f6; }
                    .card-page {
                        width: 100vw;
                        min-height: 100vh;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        padding: 20px;
                        border-bottom: 2px dashed #d1d5db;
                        background: #f3f4f6;
                    }
                    .card-inner {
                        width: 100%;
                        max-width: 650px;
                        background: white;
                        padding: 20px;
                        position: relative;
                        border: 1px solid #1f2937;
                    }
                    .no-print { display: block; }
                    @media print {
                        body { background: #fff; -webkit-print-color-adjust: exact; }
                        .no-print { display: none !important; }
                        .card-page { min-height: 297mm; width: 210mm; padding: 12mm 15mm; border: none; page-break-after: always; background: #fff; }
                        .card-inner { max-width: 100%; padding: 0; border: 1px solid #1f2937; }
                    }
                </style><\/head><body>`;

            admitCards.forEach(card => {
                const cardRoutines = getRoutinesForCard(card, routines);
                const routineRowsHtml = buildRoutineTableRows(cardRoutines);
                const printData = getAdmitCardPrintData(card, school, address);
                const cardPdfQuery = new URLSearchParams(pdfQuery);
                cardPdfQuery.set('admit_card_id', card.id);
                const cardDownloadPdfUrl = `${window.location.origin}/api/school-exam-admit-cards/export-pdf?${cardPdfQuery.toString()}`;

                html += `
                                                                                                                <div class="card-page">
                                                                                                                    <div class="card-inner">
                                                                                                                        <!-- Watermark -->
                                                                                                                        ${printData.logo ? `
                                                                                                                           <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.06] z-0">
                                                                                                                                <img src="${printData.logo}" class="w-64 h-64 object-contain" alt="">
                                                                                                                            </div>
                                                                                                                        ` : ''}

                                                                                                                        <div class="relative z-10 flex flex-col justify-between h-full w-full">
                                                                                                                            <!-- Header -->
                                                                                                                            <div class="border-b border-gray-400 pb-2 mb-3">
                                                                                                                                <div class="flex justify-between items-center gap-4">
                                                                                                                                    <!-- Left: School logo -->
                                                                                                                                    <div class="w-16 h-16 flex-shrink-0 flex items-center justify-start">
                                                                                                                                        ${printData.logo ? `<img src="${printData.logo}" class="h-16 w-16 object-cover border border-gray-300" alt="School logo" style="border-radius: 50% !important;">` : `
                                                                                                                                            <div class="h-16 w-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400" style="border-radius: 50% !important;">Logo</div>
                                                                                                                                        `}
                                                                                                                                    </div>

                                                                                                                                    <!-- Middle: School details -->
                                                                                                                                    <div class="text-center flex-grow px-2">
                                                                                                                                        <h1 class="text-xl font-bold text-gray-900 tracking-wide uppercase leading-tight">${printData.schoolName}</h1>
                                                                                                                                        <p class="text-xs text-gray-650 font-bold mt-0.5">
                                                                                                                                            ${printData.mobile} ${printData.mobile && printData.email ? ' | ' : ''} ${printData.email}
                                                                                                                                        </p>
                                                                                                                                        <p class="text-xs text-gray-500 font-semibold leading-tight">${printData.address}</p>
                                                                                                                                    </div>

                                                                                                                                    <!-- Right: Student Photo -->
                                                                                                                                    <div class="w-16 h-16 flex-shrink-0 flex items-center justify-end">
                                                                                                                                        ${printData.studentImage ? `
                                                                                                                                            <img src="${printData.studentImage}" class="w-16 h-16 border border-slate-800 object-cover" alt="Student photo" style="border-radius: 50% !important;">
                                                                                                                                        ` : `
                                                                                                                                            <div class="w-16 h-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400 bg-gray-50" style="border-radius: 50% !important;">Photo</div>
                                                                                                                                        `}
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                <div class="text-center mt-2">
                                                                                                                                    <span class="border border-gray-800 bg-gray-100 text-gray-900 text-xs font-bold px-4 py-0.5 tracking-wider uppercase">ADMIT CARD</span>
                                                                                                                                </div>
                                                                                                                            </div>

                                                                                                                            <!-- Student Info -->
                                                                                                                            <div class="flex justify-between items-start text-[11px] text-gray-900 mb-3 leading-relaxed">
                                                                                                                                <div class="w-[48%] space-y-1 text-left">
                                                                                                                                     <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Student Name</span><span>:</span><span class="font-bold capitalize">${printData.studentName}</span></div>
                                                                                                                                     <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Student ID</span><span>:</span><span class="font-mono font-bold">${printData.studentId}</span></div>
                                                                                                                                     <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Father's Name</span><span>:</span><span class="capitalize">${printData.fatherName}</span></div>
                                                                                                                                     <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Admit Card No</span><span>:</span><span class="font-mono">${printData.admitCardNumber}</span></div>
                                                                                                                                </div>
                                                                                                                                <div class="w-[48%] space-y-1 text-right">
                                                                                                                                     <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Class</span><span>:</span><span class="font-bold capitalize text-left flex-grow">${printData.className}</span></div>
                                                                                                                                     <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Group</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${printData.groupName}</span></div>
                                                                                                                                     <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Section</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${printData.sectionName}</span></div>
                                                                                                                                     <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Session</span><span>:</span><span class="font-mono text-left flex-grow">${printData.sessionName}</span></div>
                                                                                                                                     <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Exam Name</span><span>:</span><span class="font-bold text-blue-900 capitalize text-left flex-grow">${printData.examName}</span></div>
                                                                                                                                </div>
                                                                                                                            </div>

                                                                                                                            <!-- Routine Table -->
                                                                                                                            <div class="overflow-hidden">
                                                                                                                                <table class="routine-table text-center border border-slate-800 text-[9.5px]">
                                                                                                                                    <thead>
                                                                                                                                        <tr class="bg-gray-100 font-bold text-gray-800 whitespace-nowrap">
                                                                                                                                            <th class="routine-date">Date</th>
                                                                                                                                            <th class="routine-time">Time</th>
                                                                                                                                            <th class="routine-duration">Duration</th>
                                                                                                                                            <th class="routine-subject">Subject</th>
                                                                                                                                        </tr>
                                                                                                                                    </thead>
                                                                                                                                    <tbody class="font-medium text-gray-900 whitespace-nowrap">
                                                                                                                                        ${routineRowsHtml}
                                                                                                                                    </tbody>
                                                                                                                                </table>
                                                                                                                            </div>

                                                                                                                            <!-- Footer -->
                                                                                                                            <div class="flex justify-between items-end text-[10px] font-semibold text-slate-700 mt-4 mb-0.5">
                                                                                                                                <div class="text-center w-32 flex flex-col items-center justify-end h-14">
                                                                                                                                    <p class="font-mono text-slate-900 mb-0.5 text-[9px]">${currentDate}</p>
                                                                                                                                    <p class="border-t border-slate-800 pt-0.5 w-full text-[9px] text-slate-500 font-bold">Issue Date</p>
                                                                                                                                </div>
                                                                                                                                <div class="text-center w-32 flex flex-col items-center justify-end h-14">
                                                                                                                                        ${printData.principalSignature ? `
                                                                                                                                            <img src="${printData.principalSignature}" class="max-h-9 max-w-[95px] object-contain mb-0.5" alt="Principal signature">
                                                                                                                                    ` : `<div class="h-9"></div>`}
                                                                                                                                    <p class="border-t border-slate-800 pt-0.5 w-full text-[9px] text-slate-500 font-bold">Principal</p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <!-- PDF download action (hidden in printed output) -->
                                                                                                                    <div class="no-print flex justify-center mt-4">
                                                                                                                        <button onclick='window.location.href=${JSON.stringify(cardDownloadPdfUrl)}' style="border:2px solid #000;background:#fff;color:#000;padding:8px 24px;font-size:10px;font-weight:900;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;" onmouseover="this.style.background='#000';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#000'">
                                                                                                                            Download PDF
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                </div>`;
            });

            html += `<\/body></html>`;
            previewWindow.document.open();
            previewWindow.document.write(html);
            previewWindow.document.close();
        }

        function generatePrintLayout(admitCards, school, routines, printWindow) {
            if (!printWindow) printWindow = window.open('', '_blank');
            const address = school?.full_address || [school?.village, school?.upazila, school?.district, school?.division]
                .filter(Boolean)
                .join(', ');

            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = today.toLocaleString('default', { month: 'short' });
            const yyyy = today.getFullYear();
            const currentDate = `${dd}-${mm}-${yyyy}`;

            let html = `<html><head><title>Print Admit Cards</title>
                                                                                                                <style>${getAdmitCardUtilityStyles()}</style>
                                                                                                                <style>
                                                                                                                    @page { size: A4; margin: 0; }
                                                                                                                    * { border-radius: 0 !important; font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; }
                                                                                                                    body { margin: 0; padding: 0; background: #fff; }
                                                                                                                    .print-page {
                                                                                                                        width: 210mm;
                                                                                                                        height: 297mm;
                                                                                                                        background: white;
                                                                                                                        padding: 12mm 15mm;
                                                                                                                        box-sizing: border-box;
                                                                                                                        display: flex;
                                                                                                                        flex-direction: column;
                                                                                                                        justify-content: space-between;
                                                                                                                        page-break-after: always;
                                                                                                                    }
                                                                                                                    @media print {
                                                                                                                        body { -webkit-print-color-adjust: exact; background-color: #ffffff; }
                                                                                                                        .print-page {
                                                                                                                            box-shadow: none !important;
                                                                                                                            margin: 0 !important;
                                                                                                                            padding: 12mm 15mm !important;
                                                                                                                            width: 210mm !important;
                                                                                                                            height: 297mm !important;
                                                                                                                        }
                                                                                                                    }
                                                                                                                </style><\/head><body>`;

            admitCards.forEach((card, index) => {
                if (index % 2 === 0) html += '<div class="print-page">';

                const cardRoutines = getRoutinesForCard(card, routines);
                const routineRowsHtml = buildRoutineTableRows(cardRoutines);
                const printData = getAdmitCardPrintData(card, school, address);

                html += `
                                                                                                                <div class="relative flex flex-col justify-between overflow-hidden p-5" style="height: 133mm; border: 1px solid #1f2937; box-sizing: border-box;">
                                                                                                                    <!-- Watermark -->
                                                                                                                     ${printData.logo ? `
                                                                                                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.06] z-0">
                                                                                                                             <img src="${printData.logo}" class="w-64 h-64 object-contain" alt="">
                                                                                                                        </div>
                                                                                                                    ` : ''}

                                                                                                                    <div class="relative z-10 flex flex-col justify-between h-full w-full">
                                                                                                                        <!-- Upper Header Section with school logo left, centered details, student photo right -->
                                                                                                                        <div class="border-b border-gray-400 pb-2 mb-3">
                                                                                                                            <div class="flex justify-between items-center gap-4">
                                                                                                                                <!-- Left: School logo -->
                                                                                                                                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-start">
                                                                                                                                     ${printData.logo ? `<img src="${printData.logo}" class="h-16 w-16 object-cover border border-gray-300" alt="School logo" style="border-radius: 50% !important;">` : `
                                                                                                                                        <div class="h-16 w-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400" style="border-radius: 50% !important;">Logo</div>
                                                                                                                                    `}
                                                                                                                                </div>

                                                                                                                                <!-- Middle: Center School details & Badge -->
                                                                                                                                <div class="text-center flex-grow px-2">
                                                                                                                                     <h1 class="text-xl font-bold text-gray-900 tracking-wide uppercase leading-tight">${printData.schoolName}</h1>
                                                                                                                                    <p class="text-xs text-gray-650 font-bold mt-0.5">
                                                                                                                                         ${printData.mobile} ${printData.mobile && printData.email ? ' | ' : ''} ${printData.email}
                                                                                                                                    </p>
                                                                                                                                     <p class="text-xs text-gray-500 font-semibold leading-tight">${printData.address}</p>
                                                                                                                                </div>

                                                                                                                                <!-- Right: Student Photo (Upper Right) -->
                                                                                                                                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-end">
                                                                                                                                     ${printData.studentImage ? `
                                                                                                                                         <img src="${printData.studentImage}" class="w-16 h-16 border border-slate-800 object-cover" alt="Student photo" style="border-radius: 50% !important;">
                                                                                                                                    ` : `
                                                                                                                                        <div class="w-16 h-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400 bg-gray-50" style="border-radius: 50% !important;">
                                                                                                                                            Photo
                                                                                                                                        </div>
                                                                                                                                    `}
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="text-center mt-2">
                                                                                                                                <span class="border border-gray-800 bg-gray-100 text-gray-900 text-xs font-bold px-4 py-0.5 tracking-wider uppercase">
                                                                                                                                    ADMIT CARD
                                                                                                                                </span>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <!-- Student Info: 2-column layout (Left/Right both left-aligned) -->
                                                                                                                        <div class="flex justify-between items-start text-[11px] text-gray-900 mb-3 leading-relaxed">
                                                                                                                            <!-- Left Info (48%) -->
                                                                                                                            <div class="w-[48%] space-y-1 text-left">
                                                                                                                                 <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Student Name</span><span>:</span><span class="font-bold capitalize">${printData.studentName}</span></div>
                                                                                                                                 <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Student ID</span><span>:</span><span class="font-mono font-bold">${printData.studentId}</span></div>
                                                                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Father's Name</span><span>:</span><span class="capitalize">${printData.fatherName}</span></div>
                                                                                                                                 <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Admit Card No</span><span>:</span><span class="font-mono">${printData.admitCardNumber}</span></div>
                                                                                                                            </div>

                                                                                                                            <!-- Right Info (48%) -->
                                                                                                                            <div class="w-[48%] space-y-1 text-right">
                                                                                                                                 <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Class</span><span>:</span><span class="font-bold capitalize text-left flex-grow">${printData.className}</span></div>
                                                                                                                                 <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Group</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${printData.groupName}</span></div>
                                                                                                                                 <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Section</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${printData.sectionName}</span></div>
                                                                                                                                 <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Session</span><span>:</span><span class="font-mono text-left flex-grow">${printData.sessionName}</span></div>
                                                                                                                                 <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Exam Name</span><span>:</span><span class="font-bold text-blue-900 capitalize text-left flex-grow">${printData.examName}</span></div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <!-- Routine Table -->
                                                                                                                        <div class="overflow-hidden">
                                                                                                                                <table class="routine-table text-center border border-slate-800 text-[9.5px]">
                                                                                                                                <thead>
                                                                                                                                    <tr class="bg-gray-100 font-bold  text-gray-800 whitespace-nowrap">
                                                                                                                                        <th class="routine-date">Date</th>
                                                                                                                                        <th class="routine-time">Time</th>
                                                                                                                                       <th class="routine-duration">Duration</th>
                                                                                                                                        <th class="routine-subject">Subject</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody class="font-medium text-gray-900 whitespace-nowrap">
                                                                                                                                ${routineRowsHtml}
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </div>

                                                                                                                        <!-- Footer Signature & Current Date -->
                                                                                                                        <div class="flex justify-between items-end text-[10px] font-semibold text-slate-700 mt-4 mb-0.5">
                                                                                                                            <div class="text-center w-32 flex flex-col items-center justify-end h-14">
                                                                                                                                <p class="font-mono text-slate-900 mb-0.5 text-[9px]">${currentDate}</p>
                                                                                                                                <p class="border-t border-slate-800 pt-0.5 w-full text-[9px] text-slate-500 font-bold">Issue Date</p>
                                                                                                                            </div>
                                                                                                                            <div class="text-center w-32 flex flex-col items-center justify-end h-14">
                                                                                                                                 ${printData.principalSignature ? `
                                                                                                                                     <img src="${printData.principalSignature}" class="max-h-9 max-w-[95px] object-contain mb-0.5" alt="Principal signature">
                                                                                                                                ` : `
                                                                                                                                    <div class="h-9"></div>
                                                                                                                                `}
                                                                                                                                <p class="border-t border-slate-800 pt-0.5 w-full text-[9px] text-slate-500 font-bold">Principal</p>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>`;

                if ((index + 1) % 2 === 0 || index === admitCards.length - 1) {
                    html += '</div>';
                }
            });

            html += `
                                                                                                            <script>
                                                                                                                window.addEventListener('load', function() {
                                                                                                                    setTimeout(() => {
                                                                                                                        window.print();
                                                                                                                    }, 800);

                                                                                                                    window.onafterprint = function() {
                                                                                                                        window.close();
                                                                                                                    };
                                                                                                                });
                                                                                                            <\/script>
                                                                                                            <\/body></html>`;
            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
        }

        async function editAdmit(item) {
            openAdmitModal();
            const modalTitle = document.getElementById('admitModalTitle');
            if (modalTitle) modalTitle.innerText = 'Edit Individual Admit Card';
            document.getElementById('admit_edit_id').value = item.id;
            document.getElementById('submitBtn').innerText = 'Update Admit Card';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('studentStatusBox').classList.add('hidden');

            await loadInitial();
            const classEl = document.getElementById('class_name');
            setDropdownValue('class_name', item.class_name, item.class_name);
            await handleCascade(classEl, 'group');
            const groupEl = document.getElementById('group_name');
            setDropdownValue('group_name', item.group_name || '', item.group_name || 'Select Group');
            await handleCascade(groupEl, 'section');
            const secEl = document.getElementById('section_name');
            setDropdownValue('section_name', item.section_name || '', item.section_name || 'Select Section');
            await handleCascade(secEl, 'session');
            setDropdownValue('session_name', item.session_name, item.session_name);

            await fetchFilteredExams(false);
            setDropdownValue('exam_name', item.exam_name, item.exam_name);
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (info) info.innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            controls.innerHTML +=
                `<button class="pagination-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchTable(${meta.current_page - 1})"><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    controls.innerHTML +=
                        `<button class="pagination-btn ${meta.current_page === i ? 'active' : ''}" onclick="fetchTable(${i})">${i}</button>`;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    controls.innerHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }
            controls.innerHTML +=
                `<button class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTable(${meta.current_page + 1})"><i class="mdi mdi-chevron-right"></i></button>`;
        }

        document.getElementById('admitForm').onsubmit = function (e) {
            e.preventDefault();
            const editId = document.getElementById('admit_edit_id').value;
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('btnSpinner').classList.remove('hidden');
            document.getElementById('btnText').textContent = 'Processing...';

            const payload = {
                class_name: document.getElementById('class_name').value,
                group_name: document.getElementById('group_name').value,
                section_name: document.getElementById('section_name').value,
                session_name: document.getElementById('session_name').value,
                exam_name: document.getElementById('exam_name').value,
                students: studentsList
            };

            const request = editId ? axios.put(`/api/school-exam-admit-cards/${editId}`, payload) : axios.post(
                '/api/school-exam-admit-cards', payload);

            request.then(res => {
                Toastify({
                    text: res.data.message,
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeAdmitModal();
                fetchTable(1);
            }).catch(err => {
                const msg = err.response?.data?.status === 'exists' ? err.response.data.message :
                    'Operation failed';
                Swal.fire('Warning', msg, 'warning');
            }).finally(() => {
                btn.disabled = false;
                document.getElementById('btnSpinner').classList.add('hidden');
                document.getElementById('btnText').textContent = 'Generate';
            });
        };

        function deleteAdmit(id) {
            Swal.fire({
                title: 'Remove Admit Card?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444'
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-exam-admit-cards/${id}`).then(() => fetchTable());
            });
        }

        function toggleFilterModal() {
            document.getElementById('filterModal').classList.toggle('hidden');
        }

        function applyFilters() {
            fetchTable(1);
            toggleFilterModal();
        }

        function restoreAdmitSearch() {
            const desktopSearch = document.getElementById('header_search');
            const mobileSearch = document.getElementById('header_search_mobile');

            if (desktopSearch) desktopSearch.value = '';
            if (mobileSearch) mobileSearch.value = '';

            fetchTable(1);
        }

        function resetFilters() {
            const dropdowns = {
                filter_class_name: 'Select Class',
                filter_group_name: 'Select Group',
                filter_section_name: 'Select Section',
                filter_session_name: 'Select Session',
                filter_exam_name: 'Select Exam',
            };

            Object.entries(dropdowns).forEach(([id, placeholder]) => {
                setDropdownValue(id, '', placeholder);
            });

            restoreAdmitSearch();
            toggleFilterModal();
        }

        function openAdmitModal() {
            document.getElementById('admitForm').reset();
            document.getElementById('admit_edit_id').value = '';
            const modalTitle = document.getElementById('admitModalTitle');
            if (modalTitle) modalTitle.innerText = 'Bulk Admit Card Generator';
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
            document.getElementById('btnText').innerText = 'Generate All Cards';
            setDropdownValue('class_name', '', 'Select Class');
            setDropdownValue('group_name', '', 'Select Group');
            setDropdownValue('section_name', '', 'Select Section');
            setDropdownValue('session_name', '', 'Select Session');
            setDropdownValue('exam_name', '', 'Select Exam');
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('admitModal').classList.remove('hidden');
        }

        function closeAdmitModal() {
            document.getElementById('admitModal').classList.add('hidden');
        }

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('#btnAdmitExport');
            const menu = document.getElementById('admitExportDropdown');

            if (trigger && menu) {
                event.stopPropagation();
                menu.classList.toggle('hidden');
                trigger.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
                return;
            }

            if (!event.target.closest('#admitExportDropdown') && menu) {
                menu.classList.add('hidden');
                document.getElementById('btnAdmitExport')?.setAttribute('aria-expanded', 'false');
            }
        });

        document.getElementById('class_name')?.addEventListener('change', function() {
            handleCascade(this, 'group');
        });
        document.getElementById('group_name')?.addEventListener('change', function() {
            handleCascade(this, 'section');
        });
        document.getElementById('section_name')?.addEventListener('change', function() {
            handleCascade(this, 'session');
        });
        document.getElementById('session_name')?.addEventListener('change', function() {
            fetchStudentCount();
            checkPrerequisiteStatus();
        });
        document.getElementById('exam_name')?.addEventListener('change', checkPrerequisiteStatus);
        document.getElementById('filter_class_name')?.addEventListener('change', function() {
            handleCascade(this, 'filter_group');
        });
        document.getElementById('filter_group_name')?.addEventListener('change', function() {
            handleCascade(this, 'filter_section');
        });
        document.getElementById('filter_section_name')?.addEventListener('change', function() {
            handleCascade(this, 'filter_session');
        });
        document.getElementById('filter_session_name')?.addEventListener('change', function() {
            fetchFilteredExams(true);
        });

        function initializeAdmitCardPage() {
            loadInitial();
            fetchTable();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAdmitCardPage);
        } else {
            initializeAdmitCardPage();
        }
    </script>
@endsection
