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

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openSeatModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Seat Number
                        </button>
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
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="this.classList.add('hidden')">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Search Filters
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1 uppercase font-bold">Class</label>
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
                            <label class="text-[10px] text-gray-500 block mb-1 uppercase font-bold">Group</label>
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
                            <label class="text-[10px] text-gray-500 block mb-1 uppercase font-bold">Section</label>
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
                            <label class="text-[10px] text-gray-500 block mb-1 uppercase font-bold">Session</label>
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
                            <label class="text-[10px] text-gray-500 block mb-1 uppercase font-bold">Exam Name</label>
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
                        <button onclick="applyFilters(); document.getElementById('filterModal').classList.add('hidden');"
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
                        <button onclick="fetchPrintData()"
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
    <div id="seatModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Modal Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
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
                            <select id="class_name" name="class_name" onchange="handleCascade(this, 'group', 'single')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_name" name="group_name" onchange="handleCascade(this, 'section', 'single')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_name" name="section_name" onchange="handleCascade(this, 'session', 'single')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_name" name="session_name" onchange="fetchStudentCount()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
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
                            <select id="exam_name" name="exam_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
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
                    <button type="button" onclick="closeSeatModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" disabled
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap disabled:opacity-50"
                        style="border-radius: 0;">
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let studentsList = [];
        let activeGenerationMode = 'single';

        function loadInitial() {
            axios.get('/api/get-school-classes').then(res => {
                const options = res.data.data || [];
                const classOpts = ['<option value="">Select Class</option>'].concat(options.map(c => `<option value="${c.class_name}" data-id="${c.id}">${c.class_name}</option>`)).join('');
                document.getElementById('class_name').innerHTML = classOpts;
                document.getElementById('filter_class_name').innerHTML = classOpts;
                renderCheckboxGroup('multi_class_options', options, 'class_name', 'class_name', 'id');
            });

            fetchFilteredExams();
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
            return Array.from(select.selectedOptions || []).map(opt => opt.value).filter(Boolean);
        }

        function getSelectedIds(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return [];
            return Array.from(select.selectedOptions || []).map(opt => opt.getAttribute('data-id')).filter(Boolean);
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
                class_names: document.getElementById(`${prefix}class_name`) ? [document.getElementById(`${prefix}class_name`).value].filter(Boolean) : [],
                group_names: document.getElementById(`${prefix}group_name`) ? [document.getElementById(`${prefix}group_name`).value].filter(Boolean) : [],
                section_names: document.getElementById(`${prefix}section_name`) ? [document.getElementById(`${prefix}section_name`).value].filter(Boolean) : [],
                session_names: document.getElementById(`${prefix}session_name`) ? [document.getElementById(`${prefix}session_name`).value].filter(Boolean) : [],
            };

            if (prefix === '') {
                if (activeGenerationMode === 'multi') {
                    params.class_names = getCheckedValues('multi_class_options');
                    params.group_names = getCheckedValues('multi_group_options');
                    params.section_names = getCheckedValues('multi_section_options');
                    params.session_names = getCheckedValues('multi_session_options');
                } else {
                    const selection = getCurrentSelection('single');
                    params.class_names = selection.class_name ? [selection.class_name] : [];
                    params.group_names = selection.group_name ? [selection.group_name] : [];
                    params.section_names = selection.section_name ? [selection.section_name] : [];
                    params.session_names = selection.session_name ? [selection.session_name] : [];
                }
            }

            axios.get('/api/get-school-exams', { params }).then(res => {
                let opts = '<option value="">Select Exam</option>';
                res.data.data.forEach(e => opts += `<option value="${e.exam_name}" data-id="${e.id}">${e.exam_name}</option>`);
                document.getElementById(`${prefix}exam_name`).innerHTML = opts;
            });
        }

        async function handleCascade(el, next, mode = activeGenerationMode, callback = null) {
            const isFilter = el.id.startsWith('filter_');

            if (isFilter) {
                const id = el.options[el.selectedIndex]?.getAttribute('data-id');
                if (!id) return;
                if (next === 'group') {
                    await fetchFill(`/api/get-school-groups?class_id=${id}`, 'filter_group_name', 'Group', 'group_name');
                } else if (next === 'section') {
                    await fetchFill(`/api/get-school-sections?group_id=${id}`, 'filter_section_name', 'Section', 'section_name');
                } else if (next === 'session') {
                    const classId = document.getElementById('filter_class_name').options[document.getElementById('filter_class_name').selectedIndex]?.getAttribute('data-id');
                    await fetchFill(`/api/get-school-sessions?section_id=${id}&class_id=${classId}`, 'filter_session_name', 'Session', 'session_year');
                }
                fetchFilteredExams(true);
                return;
            }

            if (mode === 'single') {
                const selectedClassId = document.getElementById('class_name').options[document.getElementById('class_name').selectedIndex]?.getAttribute('data-id');
                if (next === 'group' && selectedClassId) {
                    await fetchFill(`/api/get-school-groups?class_id=${selectedClassId}`, 'group_name', 'Group', 'group_name');
                } else if (next === 'section') {
                    const selectedGroupId = document.getElementById('group_name').options[document.getElementById('group_name').selectedIndex]?.getAttribute('data-id');
                    if (selectedGroupId) {
                        await fetchFill(`/api/get-school-sections?group_id=${selectedGroupId}`, 'section_name', 'Section', 'section_name');
                    }
                } else if (next === 'session') {
                    const selectedClassIdForSession = document.getElementById('class_name').options[document.getElementById('class_name').selectedIndex]?.getAttribute('data-id');
                    const selectedSectionId = document.getElementById('section_name').options[document.getElementById('section_name').selectedIndex]?.getAttribute('data-id');
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

            fetchFilteredExams();
            fetchStudentCount();
            if (callback) callback();
        }

        function fetchFill(url, tid, lbl, fld) {
            return axios.get(url).then(res => {
                const data = res.data.data || [];
                let o = `<option value="">Select ${lbl}</option>`;
                data.forEach(i => o += `<option value="${i[fld]}" data-id="${i.id}">${i[fld]}</option>`);
                document.getElementById(tid).innerHTML = o;
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
            ['filter_class_name', 'filter_group_name', 'filter_section_name', 'filter_session_name', 'filter_exam_name',
                'header_search'
            ].forEach(id => {
                if (document.getElementById(id)) document.getElementById(id).value = '';
            });
            fetchTable(1);
            toggleFilterModal();
        }

        function openSeatModal() {
            document.getElementById('seatForm').reset();
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

        function fetchPrintData() {
            const params = {
                all: 'true',
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
                const items = res.data.data;
                const school = res.data.school;
                if (items && items.length > 0) {
                    generateSeatPrintLayout(items, school);
                } else {
                    Swal.fire('No Data', 'No records found to print', 'info');
                }
            }).catch(err => {
                console.error("Print fetch error", err);
                Swal.fire('Error', 'Could not fetch print data', 'error');
            });
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

        loadInitial();
        fetchTable();
    </script>
@endsection