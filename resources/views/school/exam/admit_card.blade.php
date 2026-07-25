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

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="toggleFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openAdmitModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Admit Card
                        </button>
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
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="toggleFilterModal()">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Admit Card filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="filter_class_name" onchange="handleCascade(this, 'filter_group')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="filter_group_name" onchange="handleCascade(this, 'filter_section')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="filter_section_name" onchange="handleCascade(this, 'filter_session')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="filter_session_name"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Exam Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Exam Name</label>
                            <div class="relative">
                                <select id="filter_exam_name"
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
                        <button onclick="resetFilters()"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button onclick="applyFilters()"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
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
    <div id="admitModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Bulk Admit Card Generator
                </h3>
            </div>

            <form id="admitForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="edit_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_name" onchange="handleCascade(this, 'group')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" required
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_name" onchange="handleCascade(this, 'section')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_name" onchange="handleCascade(this, 'session')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_name" onchange="fetchStudentCount(); checkPrerequisiteStatus()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" required
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Exam
                                Name</label>
                            <select id="exam_name" onchange="checkPrerequisiteStatus()"
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs h-[32px]" required
                                style="border-radius: 0;"></select>
                        </div>

                        <div id="admitPrerequisiteWarning" class="col-span-1 sm:col-span-2 hidden bg-yellow-50 border border-yellow-300 text-yellow-800 p-3 text-[10px]">
                            Please create the Exam, Exam Routine, and Exam Fee before generating the Admit Card.
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Generate
                                For</label>
                            <select id="generate_type" onchange="toggleStudentSelect(); updateAdmitSubmitState()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" required
                                style="border-radius: 0;">
                                <option value="all">All Students</option>
                                <option value="single">Single Student</option>
                            </select>
                        </div>

                        <div id="single_student_container" class="col-span-1 sm:col-span-2 hidden">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Select
                                Student</label>
                            <select id="student_id" onchange="updateAdmitSubmitState()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
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

                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeAdmitModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" disabled
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap disabled:opacity-50"
                        style="border-radius: 0;">
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
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let studentsList = [];

        function loadInitial() {
            axios.get('/api/get-school-classes').then(res => {
                let opts = '<option value="">Select Class</option>';
                res.data.data.forEach(c => opts +=
                    `<option value="${c.class_name}" data-id="${c.id}">${c.class_name}</option>`);
                document.getElementById('class_name').innerHTML = opts;
                document.getElementById('filter_class_name').innerHTML = opts;
            });
            fetchFilteredExams();
        }

        function fetchFilteredExams(isFilter = false) {
            const prefix = isFilter ? 'filter_' : '';
            const params = {
                class_name: document.getElementById(`${prefix}class_name`).value,
                group_name: document.getElementById(`${prefix}group_name`).value,
                section_name: document.getElementById(`${prefix}section_name`).value,
                session_name: document.getElementById(`${prefix}session_name`).value,
            };

            axios.get('/api/get-school-exams', {
                params
            }).then(res => {
                let opts = '<option value="">Select Exam</option>';
                res.data.data.forEach(e => opts +=
                    `<option value="${e.exam_name}" data-id="${e.id}">${e.exam_name}</option>`);
                document.getElementById(`${prefix}exam_name`).innerHTML = opts;
                if (!isFilter) {
                    checkPrerequisiteStatus();
                }
            });
        }

        async function handleCascade(el, next, callback = null) {
            const id = el.options[el.selectedIndex]?.getAttribute('data-id');
            const isFilter = el.id.startsWith('filter_');
            if (!id) return;

            if (next.includes('group')) {
                await fetchFill(`/api/get-school-groups?class_id=${id}`, isFilter ? 'filter_group_name' : 'group_name',
                    'Group', 'group_name');
            } else if (next.includes('section')) {
                await fetchFill(`/api/get-school-sections?group_id=${id}`, isFilter ? 'filter_section_name' :
                    'section_name', 'Section', 'section_name');
            } else if (next.includes('session')) {
                const classId = document.getElementById(isFilter ? 'filter_class_name' : 'class_name').options[document
                    .getElementById(isFilter ? 'filter_class_name' : 'class_name').selectedIndex]?.getAttribute(
                        'data-id');
                await fetchFill(`/api/get-school-sessions?section_id=${id}&class_id=${classId}`, isFilter ?
                    'filter_session_name' : 'session_name', 'Session', 'session_year');
            }

            fetchFilteredExams(isFilter);
            if (!isFilter) fetchStudentCount();
            if (callback) callback();
        }

        function fetchFill(url, tid, lbl, fld) {
            return axios.get(url).then(res => {
                let o = `<option value="">Select ${lbl}</option>`;
                res.data.data.forEach(i => o += `<option value="${i[fld]}" data-id="${i.id}">${i[fld]}</option>`);
                document.getElementById(tid).innerHTML = o;
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
                    setPrerequisiteWarning(false, res.data.message || 'Please create the Exam, Exam Routine, and Exam Fee before generating the Admit Card.');
                }
            }).catch(err => {
                const message = err.response?.data?.message || 'Unable to validate prerequisites at this time.';
                setPrerequisiteWarning(false, message);
            });
        }

        function fetchStudentCount() {
            const getVal = (id) => document.getElementById(id).options[document.getElementById(id).selectedIndex]
                ?.getAttribute('data-id') || '';
            const params = {
                class_id: getVal('class_name'),
                group_id: getVal('group_name'),
                section_id: getVal('section_name'),
                session_id: getVal('session_name')
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
            let opts = '<option value="">Select Student</option>';
            studentsList.forEach(s => {
                opts += `<option value="${s.student_id_number}">${s.student_name} (${s.student_id_number})</option>`;
            });
            document.getElementById('student_id').innerHTML = opts;
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
            document.getElementById('modalTitle').innerText = 'Edit Individual Admit Card';
            document.getElementById('edit_id').value = item.id;
            document.getElementById('submitBtn').innerText = 'Update Admit Card';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('studentStatusBox').classList.add('hidden');

            const classEl = document.getElementById('class_name');
            classEl.value = item.class_name;
            await handleCascade(classEl, 'group');
            const groupEl = document.getElementById('group_name');
            groupEl.value = item.group_name || '';
            await handleCascade(groupEl, 'section');
            const secEl = document.getElementById('section_name');
            secEl.value = item.section_name || '';
            await handleCascade(secEl, 'session');
            document.getElementById('session_name').value = item.session_name;

            fetchFilteredExams(false);
            document.getElementById('exam_name').value = item.exam_name;
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
            const editId = document.getElementById('edit_id').value;
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
            ['filter_class_name', 'filter_group_name', 'filter_section_name', 'filter_session_name', 'filter_exam_name',
                'header_search'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            fetchTable(1);
            toggleFilterModal();
        }

        function openAdmitModal() {
            document.getElementById('admitForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = 'Bulk Admit Card Generator';
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
            document.getElementById('btnText').innerText = 'Generate All Cards';
            document.getElementById('generate_type').value = 'all';
            document.getElementById('single_student_container').classList.add('hidden');
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('admitModal').classList.remove('hidden');
        }

        function closeAdmitModal() {
            document.getElementById('admitModal').classList.add('hidden');
        }

        loadInitial();
        fetchTable();
    </script>
@endsection