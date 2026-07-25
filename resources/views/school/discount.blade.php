@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Essential Reset for Mobile Full-Width */
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
            min-height: 48px !important;
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
                min-height: 35px !important;
                padding: 0.4rem 0.75rem !important;
            }

            .pagination-btn {
                height: 24px !important;
                min-width: 24px !important;
            }
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

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 4px;
            background: none;
            border: none;
            cursor: pointer;
        }
    </style>

    <div class="main-view-container">
        <div class="w-full">
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
                            <input type="text" id="discountSearch" oninput="fetchDiscounts(1)"
                                placeholder="Search student name..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openDiscountModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Discount
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="discountSearchMobile" oninput="fetchDiscounts(1)"
                        placeholder="Search student name..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    {{-- Modal Title Section --}}
                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Discount filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    {{-- Form Field Section --}}
                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Classes</option>
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
                                <select id="groupFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Groups</option>
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
                                <select id="sectionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sections</option>
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
                                <select id="sessionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sessions</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Button Section --}}
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

            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl">
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
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table id="discountTable">
                        <thead>
                            <tr>
                                <th width="60">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Student Id</th>
                                <th>Student name</th>
                                <th>Fees type</th>
                                <th>Fees name</th>
                                <th>Before discount</th>
                                <th>Discount type</th>
                                <th>Discount amount</th>
                                <th>After discount</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th width="120" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="discountTableBody">
                            <tr>
                                <td colspan="16"
                                    class="text-center py-10 text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                                    Initializing Data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="paginationControls" class="pagination-bar">
                    <div id="paginationInfo" class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">
                        0 of 0
                    </div>
                    <div id="paginationButtons" class="flex gap-1"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assign Discount Modal --}}
    <div id="discountModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Add Discount
                </h3>
            </div>

            <form id="discountForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="edit_id">
                <input type="hidden" id="discount_amount">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_id" onchange="handleClassChange()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" required></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_id" onchange="handleGroupChange()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">Select Group</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_id" onchange="loadStudentsAndFees()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_id" onchange="loadStudentsAndFees()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" required></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2 mt-1 pt-2 border-t border-gray-200/60">
                            <label class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5">Select
                                Student</label>
                            <select id="student_id"
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                style="border-radius: 0;" required>
                                <option value="">Select Student</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Fees
                                Type</label>
                            <select id="fee_type_id" onchange="updateBaseFee()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" required></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Fees
                                Name</label>
                            <input type="text" id="fee_name"
                                class="form-input-fixed w-full border border-gray-200 bg-gray-100/50 py-1.5 px-3 text-xs cursor-not-allowed h-[32px]"
                                style="border-radius: 0;" readonly>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Before
                                Discount</label>
                            <input type="number" id="before_discount"
                                class="form-input-fixed w-full border border-gray-200 bg-gray-100 py-1.5 px-3 text-xs text-gray-700 cursor-not-allowed h-[32px]"
                                style="border-radius: 0;" readonly>
                        </div>

                        <div class="col-span-1 sm:col-span-2 mt-1">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white p-3 border border-gray-200">
                                <div>
                                    <label class="block text-[9px] capitalize tracking-wider text-gray-400 mb-1">Discount
                                        Type</label>
                                    <select id="discount_type" onchange="handleTypeChange()"
                                        class="form-input-fixed w-full border border-gray-200 py-1 px-2 text-[11px] h-[32px]"
                                        style="border-radius: 0;">
                                        <option value="Fixed">Fixed Amount</option>
                                        <option value="Percentage">Percentage %</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] capitalize tracking-wider text-gray-400 mb-1">Discount
                                        Value</label>
                                    <input type="number" id="discount_value" oninput="runCalculation()"
                                        class="form-input-fixed w-full border border-blue-200 py-1 px-2 text-[11px] h-[32px]"
                                        style="border-radius: 0;" step="any" required placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-[9px] capitalize tracking-wider text-green-600 mb-1">After
                                        Discount</label>
                                    <input type="number" id="after_discount"
                                        class="form-input-fixed w-full border border-gray-100 bg-green-50 py-1 px-2 text-green-700 cursor-not-allowed text-[11px] h-[32px]"
                                        style="border-radius: 0;" readonly placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Start
                                Date</label>
                            <input type="date" id="start_date"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">End
                                Date</label>
                            <input type="date" id="end_date"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeDiscountModal()"
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
        const api = axios.create({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let currentPage = 1;
        let discountData = []; // Store full data for client-side filtering
        let cachedData = {
            classes: null
        };

        $(document).ready(async () => {
            await initFilterDefaults();
            fetchDiscounts();
        });

        // --- Helper Functions ---
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // --- Filter Initialization Logic ---
        async function initFilterDefaults() {
            if (!cachedData.classes) {
                const res = await api.get('/api/get-school-classes');
                cachedData.classes = res.data.data;
            }

            // Fill Filter Class Dropdown
            let cH = '<option value="">All Classes</option>';
            cachedData.classes.forEach(i => {
                cH += `<option value="${i.id}">${i.class_name}</option>`;
            });
            $('#classFilter').html(cH);

            // Auto-select first class if exists
            if (cachedData.classes.length > 0) {
                const firstClassId = cachedData.classes[0].id;
                $('#classFilter').val(firstClassId);
                await handleFilterCascade('class');
            }
        }

        async function handleFilterCascade(type) {
            const classId = $('#classFilter').val();
            const groupId = $('#groupFilter').val();
            const sectionId = $('#sectionFilter').val();

            if (type === 'class') {
                const resG = await api.get('/api/get-school-groups', {
                    params: {
                        class_id: classId
                    }
                });
                let hG = '<option value="">All Groups</option>';
                resG.data.data.forEach(g => hG += `<option value="${g.id}">${g.group_name}</option>`);
                $('#groupFilter').html(hG);
                if (resG.data.data.length > 0) $('#groupFilter').val(resG.data.data[0].id);
                await handleFilterCascade('group');
            } else if (type === 'group') {
                const resS = await api.get('/api/get-school-sections', {
                    params: {
                        group_id: groupId
                    }
                });
                let hS = '<option value="">All Sections</option>';
                resS.data.data.forEach(s => hS += `<option value="${s.id}">${s.section_name}</option>`);
                $('#sectionFilter').html(hS);
                if (resS.data.data.length > 0) $('#sectionFilter').val(resS.data.data[0].id);
                await handleFilterCascade('section');
            } else if (type === 'section') {
                const resSes = await api.get('/api/get-school-sessions', {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });
                let hSes = '<option value="">All Sessions</option>';
                resSes.data.data.forEach(i => hSes += `<option value="${i.id}">${i.session_year}</option>`);
                $('#sessionFilter').html(hSes);
                if (resSes.data.data.length > 0) $('#sessionFilter').val(resSes.data.data[0].id);
            }
        }

        // Attach listeners to filter dropdowns
        $('#classFilter').on('change', () => handleFilterCascade('class'));
        $('#groupFilter').on('change', () => handleFilterCascade('group'));
        $('#sectionFilter').on('change', () => handleFilterCascade('section'));

        // --- Main Fetch & Client Side Filter ---
        function fetchDiscounts(page = 1) {
            currentPage = page;
            const search = $('#discountSearch').val()?.toLowerCase();

            // Filter Values
            const fClass = $('#classFilter').val();
            const fGroup = $('#groupFilter').val();
            const fSection = $('#sectionFilter').val();
            const fSession = $('#sessionFilter').val();

            api.get('/api/fee-discounts', {
                params: {
                    page,
                    search
                }
            }).then(res => {
                const response = res.data;
                let data = response.data || [];

                // Apply Client-Side Filtering
                const filteredData = data.filter(item => {
                    const matchClass = !fClass || item.class_id == fClass;
                    const matchGroup = !fGroup || item.group_id == fGroup;
                    const matchSection = !fSection || item.section_id == fSection;
                    const matchSession = !fSession || item.session_id == fSession;
                    return matchClass && matchGroup && matchSection && matchSession;
                });

                let h = '';
                if (filteredData.length === 0) {
                    h =
                        '<tr><td colspan="16" class="text-center py-10">No records matching filters found.</td></tr>';
                } else {
                    filteredData.forEach((i, idx) => {
                        const sl = (response.from || 1) + idx;
                        const discDisplay = i.discount_type === 'Percentage' ? `${i.discount_value}%` :
                            `${i.discount_amount}`;
                        h += `<tr>
                        <td>${sl}</td>
                        <td>${i.school_class?.class_name || 'N/A'}</td>
                        <td>${i.school_group?.group_name || '-'}</td>
                        <td>${i.school_section?.section_name || '-'}</td>
                        <td>${i.school_session?.session_year || 'N/A'}</td>
                        <td class="text-gray-700">${i.student?.student_id_number || 'N/A'}</td>
                        <td class="text-gray-700">${i.student?.student_name || 'N/A'}</td>
                        <td class="text-gray-600">${i.fee_type?.fee_type_name || 'N/A'}</td>
                        <td class="text-gray-700">${i.fee_name || i.fee_type?.fee_name || 'N/A'}</td>
                        <td class="text-gray-700">${i.before_discount}</td>
                        <td class="text-gray-700">${i.discount_type}</td>
                        <td class="text-gray-700">${discDisplay}</td>
                        <td class="text-gray-700">${i.after_discount}</td>
                        <td class="text-gray-500">${formatDate(i.start_date)}</td>
                        <td class="text-gray-500">${formatDate(i.end_date)}</td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editDisc(${i.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                <button onclick="deleteDisc(${i.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                            </div>
                        </td>
                    </tr>`;
                    });
                }
                $('#discountTableBody').html(h);
                renderPagination(response);
            });
        }

        // --- Assign Discount Modal Logic ---
        async function openDiscountModal() {
            $('#discountForm')[0].reset();
            $('#edit_id').val('');
            $('#discount_value').val(0);
            $('#modalTitle').text('Assign Discount');
            $('#discountModal').removeClass('hidden');

            $('#group_id').html('<option value="">No Group</option>');
            $('#section_id').html('<option value="">No Section</option>');
            $('#session_id').html('<option value="">Select Session</option>');
            $('#student_id').html('<option value="">Select Student</option>');
            $('#fee_type_id').html('<option value="">Select Fee</option>');

            await loadInitialData();
            runCalculation();
        }

        function closeDiscountModal() {
            $('#discountModal').addClass('hidden');
        }

        async function loadInitialData() {
            if (!cachedData.classes) {
                const res = await api.get('/api/get-school-classes');
                cachedData.classes = res.data.data;
            }
            let cH = '<option value="">Select Class</option>';
            cachedData.classes.forEach(i => cH += `<option value="${i.id}">${i.class_name}</option>`);
            $('#class_id').html(cH);
        }

        async function handleClassChange() {
            $('#group_id').html('<option value="">No Group</option>');
            $('#section_id').html('<option value="">No Section</option>');
            $('#session_id').html('<option value="">Select Session</option>');
            $('#student_id').html('<option value="">Select Student</option>');
            $('#fee_type_id').html('<option value="">Select Fee</option>');
            await loadGroups();
            await loadSessions();
            await loadStudentsAndFees();
        }

        async function handleGroupChange() {
            $('#section_id').html('<option value="">No Section</option>');
            $('#session_id').html('<option value="">Select Session</option>');
            await loadSections();
            await loadSessions();
            await loadStudentsAndFees();
        }

        async function handleSectionChange() {
            await loadSessions();
            await loadStudentsAndFees();
        }

        async function loadGroups(selectedId = null) {
            const classId = $('#class_id').val();
            if (!classId) return;
            const res = await api.get('/api/get-school-groups', {
                params: {
                    class_id: classId
                }
            });
            let h = '<option value="">No Group</option>';
            res.data.data.forEach(g => h +=
                `<option value="${g.id}" ${selectedId == g.id ? 'selected' : ''}>${g.group_name}</option>`);
            $('#group_id').html(h);
        }

        async function loadSections(selectedId = null) {
            const groupId = $('#group_id').val();
            if (!groupId) return;
            const res = await api.get('/api/get-school-sections', {
                params: {
                    group_id: groupId
                }
            });
            let h = '<option value="">No Section</option>';
            res.data.data.forEach(s => h +=
                `<option value="${s.id}" ${selectedId == s.id ? 'selected' : ''}>${s.section_name}</option>`);
            $('#section_id').html(h);
        }

        async function loadSessions(selectedId = null) {
            const classId = $('#class_id').val();
            const groupId = $('#group_id').val();
            const sectionId = $('#section_id').val();
            if (!classId) {
                $('#session_id').html('<option value="">Select Session</option>');
                return;
            }
            const res = await api.get('/api/get-school-sessions', {
                params: {
                    class_id: classId,
                    group_id: groupId,
                    section_id: sectionId
                }
            });
            let h = '<option value="">Select Session</option>';
            res.data.data.forEach(i => h +=
                `<option value="${i.id}" ${selectedId == i.id ? 'selected' : ''}>${i.session_year}</option>`);
            $('#session_id').html(h);
        }

        async function loadStudentsAndFees() {
            const classId = $('#class_id').val();
            const sessionId = $('#session_id').val();
            const sectionId = $('#section_id').val();
            const groupId = $('#group_id').val();

            if (!classId || !sessionId) {
                $('#student_id').html('<option value="">Select Student</option>');
                $('#fee_type_id').html('<option value="">Select Fee</option>');
                return;
            }

            try {
                const sRes = await api.get('/api/get-school-students', {
                    params: {
                        class_id: classId,
                        session_id: sessionId,
                        section_id: sectionId,
                        group_id: groupId
                    }
                });

                let sH = '<option value="">Select Student</option>';
                const students = sRes.data.data || sRes.data;
                students.forEach(s => sH +=
                    `<option value="${s.id}">${s.student_name}</option>`);
                $('#student_id').html(sH);

                // Reset fees until a student is selected
                $('#fee_type_id').html('<option value="">Select Student First</option>');
            } catch (e) {
                console.error("Error loading students:", e);
            }
        }

        // Load fees when a student is selected
        $('#student_id').on('change', async function () {
            const studentId = $(this).val();
            const classId = $('#class_id').val();
            const sessionId = $('#session_id').val();

            $('#fee_type_id').html('<option value="">Select Fee</option>');
            $('#fee_name').val('');
            $('#before_discount').val('');
            runCalculation();

            if (!studentId || !classId || !sessionId) return;

            try {
                const fRes = await api.get('/api/student-fees/by-student', {
                    params: {
                        student_id: studentId,
                        all: true
                    }
                });

                const allFees = fRes.data.data || fRes.data;

                // Only show fees that are pending/partial/overdue (skip already paid)
                const relevantFees = allFees.filter(f =>
                    f.status !== 'paid'
                );

                let fH = '<option value="">Select Fee</option>';
                relevantFees.forEach(f => {
                    fH += `<option value="${f.fee_template_id}" data-amt="${f.amount}" data-feename="${f.fee_name || 'N/A'}">${f.fee_type_name} — ${f.fee_name || ''} (${f.amount})</option>`;
                });
                $('#fee_type_id').html(fH);
            } catch (e) {
                console.error("Error loading fees:", e);
            }
        });

        function updateBaseFee() {
            const selected = $('#fee_type_id option:selected');
            const amt = selected.data('amt') || 0;
            const feeName = selected.data('feename') || 'N/A';
            $('#before_discount').val(amt);
            $('#fee_name').val(feeName);
            runCalculation();
        }

        function runCalculation() {
            const base = parseFloat($('#before_discount').val()) || 0;
            const val = parseFloat($('#discount_value').val()) || 0;
            const discAmt = ($('#discount_type').val() === 'Percentage') ? (base * val / 100) : val;
            $('#discount_amount').val(discAmt.toFixed(2));
            $('#after_discount').val((base - discAmt).toFixed(2));
        }

        $('#discountForm').on('submit', function(e) {
            e.preventDefault();
            const data = {
                student_id: $('#student_id').val(),
                class_id: $('#class_id').val(),
                session_id: $('#session_id').val(),
                fee_type_id: $('#fee_type_id').val(),
                fee_name: $('#fee_name').val() || 'N/A',
                discount_type: $('#discount_type').val(),
                discount_value: $('#discount_value').val(),
                before_discount: $('#before_discount').val(),
                discount_amount: $('#discount_amount').val(),
                after_discount: $('#after_discount').val(),
                group_id: $('#group_id').val() || null,
                section_id: $('#section_id').val() || null,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            };

            const id = $('#edit_id').val();
            const promise = id ? api.put(`/api/fee-discounts/${id}`, data) : api.post('/api/fee-discounts', data);

            promise.then(async () => {
                // Simultaneous toast and modal closure
                Toastify({
                    text: "Success!",
                    style: {
                        background: "green"
                    }
                }).showToast();
                closeDiscountModal();

                // Background tasks: Reset filter to the new data's category and refresh table
                $('#classFilter').val(data.class_id);
                await handleFilterCascade('class');
                $('#groupFilter').val(data.group_id || "");
                await handleFilterCascade('group');
                $('#sectionFilter').val(data.section_id || "");
                await handleFilterCascade('section');
                $('#sessionFilter').val(data.session_id);

                fetchDiscounts(1);
            }).catch(err => Swal.fire('Error', err.response?.data?.message || "Failed", 'error'));
        });

        // --- Pagination and Modal Toggles ---
        function renderPagination(data) {
            $('#paginationInfo').text(`${data.to || 0} of ${data.total || 0}`);
            let btns =
                `<button onclick="fetchDiscounts(${data.current_page - 1})" ${data.current_page === 1 ? 'disabled' : ''} class="pagination-btn"><i class="mdi mdi-chevron-left"></i></button>`;
            if (data.links) {
                data.links.forEach(link => {
                    if (!isNaN(link.label)) {
                        btns +=
                            `<button onclick="fetchDiscounts(${link.label})" class="pagination-btn ${link.active ? 'active' : ''}">${link.label}</button>`;
                    }
                });
            }
            btns +=
                `<button onclick="fetchDiscounts(${data.current_page + 1})" ${data.current_page === data.last_page ? 'disabled' : ''} class="pagination-btn"><i class="mdi mdi-chevron-right"></i></button>`;
            $('#paginationButtons').html(btns);
        }

        async function editDisc(id) {
            try {
                const res = await api.get(`/api/fee-discounts/${id}`);
                const d = res.data;
                $('#discountForm')[0].reset();
                $('#discountModal').removeClass('hidden');
                $('#modalTitle').text('Edit Discount');
                $('#edit_id').val(d.id);
                await loadInitialData();
                $('#class_id').val(d.class_id);
                await loadGroups(d.group_id);
                await loadSections(d.section_id);
                await loadSessions(d.session_id);
                await loadStudentsAndFees();
                $('#student_id').val(d.student_id);
                // Trigger student change to load fees for this student
                $('#student_id').trigger('change');
                // Wait for fees to load then set the selected fee
                await new Promise(resolve => setTimeout(resolve, 300));
                $('#fee_type_id').val(d.fee_type_id);
                $('#fee_name').val(d.fee_name || 'N/A');
                $('#before_discount').val(d.before_discount);
                $('#discount_type').val(d.discount_type);
                $('#discount_value').val(d.discount_value);
                $('#start_date').val(d.start_date);
                $('#end_date').val(d.end_date);
                runCalculation();
            } catch (e) {
                console.error("Edit error:", e);
            }
        }

        function deleteDisc(id) {
            Swal.fire({
                title: 'Delete adjustment?',
                text: "This will revert the student's fee to the original amount.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/api/fee-discounts/${id}`).then(() => {
                        fetchDiscounts(currentPage);
                        Toastify({
                            text: "Discount Removed",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => document.getElementById(id).classList.toggle('hidden', !show);

            // Filter Buttons
            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('applyFilter').addEventListener('click', () => {
                fetchDiscounts(1);
                toggleModal('filterModal', false);
            });
            document.getElementById('resetFilter').addEventListener('click', async () => {
                await initFilterDefaults();
                fetchDiscounts(1);
                toggleModal('filterModal', false);
            });

            // Export Modal
            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            window.onclick = (event) => {
                if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
            };
        });
    </script>
@endsection