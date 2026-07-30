@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
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

        .loader-row {
            text-align: center;
            padding: 2rem !important;
            color: #64748b;
            font-style: italic;
        }

        .page-link-premium {
            padding: 5px 12px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: bold;
            color: #64748b;
            transition: all 0.2s;
            background: white;
        }

        .page-link-premium:hover:not(.disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .page-link-premium.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .page-link-premium.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <x-school.list-header title="Exam Routine" breadcrumb-current="Exam Routine" keep-title>
                <x-slot:search>
                    <x-input.search
                        id="tableSearch"
                        placeholder="Search Subject or Date..."
                        class="w-72"
                        onkeyup="syncRoutineSearch('tableSearch'); liveSearch()"
                    />
                    <x-button.secondary type="button" onclick="restoreRoutineSearch()">
                        Restore
                    </x-button.secondary>
                </x-slot:search>

                <x-slot:actions>
                    <x-button.secondary type="button" onclick="openFilterModal()" class="w-full lg:w-auto">
                        Filter
                    </x-button.secondary>

                    <x-dropdown
                        id="exportDropdown"
                        button-id="exportDropdownButton"
                        menu-id="exportMenu"
                        label="Export"
                        align="full"
                    >
                        <x-dropdown.item onclick="exportData('pdf')">PDF</x-dropdown.item>
                        <x-dropdown.item onclick="exportData('excel')">Excel</x-dropdown.item>
                        <x-dropdown.item onclick="window.print(); closeExportMenu()">Print</x-dropdown.item>
                    </x-dropdown>

                    <x-button.primary type="button" onclick="openRoutineModal()" class="w-full lg:w-auto">
                        Create Routine
                    </x-button.primary>
                </x-slot:actions>

                <x-slot:mobile-search>
                    <div class="col-span-3 grid grid-cols-3 gap-2">
                        <x-input.search
                            id="tableSearchMobile"
                            placeholder="Search Subject or Date..."
                            class="col-span-2 min-w-0"
                            onkeyup="syncRoutineSearch('tableSearchMobile'); liveSearch()"
                        />
                        <x-button.secondary type="button" onclick="restoreRoutineSearch()" class="w-full">
                            Restore
                        </x-button.secondary>
                    </div>
                </x-slot:mobile-search>
            </x-school.list-header>

            {{-- Filter Modal --}}
            <x-modal.form
                id="filterModal"
                form-id="routineFilterForm"
                title="Routine Filter"
                close-button-id="closeRoutineFilterModal"
                title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
            >
                <div class="relative">
                    <x-input.dropdown-select id="f_class" placeholder="All Classes" :options="[]" />
                    <x-input.floating-label for="f_class" :floating="false">Class</x-input.floating-label>
                </div>

                <div class="relative">
                    <x-input.dropdown-select id="f_group" placeholder="All Groups" :options="[]" />
                    <x-input.floating-label for="f_group" :floating="false">Group</x-input.floating-label>
                </div>

                <div class="relative">
                    <x-input.dropdown-select id="f_section" placeholder="All Sections" :options="[]" />
                    <x-input.floating-label for="f_section" :floating="false">Section</x-input.floating-label>
                </div>

                <div class="relative">
                    <x-input.dropdown-select id="f_session" placeholder="All Sessions" :options="[]" />
                    <x-input.floating-label for="f_session" :floating="false">Session</x-input.floating-label>
                </div>

                <div class="relative md:col-span-2">
                    <x-input.dropdown-select id="f_exam" placeholder="All Exams" :options="[]" />
                    <x-input.floating-label for="f_exam" :floating="false">Exam</x-input.floating-label>
                </div>

                <x-slot:footer>
                    <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                        <x-button.secondary type="button" onclick="resetFilters()" class="w-full">
                            Reset
                        </x-button.secondary>
                        <x-button.primary
                            type="button"
                            onclick="fetchRoutines(1, true); closeFilterModal()"
                            class="w-full"
                        >
                            Apply
                        </x-button.primary>
                    </div>
                </x-slot:footer>
            </x-modal.form>

            <x-school.data-table
                :empty="false"
                :empty-colspan="12"
                empty-message="No routines found."
                show-footer="true"
                min-width="1320px"
                tbody-id="routineTableBody"
            >
                <x-slot:columns>
                    <colgroup>
                        <col style="width: 50px;">
                        <col style="width: 95px;">
                        <col style="width: 95px;">
                        <col style="width: 95px;">
                        <col style="width: 95px;">
                        <col style="width: 135px;">
                        <col style="width: 160px;">
                        <col style="width: 110px;">
                        <col style="width: 105px;">
                        <col style="width: 110px;">
                        <col style="width: 110px;">
                        <col style="width: 110px;">
                    </colgroup>
                </x-slot:columns>

                <x-slot:head>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">SL</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Class</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Group</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Section</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Session</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Exam</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Subject</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Date</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-left font-semibold">Day Name</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Start Time</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">End Time</x-table.th>
                    <x-table.th unstyled class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-semibold">Action</x-table.th>
                </x-slot:head>

                <x-slot:footer>
                    <div id="paginationControls" class="pagination-container flex w-full items-center justify-between px-2">
                        <div id="paginationInfo" class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            0 of 0
                        </div>
                        <div id="paginationLinks" class="flex items-center gap-1"></div>
                    </div>
                </x-slot:footer>
            </x-school.data-table>
        </div>
    </div>

    {{-- Exam Routine Modal --}}
    <x-modal.form
        id="routineModal"
        form-id="routineForm"
        title="Create Exam Routine"
        close-button-id="closeRoutineModalButton"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    >
        <input type="hidden" id="routine_edit_id">

        <div class="relative">
            <x-input.control
                type="date"
                id="exam_date"
                name="exam_date"
                class="peer placeholder:text-transparent"
                placeholder=" "
                onchange="updateDayName()"
            />
            <x-input.floating-label for="exam_date" :floating="false">Date</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                id="day_name"
                name="day_name"
                class="peer bg-slate-50 placeholder:text-transparent"
                placeholder=" "
                readonly
            />
            <x-input.floating-label for="day_name" :floating="false">Day Name</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                type="time"
                id="start_time"
                name="start_time"
                class="peer placeholder:text-transparent"
                placeholder=" "
                onchange="calculateHours()"
            />
            <x-input.floating-label for="start_time" :floating="false">Start Time</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                type="time"
                id="end_time"
                name="end_time"
                class="peer placeholder:text-transparent"
                placeholder=" "
                onchange="calculateHours()"
            />
            <x-input.floating-label for="end_time" :floating="false">End Time</x-input.floating-label>
        </div>

        <div class="relative md:col-span-2">
            <x-input.control
                id="total_hours"
                name="total_hours"
                class="peer bg-blue-50/40 font-medium text-blue-600 placeholder:text-transparent"
                placeholder=" "
                readonly
            />
            <x-input.floating-label for="total_hours" :floating="false">Total Hours</x-input.floating-label>
        </div>

        <div class="border-t border-slate-200 pt-2 text-[10px] font-medium text-blue-600 md:col-span-2">
            Academic &amp; Subject Details
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="class_name"
                name="class_name"
                placeholder="Select Class"
                :options="[]"
                add-button-id="openClassFromRoutineForm"
                add-button-label="Add class"
                add-button-target="classModal"
            />
            <x-input.floating-label for="class_name" :floating="false">Class</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="group_name"
                name="group_name"
                placeholder="Select Group"
                :options="[]"
                add-button-id="openGroupFromRoutineForm"
                add-button-label="Add group"
                add-button-target="groupModal"
            />
            <x-input.floating-label for="group_name" :floating="false">Group</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="routine_section_name"
                name="section_name"
                placeholder="Select Section"
                :options="[]"
                add-button-id="openSectionFromRoutineForm"
                add-button-label="Add section"
                add-button-target="sectionModal"
            />
            <x-input.floating-label for="routine_section_name" :floating="false">Section</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="session_name"
                name="session_name"
                placeholder="Select Session"
                :options="[]"
                add-button-id="openSessionFromRoutineForm"
                add-button-label="Add session"
                add-button-target="sessionModal"
            />
            <x-input.floating-label for="session_name" :floating="false">Session</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="exam_name"
                name="exam_name"
                placeholder="Select Exam"
                :options="[]"
                add-button-id="openExamFromRoutineForm"
                add-button-label="Add exam"
                add-button-target="examModal"
            />
            <x-input.floating-label for="exam_name" :floating="false">Exam Name</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.dropdown-select
                id="subject_name"
                name="subject_name"
                placeholder="Select Subject"
                :options="[]"
                add-button-id="openSubjectFromRoutineForm"
                add-button-label="Add subject"
                add-button-target="subjectModal"
            />
            <x-input.floating-label for="subject_name" :floating="false">Subject</x-input.floating-label>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary
                    id="closeRoutineModalButton"
                    type="button"
                    onclick="closeRoutineModal()"
                    class="w-full"
                >
                    Cancel
                </x-button.secondary>
                <x-button.primary type="submit" class="w-full">
                    Save
                </x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.academic.subject.partials.subject-modal', ['showGradeAddButton' => true])
    @include('school.exam.exam_name.partials.exam-modal')
    @include('school.exam.grade.partials.grade-modal')

    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.subject.partials.js.modal-open')
    @include('school.exam.exam_name.partials.js.modal-open')
    @include('school.exam.grade.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.academic.subject.partials.js.modal-submit')
    @include('school.exam.exam_name.partials.js.modal-submit')
    @include('school.exam.grade.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.subject.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.exam.exam_name.partials.js.error-validation')
    @include('school.exam.grade.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;

        // --- Live Search Implementation ---
        function syncRoutineSearch(sourceId) {
            const source = document.getElementById(sourceId);
            const targetId = sourceId === 'tableSearch' ? 'tableSearchMobile' : 'tableSearch';
            const target = document.getElementById(targetId);

            if (source && target) {
                target.value = source.value;
            }
        }

        function restoreRoutineSearch() {
            const desktopSearch = document.getElementById('tableSearch');
            const mobileSearch = document.getElementById('tableSearchMobile');

            if (desktopSearch) desktopSearch.value = '';
            if (mobileSearch) mobileSearch.value = '';

            liveSearch();
        }

        function liveSearch() {
            const input = document.getElementById("tableSearch");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("routineTableBody")?.closest('table');

            if (!table) {
                return;
            }

            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const tds = tr[i].getElementsByTagName("td");
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j]) {
                        const txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        // Utilities
        function formatTime12h(timeStr) {
            if (!timeStr) return '';
            const [hours, minutes] = timeStr.split(':');
            let h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${minutes} ${ampm}`;
        }

        function formatDateDDMMYYYY(dateStr) {
            if (!dateStr) return '';
            const [y, m, d] = dateStr.split('-');
            return `${d}/${m}/${y}`;
        }

        function escapeRoutineHtml(value) {
            return String(value ?? '-').replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);
        }

        function routineTableCell(value, alignment = 'text-left') {
            const content = escapeRoutineHtml(value);

            return `
                <td class="h-8 border border-gray-300 px-3 ${alignment}">
                    <div class="school-data-table-cell-scroll" title="${content}">${content}</div>
                </td>`;
        }

        function updateDayName() {
            const dateInput = document.getElementById('exam_date').value;
            if (!dateInput) return;
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const date = new Date(dateInput);
            document.getElementById('day_name').value = days[date.getUTCDay()];
        }

        function calculateHours() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            if (start && end) {
                const s = new Date(`2026-01-01 ${start}`);
                const e = new Date(`2026-01-01 ${end}`);
                let diff = (e - s) / (1000 * 60 * 60);
                if (diff < 0) diff += 24;
                document.getElementById('total_hours').value = diff.toFixed(2) + ' Hrs';
            }
        }

        // Data Loading
        async function loadInitialData() {
            try {
                const res = await axios.get('/api/get-school-classes');
                populateComponentDropdown('class_nameMenu', res.data.data || [], 'class_name', 'class_name', {
                    id: 'id'
                });
                setComponentDropdownValue('class_name', '', 'Select Class');
                populateComponentDropdown('f_classMenu', res.data.data || [], 'class_name', 'class_name', {
                    id: 'id'
                });
                setComponentDropdownValue('f_class', '', 'All Classes');
            } catch (e) {
                console.error("Initial Load Error", e);
            }
        }

        function populateComponentDropdown(menuId, data, valueField, labelField, dataFields = {}) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            menu.innerHTML = '';

            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(item[valueField] ?? '');
                btn.textContent = item[labelField] ?? '';
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');

                Object.entries(dataFields).forEach(([dataKey, sourceKey]) => {
                    btn.dataset[`option${dataKey.charAt(0).toUpperCase()}${dataKey.slice(1)}`] = String(item[sourceKey] ?? '');
                });

                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root?.querySelector('[data-dropdown-select-input]');
                    const label = root?.querySelector('[data-dropdown-select-label]');

                    if (!root || !input || !label) return;

                    input.value = this.dataset.value || '';
                    label.textContent = this.textContent.trim();

                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                        const selected = option === this;
                        option.classList.toggle('bg-slate-100', selected);
                        option.classList.toggle('text-slate-900', selected);
                        option.classList.toggle('text-slate-800', !selected);
                        option.setAttribute('aria-selected', String(selected));
                    });

                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    root.querySelector('[data-dropdown-select-button] i')?.classList.remove('rotate-180');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                menu.appendChild(btn);
            });
        }

        function setComponentDropdownValue(dropdownId, value, label) {
            const input = document.getElementById(dropdownId);
            const labelEl = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            const menu = document.getElementById(`${dropdownId}Menu`);

            if (input) input.value = value || '';
            if (labelEl) labelEl.textContent = label || labelEl.dataset.placeholder || 'Select...';

            menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                const selected = String(option.dataset.value || '') === String(value || '');
                option.classList.toggle('bg-slate-100', selected);
                option.classList.toggle('text-slate-900', selected);
                option.classList.toggle('text-slate-800', !selected);
                option.setAttribute('aria-selected', String(selected));
            });
        }

        function populateDropdown(menuId, data, valueField, labelField) {
            populateComponentDropdown(menuId, data, valueField, labelField);
        }

        function setDropdownValue(dropdownId, value, label) {
            setComponentDropdownValue(dropdownId, value, label);
        }

        function getSelectedDataId(elementOrId) {
            const el = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
            if (!el) return '';

            if (el.matches?.('[data-dropdown-select-input]')) {
                const menu = document.getElementById(`${el.id}Menu`);
                const selected = menu?.querySelector(`[data-value="${CSS.escape(el.value)}"]`);
                return selected?.dataset.optionId || '';
            }

            return el.options?.[el.selectedIndex]?.getAttribute('data-id') || '';
        }

        // Reset downstream dropdowns to empty placeholder
        function resetDropdown(id, label, isFilter = false) {
            const element = document.getElementById(id);
            const placeholder = `${isFilter ? 'All' : 'Select'} ${label}`;

            if (!element) return;

            if (element.matches?.('[data-dropdown-select-input]')) {
                populateComponentDropdown(`${id}Menu`, [], 'value', 'label');
                setComponentDropdownValue(id, '', placeholder);
                return;
            }

            element.innerHTML = `<option value="">${placeholder}</option>`;
        }

        // Updated Cascade Logic
        async function handleCascade(el, next) {
            const isFilter = next.startsWith('f_');
            const id = getSelectedDataId(el);

            // Reset all fields downstream of the changed one
            if (next === 'group' || next === 'f_group') {
                resetDropdown(isFilter ? 'f_group'   : 'group_name',   'Group',   isFilter);
                resetDropdown(isFilter ? 'f_section' : 'routine_section_name', 'Section', isFilter);
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            } else if (next === 'section' || next === 'f_section') {
                resetDropdown(isFilter ? 'f_section' : 'routine_section_name', 'Section', isFilter);
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            } else if (next === 'session' || next === 'f_session') {
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            }

            if (!id) return;

            // NEW: Get the class_id for session filtering mandate
            const classEl = document.getElementById(isFilter ? 'f_class' : 'class_name');
            const classId = getSelectedDataId(classEl);

            try {
                if (next === 'group' || next === 'f_group') {
                    const res = await axios.get(`/api/get-school-groups?class_id=${id}`);
                    fillDropdown(isFilter ? 'f_group' : 'group_name', res.data.data, 'group_name', 'Group', isFilter);
                } else if (next === 'section' || next === 'f_section') {
                    const res = await axios.get(`/api/get-school-sections?group_id=${id}`);
                    fillDropdown(isFilter ? 'f_section' : 'routine_section_name', res.data.data, 'section_name', 'Section', isFilter);
                } else if (next === 'session' || next === 'f_session') {
                    const res = await axios.get(`/api/get-school-sessions?class_id=${classId}&section_id=${id}`);
                    fillDropdown(isFilter ? 'f_session' : 'session_name', res.data.data, 'session_year', 'Session', isFilter);
                }
            } catch (e) {
                console.error("Cascade Error", e);
            }
        }

        function fillDropdown(target, data, field, label, isFilter = false) {
            const element = document.getElementById(target);
            const placeholder = `${isFilter ? 'All' : 'Select'} ${label}`;

            if (!element) return;

            if (element.matches?.('[data-dropdown-select-input]')) {
                populateComponentDropdown(`${target}Menu`, data, field, field, {
                    id: 'id'
                });
                setComponentDropdownValue(target, '', placeholder);
                return;
            }

            let opts = `<option value="">${placeholder}</option>`;
            data.forEach(item => opts += `<option value="${item[field]}" data-id="${item.id}">${item[field]}</option>`);
            element.innerHTML = opts;
        }

        async function loadExamAndSubject() {
            const classSelect = document.getElementById('class_name');
            const className = classSelect.value;
            const groupName = document.getElementById('group_name').value;
            const sectionName = document.getElementById('routine_section_name').value;
            const sessionName = document.getElementById('session_name').value;
            const classId = getSelectedDataId(classSelect);

            // Always reset exam and subject first
            resetDropdown('exam_name',    'Exam');
            resetDropdown('subject_name', 'Subject');

            if (!className || !sessionName) return;

            // Build exam query params including group and section for precise filtering
            const examParams = new URLSearchParams({ class_name: className, session_name: sessionName });
            if (groupName) examParams.append('group_name', groupName);
            if (sectionName) examParams.append('section_name', sectionName);

            try {
                const [examRes, subRes] = await Promise.all([
                    axios.get(`/api/get-school-exams?${examParams.toString()}`),
                    axios.get(`/api/get-school-subjects?class_id=${classId}`)
                ]);
                fillDropdown('exam_name', examRes.data.data, 'exam_name', 'Exam');
                fillDropdown('subject_name', subRes.data.data, 'subject_name', 'Subject');
            } catch (e) {
                console.error("Exam/Sub Load Error", e);
            }
        }

        async function loadFilterExams() {
            const res = await axios.get('/api/get-school-exams', {
                params: {
                    class_id: getSelectedDataId('f_class') || undefined,
                    group_id: getSelectedDataId('f_group') || undefined,
                    section_id: getSelectedDataId('f_section') || undefined,
                    session_id: getSelectedDataId('f_session') || undefined,
                },
            });
            fillDropdown('f_exam', res.data.data, 'exam_name', 'Exam', true);
        }

        document.addEventListener('school:class-saved', async function (event) {
            const { classItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !classItem?.id) {
                return;
            }
            event.preventDefault();

            try {
                const response = await axios.get('/api/get-school-classes');
                populateComponentDropdown('class_nameMenu', response.data.data || [], 'class_name', 'class_name', { id: 'id' });
                setComponentDropdownValue('class_name', classItem.class_name, classItem.class_name);
                resetDropdown('group_name', 'Group');
                resetDropdown('routine_section_name', 'Section');
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:group-saved', async function (event) {
            const { groupItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !groupItem?.id || !groupItem?.class_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(groupItem.class_id));
                if (selectedClass) {
                    setComponentDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${groupItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                setComponentDropdownValue('group_name', groupItem.group_name, groupItem.group_name);
                resetDropdown('routine_section_name', 'Section');
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:section-already-exists', async function (event) {
            const { sectionName, classId, groupId, returnModalId } = event.detail || {};
            if (returnModalId !== 'routineModal' || !sectionName || !classId || !groupId) {
                return;
            }
            event.preventDefault();

            try {
                // Match the Exam Name modal flow: restore the complete academic
                // selection chain by IDs before selecting the existing section.
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data?.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });

                const selectedClass = classes.find(item => String(item.id) === String(classId));
                if (selectedClass) {
                    setComponentDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get('/api/get-school-groups', {
                    params: { class_id: classId },
                });
                const groups = groupResponse.data?.data || [];
                populateComponentDropdown('group_nameMenu', groups, 'group_name', 'group_name', { id: 'id' });

                const selectedGroup = groups.find(item => String(item.id) === String(groupId));
                if (selectedGroup) {
                    setComponentDropdownValue('group_name', selectedGroup.group_name, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get('/api/get-school-sections', {
                    params: { class_id: classId, group_id: groupId },
                });
                const sections = sectionResponse.data?.data || [];
                populateComponentDropdown('routine_section_nameMenu', sections, 'section_name', 'section_name', { id: 'id' });

                const existingSection = sections.find(item =>
                    String(item.section_name).trim().toLowerCase() === String(sectionName).trim().toLowerCase()
                );
                if (existingSection) {
                    setComponentDropdownValue('routine_section_name', existingSection.section_name, existingSection.section_name);
                }
            } catch (e) {
                console.error('Failed to refresh existing section list', e);
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:section-saved', async function (event) {
            const { sectionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !sectionItem?.id || !sectionItem?.class_id || !sectionItem?.group_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(sectionItem.class_id));
                if (selectedClass) {
                    setComponentDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${sectionItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                const selectedGroup = groupResponse.data.data.find(item => String(item.id) === String(sectionItem.group_id));
                if (selectedGroup) {
                    setComponentDropdownValue('group_name', selectedGroup.group_name, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get(`/api/get-school-sections?class_id=${sectionItem.class_id}&group_id=${sectionItem.group_id}`);
                populateComponentDropdown('routine_section_nameMenu', sectionResponse.data.data || [], 'section_name', 'section_name', { id: 'id' });
                setComponentDropdownValue('routine_section_name', sectionItem.section_name, sectionItem.section_name);
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:session-saved', async function (event) {
            const { sessionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !sessionItem?.id || !sessionItem?.class_id || !sessionItem?.group_id || !sessionItem?.section_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(sessionItem.class_id));
                if (selectedClass) {
                    setDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${sessionItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                const selectedGroup = groupResponse.data.data.find(item => String(item.id) === String(sessionItem.group_id));
                if (selectedGroup) {
                    setDropdownValue('group_name', selectedGroup.group_name, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get(`/api/get-school-sections?class_id=${sessionItem.class_id}&group_id=${sessionItem.group_id}`);
                populateComponentDropdown('routine_section_nameMenu', sectionResponse.data.data || [], 'section_name', 'section_name', { id: 'id' });
                const selectedSection = sectionResponse.data.data.find(item => String(item.id) === String(sessionItem.section_id));
                if (selectedSection) {
                    setDropdownValue('routine_section_name', selectedSection.section_name, selectedSection.section_name);
                }

                const sessionResponse = await axios.get(`/api/get-school-sessions?class_id=${sessionItem.class_id}&section_id=${sessionItem.section_id}`);
                populateComponentDropdown('session_nameMenu', sessionResponse.data.data || [], 'session_year', 'session_year', { id: 'id' });
                setDropdownValue('session_name', sessionItem.session_year, sessionItem.session_year);
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
                await loadExamAndSubject();
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:exam-saved', async function (event) {
            const { examItem, returnModalId } = event.detail || {};
            if (returnModalId !== 'routineModal' || !examItem?.id) {
                return;
            }
            event.preventDefault();

            try {
                await loadExamAndSubject();
                setComponentDropdownValue('exam_name', examItem.exam_name, examItem.exam_name);
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:subject-saved', async function (event) {
            const { subjectItem, returnModalId } = event.detail || {};
            if (returnModalId !== 'routineModal' || !subjectItem?.id) {
                return;
            }
            event.preventDefault();

            try {
                await loadExamAndSubject();
                if (typeof refreshSubjectDropdown === 'function') {
                    await refreshSubjectDropdown(subjectItem);
                } else {
                    const value = subjectItem.subject_name;
                    setComponentDropdownValue('subject_name', value, value);
                }
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        // CRUD Operations
        async function fetchRoutines(page = 1, isFiltering = false) {
            currentPage = page;
            const tbody = document.getElementById('routineTableBody');
            tbody.innerHTML = '<tr><td colspan="12" class="loader-row text-center py-4">Loading...</td></tr>';

            try {
                const params = new URLSearchParams({
                    page: page,
                    class_id: getSelectedDataId('f_class'),
                    group_id: getSelectedDataId('f_group'),
                    section_id: getSelectedDataId('f_section'),
                    session_id: getSelectedDataId('f_session'),
                    exam_id: getSelectedDataId('f_exam')
                });

                const res = await axios.get(`/api/school-exam-routines?${params.toString()}`);
                const data = res.data;
                const items = data.data;

                tbody.innerHTML = '';
                if (!items || items.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="12" class="text-center py-4 text-gray-400">No routines found matching the criteria.</td></tr>';
                } else {
                    items.forEach((item, i) => {
                        const sl = (data.current_page - 1) * data.per_page + (i + 1);
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                ${routineTableCell(sl, 'text-center')}
                                ${routineTableCell(item.school_class?.class_name)}
                                ${routineTableCell(item.school_group?.group_name)}
                                ${routineTableCell(item.school_section?.section_name)}
                                ${routineTableCell(item.school_session?.session_year)}
                                ${routineTableCell(item.school_exam?.exam_name)}
                                ${routineTableCell(item.school_subject?.subject_name)}
                                ${routineTableCell(formatDateDDMMYYYY(item.exam_date), 'text-center')}
                                ${routineTableCell(item.day_name)}
                                ${routineTableCell(formatTime12h(item.start_time), 'text-center')}
                                ${routineTableCell(formatTime12h(item.end_time), 'text-center')}
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="mx-auto flex h-8 items-center justify-center space-x-1">
                                        <button
                                            type="button"
                                            onclick="editRoutine(${item.id})"
                                            title="Edit routine"
                                            aria-label="Edit routine"
                                            class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1"
                                        >
                                            <i class="far fa-edit text-sm" aria-hidden="true"></i>
                                        </button>
                                        <button
                                            type="button"
                                            onclick="deleteRoutine(${item.id})"
                                            title="Delete routine"
                                            aria-label="Delete routine"
                                            class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1"
                                        >
                                            <i class="far fa-trash-alt text-sm" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                renderPagination(data);
                document.getElementById('tableSearch').value = '';
                document.getElementById('tableSearchMobile').value = '';
                if (isFiltering) closeFilterModal();
            } catch (err) {
                tbody.innerHTML =
                    '<tr><td colspan="12" class="border border-gray-300 px-3 py-4 text-center text-red-500">Failed to load data.</td></tr>';
            }
        }

        function renderPagination(data) {
            const info = document.getElementById('paginationInfo');
            const links = document.getElementById('paginationLinks');
            if (!data.total || data.total === 0) {
                info.innerText = '0 of 0';
                links.innerHTML = '';
                return;
            }
            info.innerText = `${data.to} of ${data.total}`;
            let html =
                `<button onclick="fetchRoutines(${data.current_page - 1})" class="page-link-premium ${data.current_page === 1 ? 'disabled' : ''}" ${data.current_page === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= data.last_page; i++) {
                if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                    html +=
                        `<button onclick="fetchRoutines(${i})" class="page-link-premium ${i === data.current_page ? 'active' : ''}">${i}</button>`;
                } else if (i === data.current_page - 2 || i === data.current_page + 2) {
                    html += `<span class="px-2 text-gray-400 text-xs">...</span>`;
                }
            }
            html +=
                `<button onclick="fetchRoutines(${data.current_page + 1})" class="page-link-premium ${data.current_page === data.last_page ? 'disabled' : ''}" ${data.current_page === data.last_page ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>`;
            links.innerHTML = html;
        }

        // Updated Edit Logic
        async function editRoutine(id) {
            try {
                const res = await axios.get(`/api/school-exam-routines/${id}`);
                const data = res.data;

                document.getElementById('routine_edit_id').value = data.id;
                document.getElementById('exam_date').value = data.exam_date;
                document.getElementById('day_name').value = data.day_name;
                document.getElementById('start_time').value = data.start_time;
                document.getElementById('end_time').value = data.end_time;
                document.getElementById('total_hours').value = data.total_hours;

                const classSelect = document.getElementById('class_name');
                setComponentDropdownValue('class_name', data.class_name, data.class_name || 'Select Class');
                // Important: Trigger cascade and WAIT for it
                await handleCascade(classSelect, 'group');

                const groupSelect = document.getElementById('group_name');
                setComponentDropdownValue('group_name', data.group_name, data.group_name || 'Select Group');
                await handleCascade(groupSelect, 'section');

                const sectionSelect = document.getElementById('routine_section_name');
                setComponentDropdownValue('routine_section_name', data.section_name, data.section_name || 'Select Section');

                // MODIFIED: Fetch sessions with explicit class_id
                const classId = getSelectedDataId(classSelect);
                const sectionId = getSelectedDataId(sectionSelect);
                const sessRes = await axios.get(`/api/get-school-sessions?class_id=${classId}&section_id=${sectionId}`);
                fillDropdown('session_name', sessRes.data.data, 'session_year', 'Session');

                setComponentDropdownValue('session_name', data.session_name, data.session_name || 'Select Session');

                await loadExamAndSubject();
                setComponentDropdownValue('exam_name', data.exam_name, data.exam_name || 'Select Exam');
                document.getElementById('subject_name').value = data.subject_name;

                document.getElementById('routineModalTitle').innerText = "Edit Exam Routine";
                openRoutineModal();
            } catch (e) {
                console.error("Edit Load Error", e);
                Swal.fire('Error', 'Could not load routine data', 'error');
            }
        }

        async function deleteRoutine(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this routine entry?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/api/school-exam-routines/${id}`);
                        Toastify({
                            text: "Routine Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchRoutines(currentPage);
                    } catch (e) {
                        console.error("Delete Error", e);
                    }
                }
            });
        }

        document.getElementById('routineForm').onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('routine_edit_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // The dropdown values are names for display/filtering. The routine
            // API validates relational IDs, stored on each dropdown option.
            data.class_id = getSelectedDataId('class_name');
            data.group_id = getSelectedDataId('group_name') || null;
            data.section_id = getSelectedDataId('routine_section_name') || null;
            data.session_id = getSelectedDataId('session_name');
            data.exam_id = getSelectedDataId('exam_name');
            data.subject_id = getSelectedDataId('subject_name');

            try {
                const req = id ? axios.put(`/api/school-exam-routines/${id}`, data) : axios.post(
                    '/api/school-exam-routines', data);
                await req;
                Toastify({
                    text: id ? "Routine Updated" : "Routine Created",
                    style: {
                        background: "#2563eb"
                    }
                }).showToast();
                closeRoutineModal();
                fetchRoutines(id ? currentPage : 1);
            } catch (err) {
                const errors = err.response?.data?.errors;
                const message = err.response?.data?.message || 'An unexpected error occurred.';

                if (errors) {
                    const formatted = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        title: 'Validation Error',
                        html: formatted,
                        icon: 'warning'
                    });
                } else {
                    Swal.fire('Error', message, 'error');
                }
            }
        };

        function resetFilters() {
            setComponentDropdownValue('f_class', '', 'All Classes');
            resetDropdown('f_group', 'Groups', true);
            resetDropdown('f_section', 'Sections', true);
            resetDropdown('f_session', 'Sessions', true);
            resetDropdown('f_exam', 'Exams', true);
            fetchRoutines(1, true);
        }

        function toggleExportMenu(event) {
            event?.stopPropagation();
            const menu = document.getElementById('exportMenu');
            const icon = document.querySelector('#exportDropdownButton i');
            const isOpen = !menu.classList.contains('hidden');

            menu.classList.toggle('hidden', isOpen);
            icon?.classList.toggle('rotate-180', !isOpen);
            document.getElementById('exportDropdownButton')?.setAttribute('aria-expanded', String(!isOpen));
        }

        function closeExportMenu() {
            document.getElementById('exportMenu')?.classList.add('hidden');
            document.querySelector('#exportDropdownButton i')?.classList.remove('rotate-180');
            document.getElementById('exportDropdownButton')?.setAttribute('aria-expanded', 'false');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('#exportDropdown')) {
                closeExportMenu();
            }
        });

        function exportData(type) {
            const params = new URLSearchParams({
                type,
                search: document.getElementById('tableSearch').value,
                class_id: getSelectedDataId('f_class'),
                group_id: getSelectedDataId('f_group'),
                section_id: getSelectedDataId('f_section'),
                session_id: getSelectedDataId('f_session'),
                exam_id: getSelectedDataId('f_exam')
            });
            window.location.href = `/api/school-exam-routines-export?${params.toString()}`;
            closeExportMenu();
        }

        function openRoutineModal() {
            if (!document.getElementById('routine_edit_id').value) {
                document.getElementById('routineForm').reset();
                setComponentDropdownValue('class_name', '', 'Select Class');
                document.getElementById('routineModalTitle').innerText = "Create Exam Routine";
            }
            document.getElementById('routineModal').classList.remove('hidden');
        }

        function closeRoutineModal() {
            document.getElementById('routineModal').classList.add('hidden');
            document.getElementById('routineForm').reset();
            setComponentDropdownValue('class_name', '', 'Select Class');
            document.getElementById('routine_edit_id').value = "";
        }

        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        window.onload = () => {
            loadInitialData();
            document.getElementById('exportDropdownButton')?.addEventListener('click', toggleExportMenu);
            document.getElementById('class_name')?.addEventListener('change', function() {
                handleCascade(this, 'group');
            });
            document.getElementById('group_name')?.addEventListener('change', function() {
                handleCascade(this, 'section');
            });
            document.getElementById('routine_section_name')?.addEventListener('change', function() {
                handleCascade(this, 'session');
            });
            document.getElementById('session_name')?.addEventListener('change', function() {
                loadExamAndSubject();
            });
            document.getElementById('f_class')?.addEventListener('change', function() {
                handleCascade(this, 'f_group');
            });
            document.getElementById('f_group')?.addEventListener('change', function() {
                handleCascade(this, 'f_section');
            });
            document.getElementById('f_section')?.addEventListener('change', function() {
                handleCascade(this, 'f_session');
            });
            document.getElementById('f_session')?.addEventListener('change', function() {
                loadFilterExams();
            });
            fetchRoutines();
        };
    </script>
@endsection
