@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

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
            outline: none;
            background: #fff;
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
            transition: all .2s ease;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
        }

        .count-box {
            background: #f1f5f9;
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .action-icon-btn {
            font-size: 1.25rem;
        }

        /* Search Box Specific Style */
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-wrapper i {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            font-size: 1rem;
        }

        .search-input {
            padding-left: 32px !important;
            width: 220px;
            height: 38px;
            border: 1.5px solid #e2e8f0 !important;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #2563eb !important;
            width: 280px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
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
                            <input type="text" id="tableSearch" onkeyup="searchTable()" placeholder="Search schedule..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500 "
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="toggleFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button id="btnExport" onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Create Schedule
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="tableSearchMobile"
                        onkeyup="document.getElementById('tableSearch').value = this.value; searchTable()"
                        placeholder="SEARCH SCHEDULE..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500 uppercase"
                        style="border-radius: 0;" />
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
                            Schedule filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="f_class" onchange="handleCascade(this, 'f_group')"
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
                                <select id="f_group" onchange="handleCascade(this, 'f_section')"
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
                                <select id="f_section" onchange="handleCascade(this, 'f_session')"
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
                                <select id="f_session"
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
                                <select id="f_exam"
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
                        <button onclick="applyFilters(); toggleFilterModal();"
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
                        <button onclick="exportData('pdf')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button onclick="exportData('excel')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button onclick="window.print()"
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
                    <table class="w-full text-left">
                        <thead>
                            <tr>
                                <th width="60">SL</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Exam</th>
                                <th>Total Sub</th>
                                <th>Submitted</th>
                                <th>Remaining</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th width="120" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule Main Modal --}}
    <div id="mainModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Header: Centered & Sticky --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    New Exam Schedule
                </h3>
            </div>

            {{-- Content Area --}}
            <div class="flex flex-col overflow-hidden m-0">
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <input type="hidden" id="edit_id">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        {{-- Selectors --}}
                        <div>
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="m_class" onchange="handleCascade(this, 'm_group')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="m_group" onchange="handleCascade(this, 'm_section')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="m_section" onchange="handleCascade(this, 'm_session')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="m_session" onchange="fetchSubjectCounts()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5 font-medium">Exam
                                Name</label>
                            <select id="m_exam" onchange="fetchSubjectCounts()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        {{-- Status Count Boxes: Desktop & Mobile side-by-side --}}
                        <div class="col-span-1 sm:col-span-2 grid grid-cols-3 gap-2 py-4 border-y border-gray-200/60 my-2">
                            <div class="text-center p-2 bg-white border border-gray-100 shadow-sm">
                                <span class="block text-[9px] text-gray-400 font-bold tracking-wider">Total</span>
                                <span id="c_total" class="text-sm font-black text-blue-600">0</span>
                            </div>
                            <div class="text-center p-2 bg-white border border-gray-100 shadow-sm">
                                <span class="block text-[9px] text-gray-400 font-bold  tracking-wider">Submitted</span>
                                <span id="c_submitted" class="text-sm font-black text-green-600">0</span>
                            </div>
                            <div class="text-center p-2 bg-white border border-gray-100 shadow-sm">
                                <span class="block text-[9px] text-gray-400 font-bold tracking-wider">Remaining</span>
                                <span id="c_remaining" class="text-sm font-black text-red-600">0</span>
                            </div>
                        </div>

                        {{-- DateTime --}}
                        <div>
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Publish
                                Date</label>
                            <input type="date" id="m_date"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Publish
                                Time</label>
                            <input type="time" id="m_time"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0 z-10">
                    <button type="button" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="button" onclick="saveSchedule()"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>



   <script>
    let isEdit = false;
    let currentPage = 1;

    window.onload = function() {
        loadInitialData();
        fetchTable();
    };

    function searchTable() {
        let input = document.getElementById("tableSearch");
        let filter = input.value.toUpperCase();
        let table = document.querySelector("table");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let textContent = tr[i].textContent || tr[i].innerText;
            if (textContent.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
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

        axios.get('/api/get-school-exams', { params }).then(res => {
            fillOptions(`${prefix}exam`, res.data.data, 'exam_name');
        });
    }

    function fillOptions(id, data, field) {
        let h = `<option value="">Select ${field.replace('_', ' ')}</option>`;
        if (data) {
            data.forEach(i => h += `<option value="${i[field]}" data-id="${i.id}">${i[field]}</option>`);
        }
        document.getElementById(id).innerHTML = h;
    }

    async function handleCascade(el, nextId) {
        const id = el.options[el.selectedIndex]?.getAttribute('data-id');
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
            const classId = document.getElementById(classSelect).options[document.getElementById(classSelect)
                .selectedIndex]?.getAttribute('data-id');
            url = `/api/get-school-sessions?section_id=${id}&class_id=${classId}`;
            fld = 'session_year';
        }

        const res = await axios.get(url);
        fillOptions(nextId, res.data.data, fld);

        // Sync exams every time the hierarchy changes
        fetchFilteredExams(isFilter);

        // Trigger subject count if we are in the main modal
        if (!isFilter) fetchSubjectCounts();
    }

    function fetchSubjectCounts() {
        const clsEl = document.getElementById('m_class');
        const cls = clsEl.value;
        const clsId = clsEl.options[clsEl.selectedIndex]?.getAttribute('data-id');
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
                body.innerHTML = '<tr><td colspan="12" class="text-center py-8">No records found</td></tr>';
                renderPagination(res.data);
                return;
            }

            res.data.data.forEach((item, i) => {
                body.innerHTML += `
            <tr class="hover:bg-gray-50 transition">
                <td>${res.data.from + i}</td>
                <td>${item.class_name}</td>
                <td>${item.group_name || 'N/A'}</td>
                <td>${item.section_name || 'N/A'}</td>
                <td>${item.session_name || 'N/A'}</td>
                <td>${item.exam_name}</td>
                <td>${item.total_subject}</td>
                <td>${item.submitted_subject}</td>
                <td>${item.remaining_subject}</td>
                <td>${formatDate(item.publish_date)}</td>
                <td>${formatTime(item.publish_time)}</td>
                <td class="text-center">
                    <div class="flex justify-center gap-3">
                        <button onclick="editItem(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                        <button onclick="deleteItem(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
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
        document.getElementById('modalTitle').innerText = "Edit Exam Schedule";
        const res = await axios.get(`/api/school-exam-schedules/${id}`);
        const d = res.data;

        document.getElementById('edit_id').value = id;

        const classEl = document.getElementById('m_class');
        classEl.value = d.class_name;
        await handleCascade(classEl, 'm_group');

        const groupEl = document.getElementById('m_group');
        groupEl.value = d.group_name;
        await handleCascade(groupEl, 'm_section');

        const sectionEl = document.getElementById('m_section');
        sectionEl.value = d.section_name;
        await handleCascade(sectionEl, 'm_session');

        document.getElementById('m_session').value = d.session_name;
        
        // Ensure exams are fetched before setting value
        fetchFilteredExams(false);
        document.getElementById('m_exam').value = d.exam_name;
        
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
        document.getElementById('modalTitle').innerText = "New Exam Schedule";
        document.getElementById('edit_id').value = '';
        document.getElementById('m_class').value = '';
        document.getElementById('m_group').innerHTML = '<option value="">Select Group</option>';
        document.getElementById('m_section').innerHTML = '<option value="">Select Section</option>';
        document.getElementById('m_session').innerHTML = '<option value="">Select Session</option>';
        document.getElementById('m_exam').innerHTML = '<option value="">Select Exam Name</option>';
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
        document.getElementById('f_class').value = '';
        document.getElementById('f_group').innerHTML = '<option value="">Select Group</option>';
        document.getElementById('f_section').innerHTML = '<option value="">Select Section</option>';
        document.getElementById('f_session').innerHTML = '<option value="">Select Session</option>';
        document.getElementById('f_exam').innerHTML = '<option value="">Select Exam Name</option>';
        fetchTable(1);
        toggleFilterModal();
    }

    function applyFilters() {
        fetchTable(1);
        toggleFilterModal();
    }
</script>
@endsection
