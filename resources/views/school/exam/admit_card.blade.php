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
            border-left: none;
            border-right: none;
        }

        /* ================= Table Container ================= */
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

        /* ================= Table Header ================= */
        th {
            padding: 0 12px !important;
            height: 34px !important;
            line-height: 34px !important;
            white-space: nowrap !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            color: #374151 !important;
            font-weight: 800 !important;
            vertical-align: middle !important;
            text-align: left !important;
            /* Only first letter capitalized */
            text-transform: capitalize !important;
            letter-spacing: 0.01em !important;
        }

        th:last-child {
            border-right: none !important;
        }

        /* ================= Table Body ================= */
        tr {
            height: 32px !important;
        }

        td {
            padding: 0 12px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            font-size: 11px !important;
            color: #4b5563 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
        }

        td:last-child {
            border-right: none !important;
        }

        tbody tr:hover {
            background: #f9fafb !important;
        }

        /* ================= Pagination Bar (Balanced Height) ================= */
        .pagination-bar {
            padding: 0.6rem 1rem !important;
            border: 1px solid #d1d5db !important;
            border-top: none !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            background: #ffffff !important;
            min-height: 44px !important;
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
            /* Sharp Brutalism Corners */
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

        /* ================= Mobile Adjustments ================= */
        @media (max-width: 768px) {

            th,
            td {
                padding: 0 8px !important;
                height: 30px !important;
            }

            .pagination-bar {
                min-height: 38px !important;
                padding: 0.4rem 0.75rem !important;
            }

            .pagination-btn {
                height: 24px !important;
                min-width: 24px !important;
            }
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .btn-outline-premium {
            border: 1.5px solid #2563eb;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .pagination-btn {
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: bold;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }

        .pagination-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .action-icon {
            font-size: 1.25rem;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-container input {
            padding-left: 32px !important;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="header_search" placeholder="Search ID or Name..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" onkeyup="if(event.key === 'Enter') fetchTable(1)" />
                        </div>
                    </div>

                    <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
                        <x-button.secondary onclick="toggleFilterModal()" class="w-full lg:w-auto">
                            Filter
                        </x-button.secondary>

                        <x-dropdown button-id="btnAdmitExport" menu-id="admitExportDropdown" label="Export">
                            <x-dropdown.item onclick="exportData('pdf-mobile')">PDF</x-dropdown.item>
                            <x-dropdown.item onclick="exportData('excel')">Excel</x-dropdown.item>
                            <x-dropdown.item onclick="exportData('pdf')">Print</x-dropdown.item>
                        </x-dropdown>

                        <x-button.primary onclick="openAdmitModal()" class="w-full lg:w-auto">
                            Admit Card
                        </x-button.primary>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="header_search_mobile" placeholder="Search ID or Name..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;"
                        onkeyup="if(event.key === 'Enter') { document.getElementById('header_search').value = this.value; fetchTable(1); }" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <x-modal.form
                id="filterModal"
                form-id="admitFilterForm"
                title="Admit Card Filter"
                close-button-id="closeAdmitFilterModal"
                class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/50 p-4 sm:p-20"
                panel-class="modal-content-sharp mx-auto my-auto flex max-h-[calc(100dvh-2rem)] w-full max-w-[320px] flex-col overflow-visible bg-white shadow-2xl"
                panel-style="border-radius: 0;"
                header-class="shrink-0 border-b border-gray-200 bg-white px-4 pb-3 pt-5"
                title-class="m-0 text-center text-2xl font-semibold leading-tight text-gray-800"
                form-class="m-0"
                body-class="bg-white px-4 py-4"
                fields-class="grid grid-cols-1 gap-3"
                onclick="if (event.target === this) toggleFilterModal()"
            >
                <div>
                    <label class="mb-1 block text-[10px] text-gray-500">Class</label>
                    <x-input.dropdown-select id="filter_class_name" placeholder="Select Class" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] text-gray-500">Group</label>
                    <x-input.dropdown-select id="filter_group_name" placeholder="Select Group" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] text-gray-500">Section</label>
                    <x-input.dropdown-select id="filter_section_name" placeholder="Select Section" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] text-gray-500">Session</label>
                    <x-input.dropdown-select id="filter_session_name" placeholder="Select Session" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] text-gray-500">Exam Name</label>
                    <x-input.dropdown-select id="filter_exam_name" placeholder="Select Exam" :options="[]" />
                </div>

                <x-slot:footer>
                    <div class="grid grid-cols-2 gap-2 bg-white px-4 pb-5 pt-2">
                        <x-button.secondary type="button" onclick="resetFilters()" class="w-full">
                            Reset
                        </x-button.secondary>
                        <x-button.primary type="button" onclick="applyFilters()" class="w-full">
                            Apply
                        </x-button.primary>
                    </div>
                </x-slot:footer>
            </x-modal.form>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="this.classList.add('hidden')">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl"
                    onclick="event.stopPropagation()">
                    <div class="flex flex-col gap-1.5">
                        <button onclick="exportData('pdf-mobile')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button onclick="exportData('excel')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button onclick="exportData('pdf')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button onclick="document.getElementById('exportModal').classList.add('hidden')"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1000px]">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Exam Name</th>
                                <th>Student Id</th>
                                <th>Student Name</th>
                                <th>Admit Number</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="admitTableBody"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase" id="paginationInfo"></div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admit Card Modal --}}
    <x-modal.form
        id="admitModal"
        form-id="admitForm"
        title="Bulk Admit Card Generator"
        close-button-id="closeAdmitModal"
        class="fixed inset-0 z-[100] hidden flex items-center justify-center overflow-y-auto bg-gray-900/60 px-8 py-12 backdrop-blur-sm sm:px-40"
        panel-class="modal-content-sharp mx-auto my-auto flex w-full max-w-2xl flex-col overflow-hidden border border-gray-100 bg-white shadow-2xl max-h-[70vh] sm:max-h-[85vh]"
        panel-style="border-radius: 0;"
        header-class="sticky top-0 z-10 flex shrink-0 items-center justify-center border-b bg-white px-5 py-3"
        title-class="text-center text-[13px] font-medium capitalize leading-tight tracking-normal text-gray-800"
        form-class="m-0 flex min-h-0 flex-1 flex-col overflow-hidden"
        body-class="min-h-0 flex-1 overflow-y-auto custom-scrollbar bg-gray-50/30 p-4 sm:p-6"
        fields-class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2"
    >
        <input type="hidden" id="admit_edit_id">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <x-input.dropdown-select
                                id="class_name"
                                placeholder="Select Class"
                                :options="[]"
                                add-button-id="openClassFromAdmitForm"
                                add-button-label="Add class"
                                add-button-target="classModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <x-input.dropdown-select
                                id="group_name"
                                placeholder="Select Group"
                                :options="[]"
                                add-button-id="openGroupFromAdmitForm"
                                add-button-label="Add group"
                                add-button-target="groupModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <x-input.dropdown-select
                                id="section_name"
                                placeholder="Select Section"
                                :options="[]"
                                add-button-id="openSectionFromAdmitForm"
                                add-button-label="Add section"
                                add-button-target="sectionModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <x-input.dropdown-select
                                id="session_name"
                                placeholder="Select Session"
                                :options="[]"
                                add-button-id="openSessionFromAdmitForm"
                                add-button-label="Add session"
                                add-button-target="sessionModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Exam
                                Name</label>
                            <x-input.dropdown-select
                                id="exam_name"
                                placeholder="Select Exam"
                                :options="[]"
                                add-button-id="openExamFromAdmitForm"
                                add-button-label="Add exam"
                                add-button-target="examModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Generate
                                For</label>
                            <x-input.dropdown-select
                                id="generate_type"
                                placeholder="Select generation type"
                                value="all"
                                :options="[
                                    ['value' => 'all', 'label' => 'All Students'],
                                    ['value' => 'single', 'label' => 'Single Student'],
                                ]"
                            />
                        </div>

                        <div id="admitPrerequisiteWarning"
                            class="col-span-1 hidden border border-amber-200 bg-amber-50 px-3 py-2 text-[10px] leading-4 text-amber-700 sm:col-span-2">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-exclamation-circle mt-0.5 shrink-0 text-[10px]" aria-hidden="true"></i>
                                <span>Please create the Exam and Exam Routine before generating the Admit Card.</span>
                            </div>
                        </div>

                        <div id="single_student_container" class="col-span-1 sm:col-span-2 hidden">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Select
                                Student</label>
                            <x-input.dropdown-select id="student_id" placeholder="Select Student" :options="[]" />
                        </div>

                        <div id="studentStatusBox"
                            class="col-span-1 sm:col-span-2 bg-white p-3 border border-dashed border-slate-300 my-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-400 mb-1">Student
                                Status</label>
                            <div id="studentCountDisplay"
                                class="text-[11px] font-mono font-bold text-blue-600 tracking-tighter">
                                0 Students Identified
                            </div>
                            <div class="text-[9px] text-gray-400 mt-1">
                                Admit card number will be auto-generated sequentially.
                            </div>
                        </div>

        <x-slot:footer>
            <div class="sticky bottom-0 flex flex-row gap-2 border-t border-gray-100 bg-white px-4 py-4 sm:justify-end sm:px-6">
                    <x-button.secondary
                        id="closeAdmitModal"
                        type="button"
                        onclick="document.getElementById('admitModal').classList.add('hidden')"
                        class="w-1/2 sm:w-auto sm:px-8"
                    >
                        Cancel
                    </x-button.secondary>
                    <x-button.primary
                        type="submit"
                        id="submitBtn"
                        disabled
                        class="w-1/2 sm:w-auto sm:px-12 disabled:opacity-50"
                    >
                        <span id="btnSpinner" class="hidden">
                            <svg class="animate-spin h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
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
            const generateType = document.getElementById('generate_type').value;
            const selectedStudent = document.getElementById('student_id').value;
            const hasStudents = studentsList.length > 0;
            const classSelected = document.getElementById('class_name').value;
            const sessionSelected = document.getElementById('session_name').value;
            const examSelected = document.getElementById('exam_name').value;

            let enable = admitPrerequisitesValid && classSelected && sessionSelected && examSelected;
            if (enable) {
                if (generateType === 'single') {
                    enable = selectedStudent && selectedStudent !== '';
                } else {
                    enable = hasStudents;
                }
            }

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
                populateStudentDropdown();
                updateAdmitSubmitState();
            });
        }

        function toggleStudentSelect() {
            const type = document.getElementById('generate_type').value;
            const container = document.getElementById('single_student_container');
            const statusBox = document.getElementById('studentStatusBox');
            if (type === 'single') {
                container.classList.remove('hidden');
                statusBox.classList.add('hidden');
            } else {
                container.classList.add('hidden');
                statusBox.classList.remove('hidden');
            }
            updateAdmitSubmitState();
        }

        function populateStudentDropdown() {
            const students = studentsList.map(student => ({
                id: student.student_id_number,
                student_id_number: student.student_id_number,
                student_label: `${student.student_name} (${student.student_id_number})`,
            }));
            populateDropdown('student_idMenu', students, 'student_id_number', 'student_label');
            setDropdownValue('student_id', '', 'Select Student');
            updateAdmitSubmitState();
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
                body.innerHTML += `<tr>
                    <td>${meta.from + i}</td>
                    <td>${item.class_name}</td>
                    <td>${item.group_name || '-'}</td>
                    <td>${item.section_name || '-'}</td>
                    <td>${item.session_name}</td>
                    <td>${item.exam_name}</td>
                    <td class="font-mono">${item.student_id_number}</td>
                    <td class="capitalize">${item.student_name}</td>
                    <td>${item.admit_card_number}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <button onclick='editAdmit(${JSON.stringify(item)})' class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteAdmit(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
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
                        <head><title>Preparing Admit Cards...</title></head>
                        <body style="display:flex;justify-content:center;align-items:center;height:100vh;margin:0;font-family:sans-serif;background:#f8fafc;">
                            <div style="text-align:center;">
                                <div style="border:4px solid #f3f3f3;border-top:4px solid #2563eb;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:0 auto 15px;"></div>
                                <div style="color:#64748b;font-size:14px;font-weight:600;">Generating Admit Cards...</div>
                            </div>
                            <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
                        </body>
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
                        generateMobilePreview(res.data.data, res.data.school_info, res.data.routines || [], previewWindow);
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

            if (!cardRoutines || cardRoutines.length === 0) {
                return `
                                                                                                                <tr>
                                                                                                                    <td class="border border-slate-800 p-1.5 font-mono text-center">-</td>
                                                                                                                    <td class="border border-slate-800 p-1.5 text-left px-2 font-semibold">-</td>
                                                                                                                    <td class="border border-slate-800 p-1.5 font-mono text-center">-</td>
                                                                                                                    <td class="border border-slate-800 p-1.5 text-left px-2 font-semibold">-</td>
                                                                                                                </tr>
                                                                                                            `;
            }

            // Pair routines up for 2-column display to reduce vertical space
            for (let i = 0; i < cardRoutines.length; i += 2) {
                const item1 = cardRoutines[i];
                const item2 = cardRoutines[i + 1] || null;

                const date1 = formatDate(item1.exam_date);
                const subj1 = item1.subject_name;

                const date2 = item2 ? formatDate(item2.exam_date) : '';
                const subj2 = item2 ? item2.subject_name : '';

                rowsHtml += `
                    <tr>
                        <td class="border border-slate-800 p-1.5 font-mono text-center">${date1}</td>
                        <td class="border border-slate-800 p-1.5 text-left px-2 font-semibold">${subj1}</td>
                        <td class="border border-slate-800 p-1.5 font-mono text-center">${date2}</td>
                        <td class="border border-slate-800 p-1.5 text-left px-2 font-semibold">${subj2}</td>
                    </tr>
                `;
            }

            return rowsHtml;
        }



        function generateMobilePreview(admitCards, school, routines, previewWindow) {
            if (!previewWindow) previewWindow = window.open('', '_blank');
            const address = school?.village || '';

            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = today.toLocaleString('default', { month: 'short' });
            const yyyy = today.getFullYear();
            const currentDate = `${dd}-${mm}-${yyyy}`;

            let html = `<!DOCTYPE html><html><head><title>Admit Card PDF</title>
                <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"><\/script>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
                <style>
                    @page { size: A4 portrait; margin: 0; }
                    * { border-radius: 0 !important; font-family: 'Inter', sans-serif; box-sizing: border-box; }
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
                </style></head><body>`;

            admitCards.forEach(card => {
                const cardRoutines = getRoutinesForCard(card, routines);
                const routineRowsHtml = buildRoutineTableRows(cardRoutines);

                html += `
                                                                                                                <div class="card-page">
                                                                                                                    <div class="card-inner">
                                                                                                                        <!-- Watermark -->
                                                                                                                        ${school?.logo ? `
                                                                                                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.06] z-0">
                                                                                                                                <img src="${school.logo}" class="w-64 h-64 object-contain">
                                                                                                                            </div>
                                                                                                                        ` : ''}

                                                                                                                        <div class="relative z-10 flex flex-col justify-between h-full w-full">
                                                                                                                            <!-- Header -->
                                                                                                                            <div class="border-b border-gray-400 pb-2 mb-3">
                                                                                                                                <div class="flex justify-between items-center gap-4">
                                                                                                                                    <!-- Left: School logo -->
                                                                                                                                    <div class="w-16 h-16 flex-shrink-0 flex items-center justify-start">
                                                                                                                                        ${school?.logo ? `<img src="${school.logo}" class="h-16 w-16 object-cover border border-gray-300" style="border-radius: 50% !important;">` : `
                                                                                                                                            <div class="h-16 w-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400" style="border-radius: 50% !important;">Logo</div>
                                                                                                                                        `}
                                                                                                                                    </div>

                                                                                                                                    <!-- Middle: School details -->
                                                                                                                                    <div class="text-center flex-grow px-2">
                                                                                                                                        <h1 class="text-xl font-bold text-gray-900 tracking-wide uppercase leading-tight">${school?.school_name || 'School Name'}</h1>
                                                                                                                                        <p class="text-xs text-gray-650 font-bold mt-0.5">
                                                                                                                                            ${school?.mobile ? school.mobile : ''} ${school?.mobile && school?.email ? ' | ' : ''} ${school?.email ? school.email : ''}
                                                                                                                                        </p>
                                                                                                                                        <p class="text-xs text-gray-500 font-semibold leading-tight">${address}</p>
                                                                                                                                    </div>

                                                                                                                                    <!-- Right: Student Photo -->
                                                                                                                                    <div class="w-16 h-16 flex-shrink-0 flex items-center justify-end">
                                                                                                                                        ${card.student_image ? `
                                                                                                                                            <img src="${card.student_image.startsWith('http') ? card.student_image : window.location.origin + '/storage/' + card.student_image}" class="w-16 h-16 border border-slate-800 object-cover" style="border-radius: 50% !important;">
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
                                                                                                                                    <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Student Name</span><span>:</span><span class="font-bold capitalize">${card.student_name}</span></div>
                                                                                                                                    <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Student ID</span><span>:</span><span class="font-mono font-bold">${card.student_id_number}</span></div>
                                                                                                                                    <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Father's Name</span><span>:</span><span class="capitalize">${card.father_name || '-'}</span></div>
                                                                                                                                    <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:110px;">Admit Card No</span><span>:</span><span class="font-mono">${card.admit_card_number}</span></div>
                                                                                                                                </div>
                                                                                                                                <div class="w-[48%] space-y-1 text-right">
                                                                                                                                    <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Class</span><span>:</span><span class="font-bold capitalize text-left flex-grow">${card.class_name}</span></div>
                                                                                                                                    <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Group</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${card.group_name || '-'}</span></div>
                                                                                                                                    <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Section</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${card.section_name || '-'}</span></div>
                                                                                                                                    <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Session</span><span>:</span><span class="font-mono text-left flex-grow">${card.session_name}</span></div>
                                                                                                                                    <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display:inline-block;width:115px;">Exam Name</span><span>:</span><span class="font-bold text-blue-900 capitalize text-left flex-grow">${card.exam_name}</span></div>
                                                                                                                                </div>
                                                                                                                            </div>

                                                                                                                            <!-- Routine Table -->
                                                                                                                            <div class="overflow-hidden">
                                                                                                                                <table class="w-full text-center border-collapse border border-slate-800 text-[9.5px]">
                                                                                                                                    <thead>
                                                                                                                                        <tr class="bg-gray-100 font-bold text-gray-800 whitespace-nowrap">
                                                                                                                                            <th class="border border-gray-800 p-1">Date</th>
                                                                                                                                            <th class="border border-gray-800 p-1 text-left px-2">Subject</th>
                                                                                                                                            <th class="border border-gray-800 p-1">Date</th>
                                                                                                                                            <th class="border border-gray-800 p-1 text-left px-2">Subject</th>
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
                                                                                                                                    ${school?.principal_signature ? `
                                                                                                                                        <img src="${school.principal_signature}" class="max-h-9 max-w-[95px] object-contain mb-0.5">
                                                                                                                                    ` : `<div class="h-9"></div>`}
                                                                                                                                    <p class="border-t border-slate-800 pt-0.5 w-full text-[9px] text-slate-500 font-bold">Principal</p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <!-- Print button (hidden on print) -->
                                                                                                                    <div class="no-print flex justify-center mt-4">
                                                                                                                        <button onclick="window.print()" style="border:2px solid #000;background:#fff;color:#000;padding:8px 24px;font-size:10px;font-weight:900;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;" onmouseover="this.style.background='#000';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#000'">
                                                                                                                            Download PDF / Print
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                </div>`;
            });

            html += `</body></html>`;
            previewWindow.document.open();
            previewWindow.document.write(html);
            previewWindow.document.close();
        }

        function generatePrintLayout(admitCards, school, routines, printWindow) {
            if (!printWindow) printWindow = window.open('', '_blank');
            const address = school?.village || '';

            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = today.toLocaleString('default', { month: 'short' });
            const yyyy = today.getFullYear();
            const currentDate = `${dd}-${mm}-${yyyy}`;

            let html = `<html><head><title>Print Admit Cards</title>
                                                                                                                <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"><\/script>
                                                                                                                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
                                                                                                                <style>
                                                                                                                    @page { size: A4; margin: 0; }
                                                                                                                    * { border-radius: 0 !important; font-family: 'Inter', sans-serif; box-sizing: border-box; }
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
                                                                                                                </style></head><body>`;

            admitCards.forEach((card, index) => {
                if (index % 2 === 0) html += '<div class="print-page">';

                const cardRoutines = getRoutinesForCard(card, routines);
                const routineRowsHtml = buildRoutineTableRows(cardRoutines);

                html += `
                                                                                                                <div class="relative flex flex-col justify-between overflow-hidden p-5" style="height: 133mm; border: 1px solid #1f2937; box-sizing: border-box;">
                                                                                                                    <!-- Watermark -->
                                                                                                                    ${school?.logo ? `
                                                                                                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.06] z-0">
                                                                                                                            <img src="${school.logo}" class="w-64 h-64 object-contain">
                                                                                                                        </div>
                                                                                                                    ` : ''}

                                                                                                                    <div class="relative z-10 flex flex-col justify-between h-full w-full">
                                                                                                                        <!-- Upper Header Section with school logo left, centered details, student photo right -->
                                                                                                                        <div class="border-b border-gray-400 pb-2 mb-3">
                                                                                                                            <div class="flex justify-between items-center gap-4">
                                                                                                                                <!-- Left: School logo -->
                                                                                                                                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-start">
                                                                                                                                    ${school?.logo ? `<img src="${school.logo}" class="h-16 w-16 object-cover border border-gray-300" style="border-radius: 50% !important;">` : `
                                                                                                                                        <div class="h-16 w-16 border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-gray-400" style="border-radius: 50% !important;">Logo</div>
                                                                                                                                    `}
                                                                                                                                </div>

                                                                                                                                <!-- Middle: Center School details & Badge -->
                                                                                                                                <div class="text-center flex-grow px-2">
                                                                                                                                    <h1 class="text-xl font-bold text-gray-900 tracking-wide uppercase leading-tight">${school?.school_name || 'School Name'}</h1>
                                                                                                                                    <p class="text-xs text-gray-650 font-bold mt-0.5">
                                                                                                                                        ${school?.mobile ? school.mobile : ''} ${school?.mobile && school?.email ? ' | ' : ''} ${school?.email ? school.email : ''}
                                                                                                                                    </p>
                                                                                                                                    <p class="text-xs text-gray-500 font-semibold leading-tight">${address}</p>
                                                                                                                                </div>

                                                                                                                                <!-- Right: Student Photo (Upper Right) -->
                                                                                                                                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-end">
                                                                                                                                    ${card.student_image ? `
                                                                                                                                        <img src="${card.student_image.startsWith('http') ? card.student_image : window.location.origin + '/storage/' + card.student_image}" class="w-16 h-16 border border-slate-800 object-cover" style="border-radius: 50% !important;">
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
                                                                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Student Name</span><span>:</span><span class="font-bold capitalize">${card.student_name}</span></div>
                                                                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Student ID</span><span>:</span><span class="font-mono font-bold">${card.student_id_number}</span></div>
                                                                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Father's Name</span><span>:</span><span class="capitalize">${card.father_name || '-'}</span></div>
                                                                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display: inline-block; width: 110px;">Admit Card No</span><span>:</span><span class="font-mono">${card.admit_card_number}</span></div>
                                                                                                                            </div>

                                                                                                                            <!-- Right Info (48%) -->
                                                                                                                            <div class="w-[48%] space-y-1 text-right">
                                                                                                                                <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Class</span><span>:</span><span class="font-bold capitalize text-left flex-grow">${card.class_name}</span></div>
                                                                                                                                <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Group</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${card.group_name || '-'}</span></div>
                                                                                                                                <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Section</span><span>:</span><span class="font-semibold text-slate-800 capitalize text-left flex-grow">${card.section_name || '-'}</span></div>
                                                                                                                                <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Session</span><span>:</span><span class="font-mono text-left flex-grow">${card.session_name}</span></div>
                                                                                                                                <div class="flex items-baseline justify-end gap-1"><span class="font-semibold text-gray-600 shrink-0 text-left" style="display: inline-block; width: 115px;">Exam Name</span><span>:</span><span class="font-bold text-blue-900 capitalize text-left flex-grow">${card.exam_name}</span></div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <!-- Routine Table -->
                                                                                                                        <div class="overflow-hidden">
                                                                                                                            <table class="w-full text-center border-collapse border border-slate-800 text-[9.5px]">
                                                                                                                                <thead>
                                                                                                                                    <tr class="bg-gray-100 font-bold  text-gray-800 whitespace-nowrap">
                                                                                                                                        <th class="border border-gray-800 p-1">Date</th>
                                                                                                                                        <th class="border border-gray-800 p-1 text-left px-2">Subject</th>
                                                                                                                                        <th class="border border-gray-800 p-1">Date</th>
                                                                                                                                        <th class="border border-gray-800 p-1 text-left px-2">Subject</th>
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
                                                                                                                                ${school?.principal_signature ? `
                                                                                                                                    <img src="${school.principal_signature}" class="max-h-9 max-w-[95px] object-contain mb-0.5">
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
                                                                                                            </body></html>`;
            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
        }

        async function editAdmit(item) {
            openAdmitModal();
            document.getElementById('admitModalTitle').innerText = 'Edit Individual Admit Card';
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

            const generateType = document.getElementById('generate_type').value;
            let finalStudents = studentsList;

            if (generateType === 'single') {
                const selectedId = document.getElementById('student_id').value;
                if (!selectedId) {
                    Swal.fire('Warning', 'Please select a student', 'warning');
                    btn.disabled = false;
                    document.getElementById('btnSpinner').classList.add('hidden');
                    document.getElementById('btnText').textContent = 'Generate';
                    return;
                }
                finalStudents = studentsList.filter(s => s.student_id_number === selectedId);
            }

            const payload = {
                class_name: document.getElementById('class_name').value,
                group_name: document.getElementById('group_name').value,
                section_name: document.getElementById('section_name').value,
                session_name: document.getElementById('session_name').value,
                exam_name: document.getElementById('exam_name').value,
                students: finalStudents
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

            document.getElementById('header_search').value = '';
            fetchTable(1);
            toggleFilterModal();
        }

        function openAdmitModal() {
            document.getElementById('admitForm').reset();
            document.getElementById('admit_edit_id').value = '';
            document.getElementById('admitModalTitle').innerText = 'Bulk Admit Card Generator';
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
            document.getElementById('btnText').innerText = 'Generate All Cards';
            setDropdownValue('class_name', '', 'Select Class');
            setDropdownValue('group_name', '', 'Select Group');
            setDropdownValue('section_name', '', 'Select Section');
            setDropdownValue('session_name', '', 'Select Session');
            setDropdownValue('exam_name', '', 'Select Exam');
            setDropdownValue('student_id', '', 'Select Student');
            setDropdownValue('generate_type', 'all', 'All Students');
            document.getElementById('single_student_container').classList.add('hidden');
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
        document.getElementById('generate_type')?.addEventListener('change', function() {
            toggleStudentSelect();
            updateAdmitSubmitState();
        });
        document.getElementById('student_id')?.addEventListener('change', updateAdmitSubmitState);
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
