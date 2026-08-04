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
                    <x-button.secondary type="button" onclick="openAdmitSettings()" class="w-full">
                        Settings
                    </x-button.secondary>

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

        <div class="relative">
            <x-input.dropdown-select
                id="bulk_language"
                placeholder="Select Language"
                value="en"
                :options="[
                    ['value' => 'en', 'label' => 'English'],
                    ['value' => 'bn', 'label' => 'Bangla'],
                ]"
            />
            <x-input.floating-label for="bulk_language" :floating="false">Document Language</x-input.floating-label>
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

    <x-modal.form
        id="admitSettingsModal"
        form-id="admitSettingsForm"
        title="Admit Card Instructions"
        close-button-id="closeAdmitSettings"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-y-auto border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[640px]"
        panel-style="border-radius:4px; max-height:min(620px, calc(100dvh - 2.5rem));"
        fields-class="grid grid-cols-1 gap-3 md:grid-cols-2"
    >
        <div class="space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-600">English</p>
            @for($i = 0; $i < 3; $i++)
                <div class="relative">
                    <x-input.control id="instruction_en_{{ $i }}" maxlength="300" required />
                    <x-input.floating-label for="instruction_en_{{ $i }}" :floating="false">Instruction {{ $i + 1 }}</x-input.floating-label>
                </div>
            @endfor
        </div>
        <div class="space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-600">Bangla</p>
            @for($i = 0; $i < 3; $i++)
                <div class="relative">
                    <x-input.control id="instruction_bn_{{ $i }}" maxlength="300" required />
                    <x-input.floating-label for="instruction_bn_{{ $i }}" :floating="false">নির্দেশনা {{ $i + 1 }}</x-input.floating-label>
                </div>
            @endfor
        </div>
        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 bg-white px-6 pb-4 pt-3">
                <x-button.secondary id="closeAdmitSettings" type="button" onclick="closeAdmitSettingsModal()" class="w-full">Cancel</x-button.secondary>
                <x-button.primary type="submit" id="saveAdmitSettings" class="w-full">Save</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    <x-modal.form
        id="admitPreviewModal"
        form-id="admitPreviewForm"
        title="Admit Card Preview"
        close-button-id="closeAdmitPreview"
        panel-class="mx-auto my-auto flex w-full max-w-[1100px] flex-col overflow-hidden border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)]"
        panel-style="border-radius:4px; width:min(1100px, calc(100vw - 2rem)); height:calc(100dvh - 2rem); max-height:760px;"
        header-class="flex h-14 shrink-0 items-center justify-center border-b border-slate-200 bg-white px-4"
        form-class="m-0 flex min-h-0 flex-1 flex-col overflow-hidden"
        body-class="flex min-h-0 flex-1 bg-slate-100 px-3 pb-3 pt-2"
        fields-class="flex min-h-0 flex-1 flex-col gap-2"
    >
        <input type="hidden" id="preview_admit_id">
        <iframe id="admitPreviewFrame" title="Admit card preview" class="min-h-0 w-full flex-1 border border-slate-300 bg-white"></iframe>
        <x-slot:footer>
            <div class="grid shrink-0 grid-cols-3 gap-3 border-t border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="closeAdmitPreview" type="button" onclick="closeAdmitPreviewModal()" class="w-full">Close</x-button.secondary>
                <x-button.secondary type="button" onclick="printAdmitPreview()" class="w-full">Print</x-button.secondary>
                <x-button.primary type="button" onclick="downloadAdmitPreview()" class="w-full">Download PDF</x-button.primary>
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
        let admitPreviewRequestId = 0;

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
                                <button type="button" title="View admit card" aria-label="View admit card" onclick="openAdmitPreview(${item.id})" class="flex h-8 w-7 items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-1"><i class="far fa-eye text-sm" aria-hidden="true"></i></button>
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
                language: document.getElementById('bulk_language')?.value || 'en',
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

                if (type === 'pdf-mobile') {
                    previewWindow.location.href = `/api/school-exam-admit-cards/export-pdf?${new URLSearchParams(params).toString()}`;
                    return;
                }

                axios.get('/api/school-exam-admit-cards/preview', { params }).then(res => {
                    previewWindow.location.href = res.data.url;
                    previewWindow.addEventListener('load', () => setTimeout(() => previewWindow.print(), 500), { once: true });
                }).catch(err => {
                    previewWindow.close();
                    Swal.fire('Error', err.response?.data?.message || firstValidationMessage(err) || 'Failed to prepare admit cards.', 'error');
                });
            }
        }

        function firstValidationMessage(error) {
            const errors = error.response?.data?.errors;
            if (!errors) return '';
            const first = Object.values(errors).flat()[0];
            return first || '';
        }

        async function loadAdmitPreview() {
            const id = document.getElementById('preview_admit_id').value;
            const frame = document.getElementById('admitPreviewFrame');
            if (!id || !frame) return;

            const requestId = ++admitPreviewRequestId;

            frame.removeAttribute('src');
            frame.srcdoc = '<div style="font:600 13px Arial;padding:32px;text-align:center;color:#64748b">Preparing admit card...</div>';

            try {
                const response = await axios.get('/api/school-exam-admit-cards/preview', {
                    params: { admit_card_id: id, language: 'en' }
                });
                if (requestId !== admitPreviewRequestId) return;
                frame.removeAttribute('srcdoc');
                frame.onload = () => {
                    try {
                        frame.contentWindow.scrollTo(0, 0);
                    } catch (error) {
                        // The signed preview is same-origin, but scrolling is non-critical.
                    }
                };
                frame.src = response.data.url;
            } catch (error) {
                if (requestId !== admitPreviewRequestId) return;
                frame.srcdoc = `<div style="font:600 13px Arial;padding:32px;text-align:center;color:#b91c1c">${escapeAdmitCardHtml(firstValidationMessage(error) || error.response?.data?.message || 'Unable to load preview.')}</div>`;
            }
        }

        function openAdmitPreview(id) {
            document.getElementById('preview_admit_id').value = id;
            document.getElementById('admitPreviewModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            loadAdmitPreview();
        }

        function closeAdmitPreviewModal() {
            admitPreviewRequestId++;
            document.getElementById('admitPreviewModal').classList.add('hidden');
            const frame = document.getElementById('admitPreviewFrame');
            frame.removeAttribute('srcdoc');
            frame.src = 'about:blank';
            document.body.classList.remove('overflow-hidden');
        }

        function printAdmitPreview() {
            const frame = document.getElementById('admitPreviewFrame');
            frame?.contentWindow?.focus();
            frame?.contentWindow?.print();
        }

        function downloadAdmitPreview() {
            const params = new URLSearchParams({
                admit_card_id: document.getElementById('preview_admit_id').value,
                language: 'en',
            });
            window.location.href = `/api/school-exam-admit-cards/export-pdf?${params.toString()}`;
        }

        async function openAdmitSettings() {
            try {
                const response = await axios.get('/api/school-exam-admit-card-settings');
                ['en', 'bn'].forEach(language => {
                    (response.data[`instructions_${language}`] || []).forEach((instruction, index) => {
                        const input = document.getElementById(`instruction_${language}_${index}`);
                        if (input) input.value = instruction;
                    });
                });
                document.getElementById('admitSettingsModal').classList.remove('hidden');
            } catch (error) {
                Swal.fire('Error', 'Unable to load admit card settings.', 'error');
            }
        }

        function closeAdmitSettingsModal() {
            document.getElementById('admitSettingsModal').classList.add('hidden');
        }

        document.getElementById('admitSettingsForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = document.getElementById('saveAdmitSettings');
            button.disabled = true;
            const payload = { instructions_en: [], instructions_bn: [] };
            ['en', 'bn'].forEach(language => {
                for (let index = 0; index < 3; index++) {
                    payload[`instructions_${language}`].push(document.getElementById(`instruction_${language}_${index}`).value.trim());
                }
            });

            try {
                const response = await axios.put('/api/school-exam-admit-card-settings', payload);
                closeAdmitSettingsModal();
                Toastify({ text: response.data.message, style: { background: '#10b981' } }).showToast();
            } catch (error) {
                Swal.fire('Warning', firstValidationMessage(error) || 'Unable to save settings.', 'warning');
            } finally {
                button.disabled = false;
            }
        });

        function escapeAdmitCardHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function editAdmit(item) {
            openAdmitModal();
            const modalTitle = document.getElementById('admitModalTitle');
            if (modalTitle) modalTitle.innerText = 'Edit Individual Admit Card';
            document.getElementById('admit_edit_id').value = item.id;
            document.getElementById('btnText').innerText = 'Update Admit Card';
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
            setDropdownValue('bulk_language', 'en', 'English');
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
