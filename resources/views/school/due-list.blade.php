@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        .spinner {
            border: 2px solid #e5e7eb;
            border-top: 2px solid #2563eb;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .status-chip.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: .75rem;
            box-sizing: border-box;
            position: relative;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        /* Table Card & Layout */
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

        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 15px !important;
        }

        /* Scrollbar Styling */
        .table-responsive::-webkit-scrollbar {
            height: 5px !important;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 0px !important;
        }

        /* Core Table Styling */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            border: 1px solid #d1d5db !important;
            font-size: 11px !important;
        }

        th {
            padding: 0 12px !important;
            height: 32px !important;
            line-height: 32px !important;
            white-space: nowrap !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            color: #374151 !important;
            font-weight: 800 !important;
            vertical-align: middle !important;
            text-align: left !important;
            /* Updated to handle Title Case */
            text-transform: capitalize !important;
            letter-spacing: 0.01em !important;
        }

        th:last-child {
            border-right: none !important;
        }

        tr {
            height: 30px !important;
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

        /* Pagination Styles */
        .pagination-container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0.6rem 1rem !important;
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-top: none !important;
            min-height: 48px !important;
        }

        /* Softened Pagination Info Text */
        #paginationInfo {
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }

        .pagination-btn,
        .page-link-btn {
            height: 26px !important;
            min-width: 26px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 8px !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            border: 1px solid #e2e8f0 !important;
            background: white !important;
            color: #64748b !important;
            cursor: pointer !important;
            border-radius: 0 !important;
            text-transform: uppercase !important;
            transition: all 0.1s ease !important;
        }

        .pagination-btn.active,
        .page-link-btn.active {
            background: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }

        .pagination-btn:hover:not(:disabled):not(.active),
        .page-link-btn:hover:not(:disabled):not(.active) {
            border-color: #2563eb !important;
            color: #2563eb !important;
            background: #f8fafc !important;
        }

        .pagination-btn:disabled,
        .page-link-btn:disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            background: #f1f5f9 !important;
        }

        /* ================= Pagination Mobile Adjustments ================= */
        @media (max-width: 768px) {
            .pagination-container {
                padding: 0.3rem 0.5rem;
                /* Tighter padding */
                min-height: 35px;
                /* Slimmer bar */
            }

            .pagination-btn {
                min-width: 22px;
                /* Narrower buttons */
                height: 20px;
                /* Shorter buttons */
                padding: 0 4px;
                /* Minimal side padding */
                font-size: 9px;
                /* Smaller text */
                gap: 2px;
            }

            #paginationInfo {
                font-size: 8px !important;
                /* Tiny info text */
            }

            #paginationControls {
                gap: 2px;
                /* Pull buttons closer together */
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

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 4px;
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.1s;
        }

        .action-icon-btn:hover {
            transform: scale(1.1);
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

        .modal-content-sharp {
            border-radius: 0 !important;
            /* border-top: 5px solid #2563eb; */
        }

        /* Badges & Pills */
        .badge-due {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 2px 6px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 9px;
        }

        .student-id-pill {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: 600;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">

            {{-- Header Section --}}
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        {{-- Dynamic Page Title --}}
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>

                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>

                        {{-- Desktop Search --}}
                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="masterSearch" placeholder="Search Name, ID, Class..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                            style="border-radius: 0;">
                            Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                            style="border-radius: 0;">
                            Export
                        </button>

                        <button id="btnStatusFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                            style="border-radius: 0;">
                            Status
                        </button>

                    </div>
                </div>

                {{-- Mobile Search --}}
                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="masterSearchMobile" placeholder="Search Name, ID, Class..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    {{-- Modal Title Section --}}
                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Due list filter
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

                        {{-- Student Filter (Optional) --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Student</label>
                            <div class="relative">
                                <select id="studentFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Students</option>
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

            {{-- Status Filter Modal --}}
            <div id="statusFilterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Filter by status
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-4 mb-4 flex flex-col gap-2">
                        <button data-status="due" onclick="toggleStatusChip(this)"
                            class="status-chip w-full border border-gray-200 py-2 px-3 text-[11px] tracking-wider flex items-center justify-center transition-all"
                            style="border-radius: 0;">
                            Due
                        </button>
                        <button data-status="due_partial" onclick="toggleStatusChip(this)"
                            class="status-chip w-full border border-gray-200 py-2 px-3 text-[11px] tracking-wider flex items-center justify-center transition-all"
                            style="border-radius: 0;">
                            Due Partial
                        </button>
                        <button data-status="over_due" onclick="toggleStatusChip(this)"
                            class="status-chip w-full border border-gray-200 py-2 px-3 text-[11px] tracking-wider flex items-center justify-center transition-all"
                            style="border-radius: 0;">
                            Over Due
                        </button>
                        <button data-status="over_due_partial" onclick="toggleStatusChip(this)"
                            class="status-chip w-full border border-gray-200 py-2 px-3 text-[11px] tracking-wider flex items-center justify-center transition-all"
                            style="border-radius: 0;">
                            Over Due Partial
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <button id="resetStatusFilter"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button id="applyStatusFilter"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl" style="border-radius: 0;">
                    <div class="flex flex-col gap-1.5">
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap"
                            style="border-radius: 0;">
                            PDF
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap"
                            style="border-radius: 0;">
                            EXCEL
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap"
                            style="border-radius: 0;">
                            PRINT
                        </button>
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all"
                            style="border-radius: 0;">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="table-card" id="printArea">
                <div class="table-responsive">
                    <table class="min-w-[1500px]">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th width="100">Status</th>
                                <th width="120">Due Date</th>
                                <th width="100">Class</th>
                                <th width="100">Group</th>
                                <th width="100">Section</th>
                                <th width="100">Session</th>
                                <th width="120">Student Id</th>
                                <th>Student Name</th>
                                <th width="150">Fee Type</th>
                                <th width="150">Fee Name</th>
                                <th width="120">Total Fee</th>
                                <th width="120">Paid Fee</th>
                                <th width="120">Due Fee</th>
                                <th width="120">Overdue Fee</th>
                                <th width="120">Last Date</th>
                                <th class="text-center no-print" width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody id="masterLedgerBody" class="bg-white divide-y divide-gray-100">
                            <tr>
                                <td colspan="17" class="text-center py-10">
                                    <div class="flex items-center justify-center gap-2 text-gray-400">
                                        <div class="spinner"></div>
                                        <span class="text-sm">Checking for dues & over dues...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="pagination-container no-print">
                    <div id="paginationInfo" class="text-[10px] uppercase tracking-widest">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Due Payment Modal --}}
    <div id="paymentModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">
        <div
            class="bg-white w-full max-w-lg modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Centered Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Due Payment
                </h3>
            </div>

            <form id="paymentForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="modalPaymentId">
                <input type="hidden" id="rawDueVal">
                <input type="hidden" id="rawOverdueVal">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Pay
                                Type</label>
                            <select id="modalPayType"
                                class="form-input-fixed w-full text-gray-700 border border-gray-200 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                                style="border-radius: 0;" onchange="handlePayTypeChange()">
                                <option value="due">Due</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total Payable</label>
                                <input type="number" id="modalTotal"
                                    class="form-input-fixed bg-gray-100/50 w-full text-gray-700 border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" readonly>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-green-600 mb-1.5">Paid Amount
                                    (৳)</label>
                                <input type="number" id="modalPayAmount"
                                    class="form-input-fixed w-full border border-gray-200 bg-white text-gray-700 py-1.5 px-3 text-xs h-[32px] outline-none focus:border-blue-500"
                                    style="border-radius: 0;" oninput="calculateRemaining()" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-red-500 mb-1.5">Remaining Due</label>
                                <input type="number" id="modalRemaining"
                                    class="form-input-fixed bg-gray-100/50 text-gray-700 w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" readonly>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Pay
                                    Method</label>
                                <select id="modalPayMethod"
                                    class="form-input-fixed w-full text-gray-700 border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" onchange="checkMethod(this.value)">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Pay
                                Date</label>
                            <input type="date" id="modalPayDate"
                                class="form-input-fixed w-full text-gray-700 border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" value="{{ date('Y-m-d') }}" required>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="button" onclick="submitPayment()" id="saveBtn"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center"
                        style="border-radius: 0;">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = "{{ url('api/school-due-list') }}";
        const FEE_STATUS_MAP = @json(config('feestatus'));
        let currentPage = 1;
        let masterRecords = []; // Stores the full list for client-side filtering
        let allStudents = []; // Stores all students for filter dropdowns
        let allClasses = []; // Stores all classes from database

        // Global filter state
        let currentFilters = {
            class: '',
            group: '',
            section: '',
            session: '',
            student: '',
            status: []  // array of status strings
        };

        function toTitleCase(str) {
            if (!str) return 'N/A';
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        async function loadLedger(page = 1) {
            currentPage = page;
            const search = document.getElementById('masterSearch').value;
            const tbody = document.getElementById('masterLedgerBody');

            try {
                // Fetch data from API
                const response = await axios.get(`${API_URL}?search=${search}&page=${page}`);
                const result = response.data;
                masterRecords = result.data || [];

                // Populate filter dropdowns based on the new data
                populateFilterOptions(masterRecords);

                // Apply the actual filtering to the table display
                renderTable(result);

            } catch (error) {
                console.error(error);
                if (typeof Toastify !== "undefined") {
                    Toastify({
                        text: "Failed to load data",
                        style: {
                            background: "#dc2626"
                        }
                    }).showToast();
                }
            }
        }

        async function loadAllDataForFilters() {
            try {
                // Fetch ALL data without pagination for filter dropdowns
                const response = await axios.get(`${API_URL}?all=true`);
                masterRecords = Array.isArray(response.data) ? response.data : [];
                console.log('Loaded all records for filters:', masterRecords.length);
            } catch (error) {
                console.error("Failed to load all data for filters:", error);
            }
        }

        function populateFilterOptions(records) {
            // Populate class filter with all classes from database
            const classSelect = document.getElementById('classFilter');
            classSelect.innerHTML = '<option value="">All Classes</option>';
            allClasses.forEach(className => {
                const opt = document.createElement('option');
                opt.value = className;
                opt.innerText = toTitleCase(className);
                classSelect.appendChild(opt);
            });
            
            // Set current class filter value
            classSelect.value = currentFilters.class;

            // Populate other filters based on current class selection
            updateCascadingFilters();
        }

        function updateCascadingFilters() {
            const selectedClass = document.getElementById('classFilter').value;
            const selectedGroup = document.getElementById('groupFilter').value;
            const selectedSection = document.getElementById('sectionFilter').value;

            // Group filter - based on selected class
            const groupSelect = document.getElementById('groupFilter');
            const currentGroupValue = selectedGroup;
            groupSelect.innerHTML = '<option value="">All Groups</option>';
            
            if (selectedClass) {
                // Filter students by selected class (using allStudents like payment.blade.php)
                const filteredStudents = allStudents.filter(s => s.class_name === selectedClass);
                const uniqueGroups = [...new Set(filteredStudents.map(s => s.group_name))].filter(Boolean);
                uniqueGroups.forEach(group => {
                    const opt = document.createElement('option');
                    opt.value = group;
                    opt.innerText = toTitleCase(group);
                    groupSelect.appendChild(opt);
                });
            }
            groupSelect.value = currentGroupValue || currentFilters.group;

            // Section filter - based on selected class and group
            const sectionSelect = document.getElementById('sectionFilter');
            const currentSectionValue = selectedSection;
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            
            if (selectedClass && selectedGroup) {
                // Filter students by selected class and group (using allStudents)
                const filteredStudents = allStudents.filter(s => 
                    s.class_name === selectedClass && s.group_name === selectedGroup
                );
                const uniqueSections = [...new Set(filteredStudents.map(s => s.section_name))].filter(Boolean);
                uniqueSections.forEach(section => {
                    const opt = document.createElement('option');
                    opt.value = section;
                    opt.innerText = toTitleCase(section);
                    sectionSelect.appendChild(opt);
                });
            }
            sectionSelect.value = currentSectionValue || currentFilters.section;

            // Session filter - based on selected class, group, and section
            const sessionSelect = document.getElementById('sessionFilter');
            const currentSessionValue = currentFilters.session;
            sessionSelect.innerHTML = '<option value="">All Sessions</option>';
            
            if (selectedClass && selectedGroup && selectedSection) {
                // Filter students by selected class, group, and section (using allStudents)
                const filteredStudents = allStudents.filter(s => 
                    s.class_name === selectedClass && 
                    s.group_name === selectedGroup && 
                    s.section_name === selectedSection
                );
                const uniqueSessions = [...new Set(filteredStudents.map(s => s.session_year))].filter(Boolean);
                uniqueSessions.forEach(session => {
                    const opt = document.createElement('option');
                    opt.value = session;
                    opt.innerText = toTitleCase(session);
                    sessionSelect.appendChild(opt);
                });
            } else {
                // If no section selected, show all sessions from current class+group or class (using allStudents)
                let filteredStudents = selectedClass ? allStudents.filter(s => s.class_name === selectedClass) : allStudents;
                if (selectedGroup) {
                    filteredStudents = filteredStudents.filter(s => s.group_name === selectedGroup);
                }
                const uniqueSessions = [...new Set(filteredStudents.map(s => s.session_year))].filter(Boolean);
                uniqueSessions.forEach(session => {
                    const opt = document.createElement('option');
                    opt.value = session;
                    opt.innerText = toTitleCase(session);
                    sessionSelect.appendChild(opt);
                });
            }
            sessionSelect.value = currentSessionValue;

            // Student filter - based on selected class, group, section, and session
            const studentSelect = document.getElementById('studentFilter');
            const currentStudentValue = currentFilters.student;
            studentSelect.innerHTML = '<option value="">All Students</option>';
            
            if (selectedClass && selectedGroup && selectedSection && currentSessionValue) {
                // Filter students by selected class, group, section, and session
                const filteredStudents = allStudents.filter(s => 
                    s.class_name === selectedClass && 
                    s.group_name === selectedGroup && 
                    s.section_name === selectedSection &&
                    s.session_year === selectedSessionValue
                );
                // Sort students by name alphabetically
                const sortedStudents = filteredStudents.sort((a, b) => 
                    a.student_name.localeCompare(b.student_name)
                );
                // Populate student dropdown with ID and Name
                sortedStudents.forEach(student => {
                    const opt = document.createElement('option');
                    opt.value = student.id;
                    opt.innerText = `${student.student_id_number} - ${student.student_name}`;
                    studentSelect.appendChild(opt);
                });
            }
            studentSelect.value = currentStudentValue;
        }

        function renderTable(apiResult) {
            const tbody = document.getElementById('masterLedgerBody');
            tbody.innerHTML = '';

            // Filter the masterRecords based on current global filter state
            const filtered = masterRecords.filter(record => {
                const statusMatch = currentFilters.status.length === 0 || currentFilters.status.includes(record.status);
                return statusMatch &&
                    (currentFilters.class === '' || record.class === currentFilters.class) &&
                    (currentFilters.group === '' || record.group === currentFilters.group) &&
                    (currentFilters.section === '' || record.section === currentFilters.section) &&
                    (currentFilters.session === '' || record.session === currentFilters.session) &&
                    (currentFilters.student === '' || record.student_id == currentFilters.student);
            });

            if (filtered.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="17" class="text-center py-10 text-gray-400 font-bold">No Records Found</td></tr>';
                updatePaginationInfo(0, 0);
                renderPaginationControls(1, 1);
                return;
            }

            filtered.forEach((record, index) => {
                const due = parseFloat(record.total_due) || 0;
                const overdue = parseFloat(record.overdue_penalty) || 0;
                const sl = ((apiResult.current_page - 1) * apiResult.per_page) + (index + 1);

                let overdueDisplay = '';
                if (overdue > 0) {
                    overdueDisplay = `<span class="badge-overdue">৳${overdue.toLocaleString()}</span>`;
                } else if (record.has_alert_penalty) {
                    overdueDisplay =
                        `<span class="badge-warning-icon" title="Exceeded Last Date"><i class="mdi mdi-alert-circle-outline"></i> !</span>`;
                } else {
                    overdueDisplay = `<span class="text-gray-300">৳0</span>`;
                }

                tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="text-gray-400">${sl}</td>
                <td class="font-medium text-[10px] tracking-wider" style="color: ${FEE_STATUS_MAP[record.status]?.hex || '#9ca3af'} !important">${FEE_STATUS_MAP[record.status]?.label || record.status}</td>
                <td class="text-gray-400">${record.display_pay_date}</td>
                <td class="text-gray-600">${toTitleCase(record.class)}</td>
                <td class="text-gray-500">${toTitleCase(record.group)}</td>
                <td class="text-gray-500">${toTitleCase(record.section)}</td>
                <td class="text-gray-500">${record.session || 'N/A'}</td>
                <td class="text-gray-600">${record.student_id_number}</td>
                <td class="text-gray-700">${toTitleCase(record.student_name)}</td>
                <td class="text-gray-500">${toTitleCase(record.fees_type)}</td>
                <td class="text-gray-500">${toTitleCase(record.fee_name)}</td>
                <td class="text-gray-600">৳${parseFloat(record.total_payable).toLocaleString()}</td>
                <td class="text-gray-600">৳${parseFloat(record.total_amount).toLocaleString()}</td>
                <td class="text-gray-700 font-semibold">৳${due.toLocaleString()}</td>
                <td class="text-gray-600">${overdueDisplay}</td>
                <td class="text-gray-400">${record.display_last_pay_date}</td>
                <td class="text-center no-print">
                    <div class="flex justify-center items-center">
                        <button onclick="openPayModal(${JSON.stringify(record).replace(/"/g, '&quot;')})" 
                                class="action-icon-btn text-green-500 flex items-center justify-center">
                            <div class="flex items-center justify-center" style="height: 14px;">
                                <i class="far fa-credit-card" style="font-size: 15px; display: block;"></i>
                            </div>
                        </button>
                    </div>
                </td>
            </tr>`;
            });

            updatePaginationInfo(apiResult.to, apiResult.total);
            renderPaginationControls(apiResult.current_page, apiResult.last_page);
        }

        function applyClientFilters() {
            currentFilters.class = document.getElementById('classFilter').value;
            currentFilters.group = document.getElementById('groupFilter').value;
            currentFilters.section = document.getElementById('sectionFilter').value;
            currentFilters.session = document.getElementById('sessionFilter').value;
            currentFilters.student = document.getElementById('studentFilter').value;

            // Re-render table based on cached masterRecords
            renderTable({
                current_page: currentPage,
                per_page: masterRecords.length,
                to: masterRecords.length,
                total: masterRecords.length,
                last_page: 1
            });
        }

        function toggleStatusChip(btn) {
            btn.classList.toggle('active');
        }

        function resetClientFilters() {
            currentFilters = {
                class: '',
                group: '',
                section: '',
                session: '',
                student: '',
                status: []
            };
            document.getElementById('classFilter').value = '';
            document.getElementById('groupFilter').value = '';
            document.getElementById('sectionFilter').value = '';
            document.getElementById('sessionFilter').value = '';
            document.getElementById('studentFilter').value = '';
            document.querySelectorAll('.status-chip').forEach(c => c.classList.remove('active'));
            loadLedger(1);
        }

        function updatePaginationInfo(to, total) {
            document.getElementById('paginationInfo').innerText = `${to || 0} of ${total || 0}`;
        }

        function renderPaginationControls(current, last) {
            const container = document.getElementById('paginationControls');
            container.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = current === 1;
            prevBtn.onclick = () => loadLedger(current - 1);
            container.appendChild(prevBtn);

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `pagination-btn ${i === current ? 'active' : ''}`;
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => loadLedger(i);
                    container.appendChild(pageBtn);
                }
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = current === last || last === 0;
            nextBtn.onclick = () => loadLedger(current + 1);
            container.appendChild(nextBtn);
        }

        function openPayModal(record) {
            document.getElementById('modalPaymentId').value = record.payment_id;
            const dueVal = parseFloat(record.total_due) || 0;
            const overdueVal = parseFloat(record.overdue_penalty) || 0;
            document.getElementById('rawDueVal').value = dueVal;
            document.getElementById('rawOverdueVal').value = overdueVal;
            const typeSelect = document.getElementById('modalPayType');
            typeSelect.value = (overdueVal > 0) ? 'overdue' : 'due';
            handlePayTypeChange();
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function handlePayTypeChange() {
            const type = document.getElementById('modalPayType').value;
            const dueVal = parseFloat(document.getElementById('rawDueVal').value) || 0;
            const overdueVal = parseFloat(document.getElementById('rawOverdueVal').value) || 0;
            const targetVal = (type === 'overdue') ? overdueVal : dueVal;
            document.getElementById('modalTotal').value = targetVal;
            document.getElementById('modalPayAmount').value = targetVal;
            calculateRemaining();
        }

        function calculateRemaining() {
            const total = parseFloat(document.getElementById('modalTotal').value) || 0;
            const paying = parseFloat(document.getElementById('modalPayAmount').value) || 0;
            document.getElementById('modalRemaining').value = (total - paying).toFixed(2);
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function checkMethod(val) {
            if (val === 'bank') {
                Swal.fire({
                    title: 'Notice',
                    text: 'Bank gateway is currently under maintenance.',
                    icon: 'info',
                    confirmButtonColor: '#2563eb'
                });
                document.getElementById('modalPayMethod').value = 'cash';
            }
        }

        async function submitPayment() {
            const id = document.getElementById('modalPaymentId').value;
            const amount = document.getElementById('modalPayAmount').value;
            const method = document.getElementById('modalPayMethod').value;
            const date = document.getElementById('modalPayDate').value;
            const type = document.getElementById('modalPayType').value;

            if (!amount || amount <= 0) {
                Toastify({
                    text: "Invalid amount",
                    style: {
                        background: "#dc2626"
                    }
                }).showToast();
                return;
            }

            try {
                await axios.post(`{{ url('api/due-list/pay') }}`, {
                    school_student_fee_id: id,
                    type_amount: amount,
                    pay_method: method,
                    pay_date: date,
                });
                Toastify({
                    text: "Payment Successful",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                loadLedger(currentPage);
            } catch (error) {
                Swal.fire('Error', 'Payment failed to process.', 'error');
            }
        }



        async function loadStudents() {
            try {
                // Request all students without pagination for filter dropdowns
                const res = await axios.get('/api/school/students?all=true');
                allStudents = Array.isArray(res.data) ? res.data : res.data.data;
                console.log('allStudents count:', allStudents.length);
            } catch (e) {
                console.error("Students retrieval failed", e);
            }
        }

        async function loadClasses() {
            try {
                // Fetch all classes from the classes table
                const res = await axios.get('/api/classes');
                const classesData = Array.isArray(res.data) ? res.data : res.data.data;
                allClasses = classesData.map(c => c.class_name).filter(Boolean);
                
                // Sort classes numerically (extract number from class name for proper ordering)
                allClasses.sort((a, b) => {
                    const numA = parseInt(a.replace(/\D/g, ''), 10) || 0;
                    const numB = parseInt(b.replace(/\D/g, ''), 10) || 0;
                    return numA - numB;
                });
                
                console.log('All classes from database (sorted):', allClasses);
            } catch (e) {
                console.error("Classes retrieval failed", e);
            }
        }

        let searchTimer;
        document.getElementById('masterSearch').oninput = () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadLedger(1), 500);
        };

        window.onload = async () => {
            await loadClasses();
            await loadStudents(); // Load students for filter dropdowns
            loadLedger(1);
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Cascading filter event listeners - only update dropdowns, don't apply filter yet
            document.getElementById('classFilter').addEventListener('change', function() {
                currentFilters.class = this.value;
                // Reset downstream filters
                document.getElementById('groupFilter').innerHTML = '<option value="">All Groups</option>';
                document.getElementById('groupFilter').value = '';
                document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
                document.getElementById('sectionFilter').value = '';
                currentFilters.group = '';
                currentFilters.section = '';
                updateCascadingFilters();
            });

            document.getElementById('groupFilter').addEventListener('change', function() {
                currentFilters.group = this.value;
                // Reset downstream filters
                document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
                document.getElementById('sectionFilter').value = '';
                currentFilters.section = '';
                updateCascadingFilters();
            });

            document.getElementById('sectionFilter').addEventListener('change', function() {
                currentFilters.section = this.value;
                updateCascadingFilters();
            });

            document.getElementById('sessionFilter').addEventListener('change', function() {
                currentFilters.session = this.value;
                // Update student dropdown when session changes
                updateCascadingFilters();
            });

            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', !show);
            };

            const btnFilter = document.getElementById('btnFilter');
            if (btnFilter) btnFilter.addEventListener('click', async () => {
                await loadStudents();
                populateFilterOptions(masterRecords);
                toggleModal('filterModal', true);
            });

            const resetFilter = document.getElementById('resetFilter');
            if (resetFilter) resetFilter.addEventListener('click', () => {
                resetClientFilters();
                toggleModal('filterModal', false);
            });

            const applyFilter = document.getElementById('applyFilter');
            if (applyFilter) applyFilter.addEventListener('click', () => {
                applyClientFilters();
                toggleModal('filterModal', false);
            });

            const btnExport = document.getElementById('btnExport');
            if (btnExport) btnExport.addEventListener('click', () => toggleModal('exportModal', true));

            const closeExport = document.getElementById('closeExport');
            if (closeExport) closeExport.addEventListener('click', () => toggleModal('exportModal', false));

            const btnStatusFilter = document.getElementById('btnStatusFilter');
            if (btnStatusFilter) btnStatusFilter.addEventListener('click', async () => {
                await loadAllDataForFilters();
                toggleModal('statusFilterModal', true);
            });

            const resetStatusFilter = document.getElementById('resetStatusFilter');
            if (resetStatusFilter) resetStatusFilter.addEventListener('click', () => {
                document.querySelectorAll('.status-chip').forEach(c => c.classList.remove('active'));
                currentFilters.status = [];
                applyClientFilters();
                toggleModal('statusFilterModal', false);
            });

            const applyStatusFilter = document.getElementById('applyStatusFilter');
            if (applyStatusFilter) applyStatusFilter.addEventListener('click', () => {
                const selected = [];
                document.querySelectorAll('.status-chip.active').forEach(c => {
                    selected.push(c.dataset.status);
                });
                currentFilters.status = selected;
                applyClientFilters();
                toggleModal('statusFilterModal', false);
            });

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });
    </script>
@endsection