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

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #edf2f7;
        }

        .pagination-btn {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            min-width: 35px;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
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

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            outline: none;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }


        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
        }

        .badge-target {
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 2px;
            text-transform: capitalize;
            display: inline-block;
            margin-right: 2px;
            margin-bottom: 2px;
        }

        .bg-blue-badge {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        .bg-gray-badge {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        {{-- Dynamic Page Title --}}
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>

                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[8px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>

                        {{-- Desktop Search --}}
                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="holidaySearch" placeholder="Search reason..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="toggleModal('filterModal', true)"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="toggleModal('exportModal', true)"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Holiday
                        </button>
                    </div>
                </div>

                {{-- Mobile Search --}}
                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="holidaySearchMobile" placeholder="Search reason..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="toggleModal('filterModal', false)">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Holiday Filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="groupFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="sectionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="sessionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button id="resetFilter"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button id="applyFilter"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="toggleModal('exportModal', false)">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl"
                    onclick="event.stopPropagation()">
                    <div class="flex flex-col gap-1.5">
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button onclick="toggleModal('exportModal', false)"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th width="60">SL</th>
                                <th width="100">Type</th>
                                <th width="220">Target Audience</th>
                                <th>Holiday Reason</th>
                                <th width="120">Start</th>
                                <th width="120">End</th>
                                <th width="100">Total</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="holidayTableBody"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo"></div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Holiday Modal (Strict Select Labels Fix) --}}
    <div id="holidayModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'holidayModal')">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Add holiday
                </h3>
            </div>

            <form id="holidayForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="edit_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        {{-- Holiday Type --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Holiday
                                type</label>
                            <select id="type"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] bg-white outline-none focus:border-blue-500"
                                onchange="toggleFields()" required style="border-radius: 0;">
                                <option value="General">General (Whole school)</option>
                                <option value="Class Wise">Class specific</option>
                            </select>
                        </div>

                        {{-- Class Specific Fields --}}
                        <div id="classFields" class="col-span-1 sm:col-span-2 hidden">
                            <div
                                class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-3 bg-white border border-gray-100 mb-2 overflow-visible">
                                <div>
                                    <label class="text-[9px] capitalize text-gray-400 block mb-1">Class</label>
                                    <select id="class_name"
                                        class="form-input-fixed w-full border border-gray-200 text-[11px] h-[32px] px-2 py-0 bg-white outline-none focus:border-blue-500"
                                        style="border-radius: 0;">
                                        <option value="">Select class</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[9px] capitalize text-gray-400 block mb-1">Group</label>
                                    <select id="group_name"
                                        class="form-input-fixed w-full border border-gray-200 text-[11px] h-[32px] px-2 py-0 bg-white outline-none focus:border-blue-500"
                                        style="border-radius: 0;">
                                        <option value="">Select group</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[9px] capitalize text-gray-400 block mb-1">Section</label>
                                    <select id="section_name"
                                        class="form-input-fixed w-full border border-gray-200 text-[11px] h-[32px] px-2 py-0 bg-white outline-none focus:border-blue-500"
                                        style="border-radius: 0;">
                                        <option value="">Select section</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[9px] capitalize text-gray-400 block mb-1">Session</label>
                                    <select id="session"
                                        class="form-input-fixed w-full border border-gray-200 text-[11px] h-[32px] px-2 py-0 bg-white outline-none focus:border-blue-500"
                                        style="border-radius: 0;">
                                        <option value="">Select session</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Holiday
                                reason</label>
                            <input type="text" id="reason" placeholder="e.g. Winter vacation"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs text-gray-700 h-[32px] outline-none focus:border-blue-500"
                                required style="border-radius: 0;">
                        </div>

                        {{-- Dates --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Start
                                date</label>
                            <input type="date" id="start_date" onchange="calcDays()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                                required style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">End
                                date</label>
                            <input type="date" id="end_date" onchange="calcDays()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                                required style="border-radius: 0;">
                        </div>

                        {{-- Total Days (Calculated) --}}
                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total
                                calculated days</label>
                            <input type="number" id="total_days" readonly
                                class="form-input-fixed w-full border border-gray-100 bg-gray-50 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                required style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let currentPage = 1;

        // Standard Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        /**
         * CASCADE LOGIC (Shared by both Form and Filter Modal)
         */
        window.handleCascade = async function(element, step, isFilter = false) {
            const val = element.value;
            const prefix = isFilter ? 'Filter' : ''; // IDs for filters end with 'Filter' (e.g., classFilter)

            // Define target IDs based on whether we are in the Filter Modal or Create Modal
            const groupEl = isFilter ? document.getElementById('groupFilter') : document.getElementById(
                'group_name');
            const sectionEl = isFilter ? document.getElementById('sectionFilter') : document.getElementById(
                'section_name');
            const sessionEl = isFilter ? document.getElementById('sessionFilter') : document.getElementById(
                'session');

            if (step === 'class') {
                groupEl.innerHTML = '<option value="">Select group</option>';
                sectionEl.innerHTML = '<option value="">Select section</option>';
                sessionEl.innerHTML = '<option value="">Select session</option>';
                if (val) {
                    await loadGroups(val, groupEl);
                    await loadSessions(isFilter);
                }
            } else if (step === 'group') {
                sectionEl.innerHTML = '<option value="">Select section</option>';
                if (val) {
                    await loadSections(val, sectionEl);
                    await loadSessions(isFilter);
                }
            } else if (step === 'section') {
                await loadSessions(isFilter);
            }
        }

        async function loadClasses() {
            try {
                const res = await axios.get('/api/get-school-classes');
                const data = res.data.data || [];
                const mainSelect = document.getElementById('class_name');
                const filterSelect = document.getElementById('classFilter');

                let options = '<option value="">Select class</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.class_name}">${item.class_name}</option>`;
                });

                if (mainSelect) mainSelect.innerHTML = options;
                if (filterSelect) filterSelect.innerHTML = options;
            } catch (err) {
                console.error("Class load failed", err);
            }
        }

        async function loadGroups(classId, targetElement) {
            try {
                const res = await axios.get(`/api/get-school-groups?class_id=${classId}`);
                const data = res.data.data || [];
                let options = '<option value="">Select group</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.group_name}">${item.group_name}</option>`;
                });
                targetElement.innerHTML = options;
            } catch (err) {
                console.error("Group load failed", err);
            }
        }

        async function loadSections(groupId, targetElement) {
            try {
                const res = await axios.get(`/api/get-school-sections?group_id=${groupId}`);
                const data = res.data.data || [];
                let options = '<option value="">Select section</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.section_name}">${item.section_name}</option>`;
                });
                targetElement.innerHTML = options;
            } catch (err) {
                console.error("Section load failed", err);
            }
        }

        async function loadSessions(isFilter = false) {
            const classId = isFilter ? document.getElementById('classFilter')?.value : document.getElementById(
                'class_name')?.value;
            const groupId = isFilter ? document.getElementById('groupFilter')?.value : document.getElementById(
                'group_name')?.value;
            const sectionId = isFilter ? document.getElementById('sectionFilter')?.value : document.getElementById(
                'section_name')?.value;
            const select = isFilter ? document.getElementById('sessionFilter') : document.getElementById('session');

            const params = {
                class_id: classId || null,
                group_id: groupId || null,
                section_id: sectionId || null
            };

            try {
                const res = await axios.get(`/api/get-school-sessions`, {
                    params
                });
                const data = res.data.data || [];
                let options = '<option value="">Select session</option>';
                data.forEach(item => {
                    options += `<option value="${item.session_year}">${item.session_year}</option>`;
                });
                select.innerHTML = options;
            } catch (err) {
                console.error("Session load failed", err);
            }
        }

        /**
         * MODAL UTILITIES
         */
        window.toggleModal = function(modalId, show) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            show ? modal.classList.remove('hidden') : modal.classList.add('hidden');
        }

        window.openModal = function() {
            document.getElementById('holidayForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = 'Record Holiday';
            toggleModal('holidayModal', true);
            toggleFields();
        }

        window.closeModal = function() {
            toggleModal('holidayModal', false);
        }

        window.toggleFields = function() {
            const type = document.getElementById('type').value;
            const classFields = document.getElementById('classFields');
            const isClassWise = type === 'Class Wise';
            if (classFields) classFields.classList.toggle('hidden', !isClassWise);

            ['class_name', 'section_name', 'group_name', 'session'].forEach(f => {
                const el = document.getElementById(f);
                if (el) el.required = isClassWise;
            });
        }

        window.calcDays = function() {
            const startVal = document.getElementById('start_date').value;
            const endVal = document.getElementById('end_date').value;
            if (startVal && endVal) {
                const start = new Date(startVal);
                const end = new Date(endVal);
                const diffDays = end >= start ? Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1 : 0;
                document.getElementById('total_days').value = diffDays;
            }
        }

        /**
         * FETCH & FILTER DATA
         */
        window.fetchHolidays = function(page = 1) {
            currentPage = page;
            const searchInput = document.getElementById('holidaySearch');
            const search = searchInput ? searchInput.value : '';

            // Get values from Filter Modal
            const classSel = document.getElementById('classFilter');
            const groupSel = document.getElementById('groupFilter');
            const sectionSel = document.getElementById('sectionFilter');

            const params = {
                page: page,
                search: search,
                class_name: classSel?.options[classSel.selectedIndex]?.getAttribute('data-name') || '',
                group_name: groupSel?.options[groupSel.selectedIndex]?.getAttribute('data-name') || '',
                section_name: sectionSel?.options[sectionSel.selectedIndex]?.getAttribute('data-name') || '',
                session: document.getElementById('sessionFilter')?.value || ''
            };

            axios.get(`/api/school-holidays`, {
                params
            }).then(res => {
                const items = res.data.data;
                const meta = res.data;
                const tbody = document.getElementById('holidayTableBody');
                if (!tbody) return;
                tbody.innerHTML = '';

                if (items.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="8" class="text-center py-6 text-gray-400 tracking-tighter text-[11px]">No holidays found</td></tr>`;
                }

                items.forEach((h, i) => {
                    const formatDate = (dateStr) => {
                        if (!dateStr) return 'N/A';
                        const d = new Date(dateStr);
                        return isNaN(d.getTime()) ? dateStr :
                            `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
                    };

                    let targetHtml = h.type === 'General' ?
                        `<span class="text-gray-400 text-[10px] whitespace-nowrap leading-tight">Entire School</span>` :
                        `<div class="flex items-center gap-1.5 leading-tight">
                        <span class="text-gray-600 text-[10px] whitespace-nowrap">${h.class_name || 'N/A'}</span>
                        <div class="flex gap-1">
                            <span class="px-1 py-0.5 bg-gray-50 text-gray-500 text-[8px] border border-gray-100 leading-none">${h.section_name || 'N/A'}</span>
                            <span class="px-1 py-0.5 bg-gray-50 text-gray-500 text-[8px] border border-gray-100 leading-none">${h.session || 'N/A'}</span>
                        </div>
                    </div>`;

                    tbody.innerHTML += `
                    <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-100">
                        <td class="text-gray-400 text-[10px] px-2 py-1.5">${(meta.from || 1) + i}</td>
                        <td class="py-1.5"><span class="px-1.5 py-0.5 text-[8px] tracking-wider bg-gray-100 text-gray-500 border border-gray-200">${h.type}</span></td>
                        <td class="py-1.5">${targetHtml}</td>
                        <td class="text-gray-600 text-[11px] py-1.5 truncate max-w-[150px]">${h.reason}</td>
                        <td class="text-gray-500 text-[10px] py-1.5 whitespace-nowrap">${formatDate(h.start_date)}</td>
                        <td class="text-gray-500 text-[10px] py-1.5 whitespace-nowrap">${formatDate(h.end_date)}</td>
                        <td class="text-blue-600 text-[10px] py-1.5 font-medium leading-none">${h.total_days} Days</td>
                        <td class="text-center py-1.5">
                            <div class="flex justify-center gap-3">
                                <button onclick="editHoliday(${h.id})" class="action-icon-btn text-blue-500 hover:text-blue-700 transition-colors"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                <button onclick="deleteHoliday(${h.id})" class="action-icon-btn text-red-400 hover:text-red-600 transition-colors"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                renderPagination(meta);
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (info) info.innerText = `${meta.to || 0} of ${meta.total}`;
            if (!controls) return;
            controls.innerHTML = '';

            const createBtn = (content, page, disabled, active = false) => {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${active ? 'active' : ''}`;
                btn.innerHTML = content;
                btn.disabled = disabled;
                btn.onclick = () => fetchHolidays(page);
                return btn;
            };

            controls.appendChild(createBtn('<i class="mdi mdi-chevron-left"></i>', meta.current_page - 1, meta
                .current_page === 1));
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    controls.appendChild(createBtn(i, i, false, meta.current_page === i));
                }
            }
            controls.appendChild(createBtn('<i class="mdi mdi-chevron-right"></i>', meta.current_page + 1, meta
                .current_page === meta.last_page));
        }

        // APPLY & RESET FILTERS
        document.getElementById('applyFilter').onclick = function() {
            fetchHolidays(1);
            toggleModal('filterModal', false);
        };

        document.getElementById('resetFilter').onclick = function() {
            document.getElementById('classFilter').value = '';
            document.getElementById('groupFilter').innerHTML = '<option value="">Select group</option>';
            document.getElementById('sectionFilter').innerHTML = '<option value="">Select section</option>';
            document.getElementById('sessionFilter').innerHTML = '<option value="">Select session</option>';
            fetchHolidays(1);
            toggleModal('filterModal', false);
        };

        // Form Submission (Create/Update)
        document.getElementById('holidayForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const saveBtn = document.getElementById('saveBtn');
            const classSel = document.getElementById('class_name');
            const groupSel = document.getElementById('group_name');
            const sectionSel = document.getElementById('section_name');

            const data = {
                type: document.getElementById('type').value,
                class_name: classSel.options[classSel.selectedIndex]?.getAttribute('data-name') || '',
                group_name: groupSel.options[groupSel.selectedIndex]?.getAttribute('data-name') || '',
                section_name: sectionSel.options[sectionSel.selectedIndex]?.getAttribute('data-name') || '',
                session: document.getElementById('session').value,
                reason: document.getElementById('reason').value,
                start_date: document.getElementById('start_date').value,
                end_date: document.getElementById('end_date').value,
                total_days: document.getElementById('total_days').value,
            };

            saveBtn.disabled = true;
            const request = id ? axios.put(`/api/school-holidays/${id}`, data) : axios.post('/api/school-holidays',
                data);

            request.then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Holiday saved successfully'
                });
                closeModal();
                fetchHolidays(currentPage);
            }).catch(err => {
                Toast.fire({
                    icon: 'error',
                    title: err.response?.data?.message || "Save failed"
                });
            }).finally(() => {
                saveBtn.disabled = false;
            });
        };

        window.editHoliday = async function(id) {
            try {
                const res = await axios.get(`/api/school-holidays/${id}`);
                const h = res.data;
                document.getElementById('edit_id').value = h.id;
                document.getElementById('type').value = h.type;
                document.getElementById('reason').value = h.reason;
                document.getElementById('start_date').value = h.start_date;
                document.getElementById('end_date').value = h.end_date;
                document.getElementById('total_days').value = h.total_days;

                toggleFields();

                if (h.class_name) {
                    const classSel = document.getElementById('class_name');
                    for (let opt of classSel.options) {
                        if (opt.getAttribute('data-name') === h.class_name) {
                            classSel.value = opt.value;
                            break;
                        }
                    }
                    if (classSel.value) {
                        await loadGroups(classSel.value, document.getElementById('group_name'));
                        const groupSel = document.getElementById('group_name');
                        for (let opt of groupSel.options) {
                            if (opt.getAttribute('data-name') === h.group_name) {
                                groupSel.value = opt.value;
                                break;
                            }
                        }
                        if (groupSel.value) {
                            await loadSections(groupSel.value, document.getElementById('section_name'));
                            const sectionSel = document.getElementById('section_name');
                            for (let opt of sectionSel.options) {
                                if (opt.getAttribute('data-name') === h.section_name) {
                                    sectionSel.value = opt.value;
                                    break;
                                }
                            }
                        }
                        await loadSessions(false);
                        document.getElementById('session').value = h.session || '';
                    }
                }
                document.getElementById('modalTitle').innerText = 'Update Holiday';
                toggleModal('holidayModal', true);
            } catch (err) {
                console.error("Edit failed", err);
            }
        }

        window.deleteHoliday = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/school-holidays/${id}`).then(() => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Holiday deleted'
                        });
                        fetchHolidays(currentPage);
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Form Cascades
            document.getElementById('class_name')?.addEventListener('change', function() {
                handleCascade(this, 'class', false);
            });
            document.getElementById('group_name')?.addEventListener('change', function() {
                handleCascade(this, 'group', false);
            });
            document.getElementById('section_name')?.addEventListener('change', function() {
                handleCascade(this, 'section', false);
            });

            // Filter Cascades
            document.getElementById('classFilter')?.addEventListener('change', function() {
                handleCascade(this, 'class', true);
            });
            document.getElementById('groupFilter')?.addEventListener('change', function() {
                handleCascade(this, 'group', true);
            });
            document.getElementById('sectionFilter')?.addEventListener('change', function() {
                handleCascade(this, 'section', true);
            });

            loadClasses();
            fetchHolidays();
        });
    </script>
@endsection
