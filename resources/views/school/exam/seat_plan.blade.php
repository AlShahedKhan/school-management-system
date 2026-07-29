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



        .action-icon {
            font-size: 1.25rem;
        }

        /* Fixed Search Padding */
        .search-wrapper {
            position: relative;
            width: 16rem;
        }

        .search-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .search-wrapper input {
            padding-left: 36px !important;
        }

        .mode-tab-btn {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.4rem 0.7rem;
            transition: all 0.2s ease;
        }

        .mode-tab-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .multi-option-list {
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 0.45rem;
            display: grid;
            gap: 0.35rem;
        }

        .multi-option-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 11px;
            color: #475569;
            cursor: pointer;
        }

        .multi-option-item input {
            accent-color: #2563eb;
        }

        /* Increased margin for clarity */
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
                                onkeyup="if(event.key === 'Enter') fetchTable(1)"
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
                        <x-button.secondary onclick="toggleFilterModal()" class="w-full lg:w-auto">
                            Filter
                        </x-button.secondary>

                        <x-dropdown button-id="btnSeatPlanExport" menu-id="seatPlanExportDropdown" label="Export">
                            <x-dropdown.item onclick="exportSeatPlans('pdf')">PDF</x-dropdown.item>
                            <x-dropdown.item onclick="exportSeatPlans('excel')">Excel</x-dropdown.item>
                            <x-dropdown.item onclick="exportSeatPlans('print')">Print</x-dropdown.item>
                        </x-dropdown>

                        <x-button.primary onclick="openSeatModal()" class="w-full lg:w-auto">
                            Seat Number
                        </x-button.primary>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="header_search_mobile" placeholder="Search ID or Name..."
                        onkeyup="if(event.key === 'Enter') { document.getElementById('header_search').value = this.value; fetchTable(1); }"
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <x-modal.form
                id="filterModal"
                form-id="seatPlanFilterForm"
                title="Search Filters"
                close-button-id="closeSeatPlanFilterModal"
                class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/50 p-4 sm:p-20"
                panel-class="modal-content-sharp w-full max-w-[320px] overflow-hidden bg-white shadow-2xl"
                panel-style="border-radius:0;"
                header-class="border-b border-gray-200 bg-white px-4 py-3"
                title-class="m-0 text-center text-[13px] font-medium capitalize leading-tight tracking-normal text-gray-800"
                form-class="m-0"
                body-class="bg-white px-4 py-3"
                fields-class="space-y-3"
                onclick="if (event.target === this) this.classList.add('hidden')"
                onsubmit="event.preventDefault(); applyFilters();"
            >
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-gray-500">Class</label>
                    <x-input.dropdown-select id="filter_class_name" placeholder="Select Class" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-gray-500">Group</label>
                    <x-input.dropdown-select id="filter_group_name" placeholder="Select Group" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-gray-500">Section</label>
                    <x-input.dropdown-select id="filter_section_name" placeholder="Select Section" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-gray-500">Session</label>
                    <x-input.dropdown-select id="filter_session_name" placeholder="Select Session" :options="[]" />
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-gray-500">Exam Name</label>
                    <x-input.dropdown-select id="filter_exam_name" placeholder="Select Exam" :options="[]" />
                </div>

                <x-slot:footer>
                    <div class="grid grid-cols-2 gap-2 bg-white px-4 pb-4">
                        <x-button.secondary type="button" onclick="resetFilters()" class="w-full">Reset</x-button.secondary>
                        <x-button.primary type="submit" class="w-full">Apply</x-button.primary>
                    </div>
                </x-slot:footer>
            </x-modal.form>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1000px]">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Exam Name</th>
                                <th>Student Id</th>
                                <th>Student Name</th>
                                <th>Seat Number</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="seatTableBody"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase" id="paginationInfo"></div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Seat Number Modal --}}
    <div id="seatModal" role="dialog" aria-modal="true" aria-labelledby="seatModalTitle"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Modal Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="seatModalTitle" class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Bulk Seat Plan Generator
                </h3>
            </div>

            <form id="seatForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="flex gap-2 mb-4">
                        <button type="button" onclick="switchGenerationMode('single')" id="singleModeTab" class="mode-tab-btn active">Single Class</button>
                        <button type="button" onclick="switchGenerationMode('multi')" id="multiModeTab" class="mode-tab-btn">Multi Class</button>
                    </div>

                    <div id="singleModePanel" class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <x-input.dropdown-select id="class_name" name="class_name" placeholder="Select Class" :options="[]" required
                                add-button-id="openClassFromSeatForm" add-button-label="Add class" add-button-target="classModal" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <x-input.dropdown-select id="group_name" name="group_name" placeholder="Select Group" :options="[]"
                                add-button-id="openGroupFromSeatForm" add-button-label="Add group" add-button-target="groupModal" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <x-input.dropdown-select id="section_name" name="section_name" placeholder="Select Section" :options="[]"
                                add-button-id="openSectionFromSeatForm" add-button-label="Add section" add-button-target="sectionModal" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <x-input.dropdown-select id="session_name" name="session_name" placeholder="Select Session" :options="[]" required
                                add-button-id="openSessionFromSeatForm" add-button-label="Add session" add-button-target="sessionModal" />
                        </div>
                    </div>

                    <div id="multiModePanel" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class (Multiple)</label>
                            <div id="multi_class_options" class="multi-option-list" onchange="handleMultiSelectionChange(event)"></div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group (Multiple)</label>
                            <div id="multi_group_options" class="multi-option-list" onchange="handleMultiSelectionChange(event)"></div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section (Multiple)</label>
                            <div id="multi_section_options" class="multi-option-list" onchange="handleMultiSelectionChange(event)"></div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session (Multiple)</label>
                            <div id="multi_session_options" class="multi-option-list" onchange="handleMultiSelectionChange(event)"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4 mt-4">
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Exam Name</label>
                            <x-input.dropdown-select id="exam_name" name="exam_name" placeholder="Select Exam" :options="[]" required
                                add-button-id="openExamFromSeatForm" add-button-label="Add exam" add-button-target="examModal" />
                        </div>

                        <div class="col-span-1 sm:col-span-2 bg-blue-50/30 p-3 border border-dashed border-blue-200 mt-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-400 mb-1">Students Identified</label>
                            <div id="studentCountDisplay"
                                class="text-[11px] font-mono font-bold text-blue-600 tracking-tighter">0 Students Identified</div>
                        </div>

                        <div class="col-span-1 mt-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Seat Number Start</label>
                            <input type="number" id="seat_number_start" name="seat_number_start" oninput="calculateEnd()" onkeyup="calculateEnd()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] font-mono"
                                placeholder="e.g. 101" required style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1 mt-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Seat Number End
                                (Auto)</label>
                            <input type="text" id="seat_number_end" name="seat_number_end" readonly
                                class="form-input-fixed w-full border border-gray-100 bg-gray-50 py-1.5 px-3 text-xs h-[32px] font-mono text-gray-400"
                                style="border-radius: 0;" />
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <x-button.secondary type="button" onclick="closeSeatModal()" class="w-1/2 sm:w-auto sm:px-8">
                        Cancel
                    </x-button.secondary>
                    <x-button.primary type="submit" id="submitBtn" disabled class="w-1/2 sm:w-auto sm:px-12 disabled:opacity-50">
                        Generate
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick-create modals shared with the Exam Routine and Admit Card forms. --}}
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
        let activeGenerationMode = 'single';

        function setDropdownValue(id, value, label, shouldNotify = false) {
            const input = document.getElementById(id);
            const root = input?.closest('[data-dropdown-select]');
            const labelElement = root?.querySelector('[data-dropdown-select-label]');
            const menu = document.getElementById(`${id}Menu`);

            if (!input || !root || !labelElement || !menu) return;

            input.value = value || '';
            labelElement.textContent = label || labelElement.dataset.placeholder || 'Select...';
            menu.classList.add('hidden');
            root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');

            menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                const selected = String(option.dataset.value || '') === String(value || '');
                option.classList.toggle('bg-slate-100', selected);
                option.classList.toggle('text-slate-900', selected);
                option.classList.toggle('text-slate-800', !selected);
                option.setAttribute('aria-selected', String(selected));
            });

            if (shouldNotify) {
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        function populateDropdown(menuId, data, valueField, labelField) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            menu.innerHTML = '';
            (data || []).forEach(item => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
                option.dataset.value = String(item[valueField] ?? '');
                option.dataset.optionId = String(item.id ?? '');
                option.textContent = item[labelField] ?? '';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.setAttribute('data-dropdown-select-option', '');

                option.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root?.querySelector('[data-dropdown-select-input]');
                    if (!input) return;

                    setDropdownValue(input.id, this.dataset.value, this.textContent.trim());
                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                menu.appendChild(option);
            });
        }

        function getDropdownOptionId(id) {
            const input = document.getElementById(id);
            const menu = document.getElementById(`${id}Menu`);
            const option = Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
                .find(item => String(item.dataset.value || '') === String(input?.value || ''));

            return option?.dataset.optionId || '';
        }

        function getSelectedDataId(elementOrId) {
            const input = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
            if (!input) return '';

            if (input.matches?.('[data-dropdown-select-input]')) {
                return getDropdownOptionId(input.id);
            }

            return input.options?.[input.selectedIndex]?.getAttribute('data-id') || input.value || '';
        }

        function getDropdownSelectSelectedLabel(id) {
            const input = document.getElementById(id);
            const menu = document.getElementById(`${id}Menu`);
            const selected = Array.from(menu?.querySelectorAll('[data-dropdown-select-option]') || [])
                .find(option => String(option.dataset.value || '') === String(input?.value || ''));

            return selected?.textContent?.trim() || '';
        }

        function initializeFilterDropdowns() {
            const cascades = {
                filter_class_name: 'group',
                filter_group_name: 'section',
                filter_section_name: 'session',
            };

            Object.entries(cascades).forEach(([id, next]) => {
                const input = document.getElementById(id);
                if (!input || input.dataset.filterCascadeReady === 'true') return;

                input.dataset.filterCascadeReady = 'true';
                input.addEventListener('change', () => handleCascade(input, next));
            });

            const filterSession = document.getElementById('filter_session_name');
            if (filterSession && filterSession.dataset.filterExamReady !== 'true') {
                filterSession.dataset.filterExamReady = 'true';
                filterSession.addEventListener('change', () => fetchFilteredExams(true));
            }
        }

        function initializeSingleDropdowns() {
            const cascades = {
                class_name: 'group',
                group_name: 'section',
                section_name: 'session',
            };

            Object.entries(cascades).forEach(([id, next]) => {
                const input = document.getElementById(id);
                if (!input || input.dataset.singleCascadeReady === 'true') return;

                input.dataset.singleCascadeReady = 'true';
                input.addEventListener('change', () => handleCascade(input, next, 'single'));
            });

            const session = document.getElementById('session_name');
            if (session && session.dataset.studentCountReady !== 'true') {
                session.dataset.studentCountReady = 'true';
                session.addEventListener('change', fetchStudentCount);
            }
        }

        function selectSeatOption(id, recordId) {
            const menu = document.getElementById(`${id}Menu`);
            if (!menu || !recordId) return false;

            const option = Array.from(menu.querySelectorAll('[data-dropdown-select-option]'))
                .find(item => String(item.dataset.optionId) === String(recordId));
            if (!option) return false;

            setDropdownValue(id, option.dataset.value, option.textContent.trim());
            return true;
        }

        async function restoreSeatSelection({ classId = null, groupId = null, sectionId = null, sessionId = null } = {}) {
            await loadInitial();

            if (classId && selectSeatOption('class_name', classId)) {
                await handleCascade(document.getElementById('class_name'), 'group', 'single');
            }
            if (groupId && selectSeatOption('group_name', groupId)) {
                await handleCascade(document.getElementById('group_name'), 'section', 'single');
            }
            if (sectionId && selectSeatOption('section_name', sectionId)) {
                await handleCascade(document.getElementById('section_name'), 'session', 'single');
            }
            if (sessionId && selectSeatOption('session_name', sessionId)) {
                await fetchFilteredExams();
                fetchStudentCount();
            }
        }

        document.addEventListener('school:class-saved', async event => {
            const { classItem, returnModalId, isNew } = event.detail || {};
            if ((returnModalId && returnModalId !== 'seatModal') || !isNew || !classItem?.id) return;

            event.preventDefault();
            await restoreSeatSelection({ classId: classItem.id });
            if (!getDropdownOptionId('class_name') && classItem.class_name) {
                setDropdownValue('class_name', classItem.class_name, classItem.class_name);
            }
            document.getElementById('seatModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:group-saved', async event => {
            const { groupItem, returnModalId, isNew } = event.detail || {};
            if ((returnModalId && returnModalId !== 'seatModal') || !isNew || !groupItem?.id) return;

            event.preventDefault();
            await restoreSeatSelection({ classId: groupItem.class_id, groupId: groupItem.id });
            document.getElementById('seatModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:section-saved', async event => {
            const { sectionItem, returnModalId, isNew } = event.detail || {};
            if ((returnModalId && returnModalId !== 'seatModal') || !isNew || !sectionItem?.id) return;

            event.preventDefault();
            await restoreSeatSelection({
                classId: sectionItem.class_id,
                groupId: sectionItem.group_id,
                sectionId: sectionItem.id,
            });
            document.getElementById('seatModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:session-saved', async event => {
            const { sessionItem, returnModalId, isNew } = event.detail || {};
            if ((returnModalId && returnModalId !== 'seatModal') || !isNew || !sessionItem?.id) return;

            event.preventDefault();
            await restoreSeatSelection({
                classId: sessionItem.class_id,
                groupId: sessionItem.group_id,
                sectionId: sessionItem.section_id,
                sessionId: sessionItem.id,
            });
            document.getElementById('seatModal')?.classList.remove('hidden');
        });

        document.addEventListener('school:exam-saved', async event => {
            const { examItem, returnModalId, isNew } = event.detail || {};
            if ((returnModalId && returnModalId !== 'seatModal') || !isNew || !examItem?.id) return;

            event.preventDefault();
            await fetchFilteredExams();
            selectSeatOption('exam_name', examItem.id);
            document.getElementById('seatModal')?.classList.remove('hidden');
        });

        async function loadInitial() {
            await axios.get('/api/get-school-classes').then(res => {
                const options = res.data.data || [];
                populateDropdown('class_nameMenu', options, 'class_name', 'class_name');
                setDropdownValue('class_name', '', 'Select Class');
                populateDropdown('filter_class_nameMenu', options, 'class_name', 'class_name');
                setDropdownValue('filter_class_name', '', 'Select Class');
                renderCheckboxGroup('multi_class_options', options, 'class_name', 'class_name', 'id');
            });

            await fetchFilteredExams();
        }

        function renderCheckboxGroup(containerId, items, valueField, labelField, idField, selectedValues = []) {
            const container = document.getElementById(containerId);
            if (!container) return;

            if (!items || !items.length) {
                container.innerHTML = '<div class="text-[10px] text-gray-400">No options available</div>';
                return;
            }

            const values = selectedValues.map(v => String(v));
            container.innerHTML = items.map(item => {
                const value = item[valueField];
                const id = item[idField];
                const checked = values.includes(String(id)) || values.includes(String(value)) ? 'checked' : '';
                return `<label class="multi-option-item"><input type="checkbox" value="${value}" data-id="${id}" ${checked} onchange="handleMultiSelectionChange(event)"> <span>${value}</span></label>`;
            }).join('');
        }

        function getCheckedValues(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return [];
            return Array.from(container.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value).filter(Boolean);
        }

        function getCheckedIds(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return [];
            return Array.from(container.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.getAttribute('data-id')).filter(Boolean);
        }

        function getSelectedValues(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return [];
            return select?.value ? [select.value] : [];
        }

        function getSelectedIds(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return [];
            const id = getDropdownOptionId(selectId);
            return id ? [id] : [];
        }

        function getCurrentSelection(mode = activeGenerationMode) {
            if (mode === 'single') {
                return {
                    class_name: document.getElementById('class_name').value,
                    group_name: document.getElementById('group_name').value,
                    section_name: document.getElementById('section_name').value,
                    session_name: document.getElementById('session_name').value,
                    class_ids: getSelectedIds('class_name'),
                    group_ids: getSelectedIds('group_name'),
                    section_ids: getSelectedIds('section_name'),
                    session_ids: getSelectedIds('session_name'),
                };
            }

            return {
                class_name: '',
                group_name: '',
                section_name: '',
                session_name: '',
                class_ids: getCheckedIds('multi_class_options'),
                group_ids: getCheckedIds('multi_group_options'),
                section_ids: getCheckedIds('multi_section_options'),
                session_ids: getCheckedIds('multi_session_options'),
            };
        }

        function fetchFilteredExams(isFilter = false) {
            const prefix = isFilter ? 'filter_' : '';
            const params = {
                class_ids: getSelectedIds(`${prefix}class_name`),
                group_ids: getSelectedIds(`${prefix}group_name`),
                section_ids: getSelectedIds(`${prefix}section_name`),
                session_ids: getSelectedIds(`${prefix}session_name`),
            };

            if (prefix === '') {
                if (activeGenerationMode === 'multi') {
                    params.class_ids = getCheckedIds('multi_class_options');
                    params.group_ids = getCheckedIds('multi_group_options');
                    params.section_ids = getCheckedIds('multi_section_options');
                    params.session_ids = getCheckedIds('multi_session_options');
                }
            }

            return axios.get('/api/get-school-exams', { params }).then(res => {
                const exams = res.data.data || [];

                if (isFilter) {
                    populateDropdown('filter_exam_nameMenu', exams, 'exam_name', 'exam_name');
                    setDropdownValue('filter_exam_name', '', 'Select Exam');
                    return;
                }

                populateDropdown('exam_nameMenu', exams, 'exam_name', 'exam_name');
                setDropdownValue('exam_name', '', 'Select Exam');
            });
        }

        async function handleCascade(el, next, mode = activeGenerationMode, callback = null) {
            const isFilter = el.id.startsWith('filter_');

            if (isFilter) {
                const id = getDropdownOptionId(el.id);
                if (!id) {
                    await fetchFilteredExams(true);
                    return;
                }
                if (next === 'group') {
                    await fetchFill(`/api/get-school-groups?class_id=${id}`, 'filter_group_name', 'Group', 'group_name');
                } else if (next === 'section') {
                    await fetchFill(`/api/get-school-sections?group_id=${id}`, 'filter_section_name', 'Section', 'section_name');
                } else if (next === 'session') {
                    const classId = getDropdownOptionId('filter_class_name');
                    await fetchFill(`/api/get-school-sessions?section_id=${id}&class_id=${classId}`, 'filter_session_name', 'Session', 'session_year');
                }
                await fetchFilteredExams(true);
                return;
            }

            if (mode === 'single') {
                const selectedClassId = getDropdownOptionId('class_name');
                if (next === 'group' && selectedClassId) {
                    await fetchFill(`/api/get-school-groups?class_id=${selectedClassId}`, 'group_name', 'Group', 'group_name');
                } else if (next === 'section') {
                    const selectedGroupId = getDropdownOptionId('group_name');
                    if (selectedGroupId) {
                        await fetchFill(`/api/get-school-sections?group_id=${selectedGroupId}`, 'section_name', 'Section', 'section_name');
                    }
                } else if (next === 'session') {
                    const selectedClassIdForSession = getDropdownOptionId('class_name');
                    const selectedSectionId = getDropdownOptionId('section_name');
                    if (selectedClassIdForSession && selectedSectionId) {
                        await fetchFill(`/api/get-school-sessions?section_id=${selectedSectionId}&class_id=${selectedClassIdForSession}`, 'session_name', 'Session', 'session_year');
                    }
                }
            } else {
                const selectedClassIds = getCheckedIds('multi_class_options');
                if (next === 'group') {
                    await fetchCheckboxOptions('/api/get-school-groups', 'multi_group_options', 'group_name', { class_ids: selectedClassIds });
                } else if (next === 'section') {
                    const selectedGroupIds = getCheckedIds('multi_group_options');
                    await fetchCheckboxOptions('/api/get-school-sections', 'multi_section_options', 'section_name', { class_ids: selectedClassIds, group_ids: selectedGroupIds });
                } else if (next === 'session') {
                    const selectedSectionIds = getCheckedIds('multi_section_options');
                    const selectedGroupIds = getCheckedIds('multi_group_options');
                    await fetchCheckboxOptions('/api/get-school-sessions', 'multi_session_options', 'session_year', { class_ids: selectedClassIds, group_ids: selectedGroupIds, section_ids: selectedSectionIds });
                }
            }

            await fetchFilteredExams();
            fetchStudentCount();
            if (callback) callback();
        }

        function fetchFill(url, tid, lbl, fld) {
            return axios.get(url).then(res => {
                const data = res.data.data || [];
                const target = document.getElementById(tid);

                if (target?.matches?.('[data-dropdown-select-input]')) {
                    populateDropdown(`${tid}Menu`, data, fld, fld);
                    setDropdownValue(tid, '', `Select ${lbl}`);
                    return;
                }

                let o = `<option value="">Select ${lbl}</option>`;
                data.forEach(i => o += `<option value="${i[fld]}" data-id="${i.id}">${i[fld]}</option>`);
                if (target) target.innerHTML = o;
            });
        }

        function fetchCheckboxOptions(url, containerId, valueField, params = {}) {
            return axios.get(url, { params }).then(res => {
                renderCheckboxGroup(containerId, res.data.data || [], valueField, valueField, 'id');
            });
        }

        function handleMultiSelectionChange(event) {
            if (activeGenerationMode !== 'multi') return;
            const target = event.target;
            if (!target || !target.matches('input[type="checkbox"]')) return;

            const containerId = target.closest('.multi-option-list')?.id;
            if (containerId === 'multi_class_options') {
                handleCascade({ id: 'multi_class_name' }, 'group', 'multi');
            } else if (containerId === 'multi_group_options') {
                handleCascade({ id: 'multi_group_name' }, 'section', 'multi');
            } else if (containerId === 'multi_section_options') {
                handleCascade({ id: 'multi_section_name' }, 'session', 'multi');
            } else {
                fetchStudentCount();
            }
        }

        function fetchStudentCount() {
            const selection = getCurrentSelection();
            const params = {
                class_ids: selection.class_ids,
                group_ids: selection.group_ids,
                section_ids: selection.section_ids,
                session_ids: selection.session_ids,
            };

            if (activeGenerationMode === 'single') {
                if (!selection.class_ids.length || !selection.session_ids.length) {
                    document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
                    document.getElementById('submitBtn').disabled = true;
                    return;
                }
            } else {
                if (!selection.class_ids.length || !selection.session_ids.length) {
                    document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
                    document.getElementById('submitBtn').disabled = true;
                    return;
                }
            }

            axios.get('/api/get-school-students', { params }).then(res => {
                studentsList = res.data.data || [];
                document.getElementById('studentCountDisplay').innerText = `${studentsList.length} Students Identified`;
                document.getElementById('submitBtn').disabled = studentsList.length === 0;
                calculateEnd();
            }).catch(err => {
                console.error('Student fetch error', err);
            });
        }

        function calculateEnd() {
            const start = document.getElementById('seat_number_start').value;
            if (start && studentsList.length > 0) {
                document.getElementById('seat_number_end').value = parseInt(start) + (studentsList.length - 1);
            } else {
                document.getElementById('seat_number_end').value = '';
            }
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

            axios.get('/api/school-exam-seat-plans', {
                params
            }).then(res => {
                const meta = res.data;
                const body = document.getElementById('seatTableBody');
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
                                                                        <td class="text-gray-700">${item.student_name}</td>
                                                                        <td>${item.seat_number}</td>
                                                                        <td class="text-center">
                                                                            <div class="flex justify-center gap-3">
                                                                                <button onclick='editSingleSeat(${JSON.stringify(item)})' class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                                                                <button onclick="deleteSeat(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>`;
                });
                renderPagination(meta);
            });
        }

        function editSingleSeat(item) {
            Swal.fire({
                title: 'Edit Seat Number',
                input: 'number',
                inputLabel: `Updating seat for ${item.student_name}`,
                inputValue: item.seat_number,
                showCancelButton: true,
                confirmButtonText: 'Update'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put(`/api/school-exam-seat-plans/${item.id}`, {
                        seat_number: result.value
                    }).then(res => {
                        Toastify({
                            text: res.data.message,
                            style: {
                                background: "#10b981"
                            }
                        }).showToast();
                        fetchTable();
                    });
                }
            });
        }

        function syncFormValidationState() {
            const singleFields = ['class_name', 'session_name'];
            singleFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = activeGenerationMode !== 'single';
                    field.required = activeGenerationMode === 'single';
                }
            });

            const multiCheckboxes = document.querySelectorAll('#multiModePanel input[type="checkbox"]');
            multiCheckboxes.forEach(checkbox => {
                checkbox.disabled = activeGenerationMode !== 'multi';
            });

            const examField = document.getElementById('exam_name');
            if (examField) {
                examField.required = true;
            }

            const startField = document.getElementById('seat_number_start');
            if (startField) {
                startField.required = true;
            }
        }

        function switchGenerationMode(mode) {
            activeGenerationMode = mode;
            document.getElementById('singleModePanel').classList.toggle('hidden', mode !== 'single');
            document.getElementById('multiModePanel').classList.toggle('hidden', mode !== 'multi');
            document.getElementById('singleModeTab').classList.toggle('active', mode === 'single');
            document.getElementById('multiModeTab').classList.toggle('active', mode === 'multi');
            syncFormValidationState();
            fetchFilteredExams();
            fetchStudentCount();
        }

        document.getElementById('seatForm').onsubmit = function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;

            const selection = getCurrentSelection();
            const payload = {
                generation_mode: activeGenerationMode,
                class_ids: selection.class_ids,
                group_ids: selection.group_ids,
                section_ids: selection.section_ids,
                session_ids: selection.session_ids,
                class_names: activeGenerationMode === 'single' ? [selection.class_name].filter(Boolean) : getCheckedValues('multi_class_options'),
                group_names: activeGenerationMode === 'single' ? [selection.group_name].filter(Boolean) : getCheckedValues('multi_group_options'),
                section_names: activeGenerationMode === 'single' ? [selection.section_name].filter(Boolean) : getCheckedValues('multi_section_options'),
                session_names: activeGenerationMode === 'single' ? [selection.session_name].filter(Boolean) : getCheckedValues('multi_session_options'),
                exam_name: document.getElementById('exam_name').value,
                seat_number_start: document.getElementById('seat_number_start').value,
                seat_number_end: document.getElementById('seat_number_end').value,
                students: studentsList
            };

            axios.post('/api/school-exam-seat-plans', payload).then(res => {
                Toastify({
                    text: res.data.message,
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeSeatModal();
                fetchTable(1);
            }).catch(err => {
                if (err.response && err.response.data.status === 'exists') {
                    Swal.fire('Already Exists', err.response.data.message, 'warning');
                } else {
                    Swal.fire('Error', 'Batch creation failed', 'error');
                }
            }).finally(() => btn.disabled = false);
        };

        function deleteSeat(id) {
            Swal.fire({
                title: 'Remove record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444'
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-exam-seat-plans/${id}`).then(() => fetchTable());
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

            const headerSearch = document.getElementById('header_search');
            if (headerSearch) headerSearch.value = '';
            fetchTable(1);
            toggleFilterModal();
        }

        function openSeatModal() {
            document.getElementById('seatForm').reset();
            const dropdowns = {
                class_name: 'Select Class',
                group_name: 'Select Group',
                section_name: 'Select Section',
                session_name: 'Select Session',
                exam_name: 'Select Exam',
            };
            Object.entries(dropdowns).forEach(([id, placeholder]) => {
                setDropdownValue(id, '', placeholder);
            });
            document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
            document.getElementById('submitBtn').disabled = true;
            switchGenerationMode('single');
            document.getElementById('seatModal').classList.remove('hidden');
        }

        function closeSeatModal() {
            document.getElementById('seatModal').classList.add('hidden');
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
                }
            }
            controls.innerHTML +=
                `<button class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTable(${meta.current_page + 1})"><i class="mdi mdi-chevron-right"></i></button>`;
        }

        function getSeatPlanExportParams() {
            return {
                all: 'true',
                class_name: document.getElementById('filter_class_name').value,
                group_name: document.getElementById('filter_group_name').value,
                section_name: document.getElementById('filter_section_name').value,
                session_name: document.getElementById('filter_session_name').value,
                exam_name: document.getElementById('filter_exam_name').value,
                search: document.getElementById('header_search').value
            };
        }

        function closeSeatPlanExport() {
            const menu = document.getElementById('seatPlanExportDropdown');
            const trigger = document.getElementById('btnSeatPlanExport');
            menu?.classList.add('hidden');
            trigger?.setAttribute('aria-expanded', 'false');
        }

        function exportSeatPlans(type) {
            closeSeatPlanExport();

            axios.get('/api/school-exam-seat-plans', {
                params: getSeatPlanExportParams()
            }).then(res => {
                const items = res.data.data;
                const school = res.data.school;
                if (!items || items.length === 0) {
                    Swal.fire('No Data', 'No records found to export', 'info');
                    return;
                }

                if (type === 'excel') {
                    downloadSeatPlanExcel(items);
                    return;
                }

                // Browsers use the print dialog for both printing and saving a PDF.
                if (type === 'pdf' || type === 'print') {
                    generateSeatPrintLayout(items, school);
                }
            }).catch(err => {
                console.error('Seat-plan export error', err);
                Swal.fire('Error', 'Could not export seat plans. Please try again.', 'error');
            });
        }

        function downloadSeatPlanExcel(items) {
            const headers = ['Class', 'Group', 'Section', 'Session', 'Exam Name', 'Student ID', 'Student Name', 'Seat Number'];
            const rows = items.map(item => [
                item.class_name,
                item.group_name || '',
                item.section_name || '',
                item.session_name,
                item.exam_name,
                item.student_id_number,
                item.student_name,
                item.seat_number,
            ]);
            const csvValue = value => `"${String(value ?? '').replace(/"/g, '""')}"`;
            const csv = [headers, ...rows].map(row => row.map(csvValue).join(',')).join('\r\n');
            const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'seat-plans.csv';
            link.click();
            URL.revokeObjectURL(url);
        }

        function fetchPrintData() {
            exportSeatPlans('print');
        }

        function generateSeatPrintLayout(items, school) {
            let printWindow = window.open('', '_blank');
            const address = school?.location || 'Bangladesh';
            const schoolName = school?.school_name || 'School Name';
            const watermarkImg = school?.logo ? '<img src="' + school.logo + '" class="seat-watermark" alt="watermark">' : '';

            let html = `<html><head><title>Print Seat Plans</title>
                                                                <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"><\/script>
                                                                <link rel="preconnect" href="https://fonts.googleapis.com">
                                                                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                                                                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                                                                <style>
                                                                    @page { size: A4; margin: 0; }
                                                                    body { margin: 0; padding: 0; background: #fff; font-family: 'Inter', sans-serif; }
                                                                    .print-page {
                                                                        width: 210mm;
                                                                        height: 297mm;
                                                                        background: white;
                                                                        margin: 0 auto;
                                                                        padding: 8mm 10mm;
                                                                        box-sizing: border-box;
                                                                        display: grid;
                                                                        grid-template-columns: repeat(2, minmax(0, 1fr));
                                                                        gap: 3mm 3mm;
                                                                        align-content: start;
                                                                    }
                                                                    .seat-card {
                                                                        border: 1px solid #27272a;
                                                                        padding: 1mm;
                                                                        display: flex;
                                                                        flex-direction: column;
                                                                        justify-content: space-between;
                                                                        background: white;
                                                                        height: 54mm;
                                                                        box-sizing: border-box;
                                                                        position: relative;
                                                                        overflow: hidden;
                                                                    }
                                                                    .seat-watermark {
                                                                        position: absolute;
                                                                        top: 70%;
                                                                        left: 50%;
                                                                        transform: translate(-50%, -50%);
                                                                        width: 28mm;
                                                                        height: 28mm;
                                                                        object-fit: contain;
                                                                        opacity: 0.08;
                                                                        pointer-events: none;
                                                                        z-index: 0;
                                                                    }
                                                                    .seat-card > *:not(.seat-watermark) {
                                                                        position: relative;
                                                                        z-index: 1;
                                                                    }
                                                                    .page-break { page-break-after: always; }
                                                                    @media print {
                                                                        body { background-color: #fff; -webkit-print-color-adjust: exact; }
                                                                        .print-page { box-shadow: none; }
                                                                    }
                                                                </style></head><body>`;

            items.forEach((item, index) => {
                if (index % 10 === 0) html += '<div class="print-page">';

                html += `
                                                                    <div class="seat-card">
                                                                        ${watermarkImg}
                                                                        <div class="border-b border-gray-400 pb-1 mb-2">
                                                                            <div class="text-center">
                                                                                <h1 class="text-[13px] font-bold text-gray-900 tracking-tight">${schoolName}</h1>
                                                                                <p class="text-[10px] text-gray-600 font-medium">
                                                                                    ${school?.mobile ? school.mobile : ''}${school?.mobile && school?.email ? ' | ' : ''}${school?.email ? school.email : ''}
                                                                                </p>
                                                                                <p class="text-[10px] text-gray-500 font-semibold leading-tight">${address}</p>
                                                                            </div>
                                                                            <div class="text-center mt-1">
                                                                                <span class="border border-gray-800 text-gray-900 text-[10px] font-bold px-3 py-0.5 tracking-wider uppercase">
                                                                                    Seat Number
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex justify-between items-start gap-2 text-[10px] text-gray-900 leading-relaxed my-1">
                                                                            <div class="w-[48%] space-y-1 text-left">
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:70px;">ID Number</span><span>:</span><span class="font-mono font-bold">${item.student_id_number || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:70px;">Student Name</span><span>:</span><span class="font-mono capitalize">${item.student_name || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:70px;">Exam Name</span><span>:</span><span class="font-mono text-blue-900 capitalize">${item.exam_name || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:70px;">Seat Number</span><span>:</span><span class="font-mono text-indigo-700">${item.seat_number || '-'}</span></div>
                                                                            </div>

                                                                            <div class="w-[48%] space-y-1 text-left">
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:40px;">Class</span><span>:</span><span class="font-mono capitalize">${item.class_name || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:40px;">Group</span><span>:</span><span class="font-mono capitalize">${item.group_name || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:40px;">Section</span><span>:</span><span class="font-mono capitalize">${item.section_name || '-'}</span></div>
                                                                                <div class="flex items-baseline gap-1"><span class="font-semibold text-gray-600 shrink-0" style="display:inline-block;width:40px;">Session</span><span>:</span><span class="font-mono">${item.session_name || '-'}</span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>`;

                if ((index + 1) % 10 === 0 || index === items.length - 1) {
                    html += '</div>';
                    if ((index + 1) % 10 === 0 && index !== items.length - 1) html += '<div class="page-break"></div>';
                }
            });

            html += `<script>window.onload = function() { window.print(); window.close(); };<\/script></body></html>`;
            printWindow.document.write(html);
            printWindow.document.close();
        }

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('#btnSeatPlanExport');
            const menu = document.getElementById('seatPlanExportDropdown');

            if (trigger && menu) {
                event.stopPropagation();
                menu.classList.toggle('hidden');
                trigger.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
                return;
            }

            if (!event.target.closest('#seatPlanExportDropdown') && menu) {
                closeSeatPlanExport();
            }
        });

        initializeFilterDropdowns();
        initializeSingleDropdowns();
        loadInitial();
        fetchTable();
    </script>
@endsection
