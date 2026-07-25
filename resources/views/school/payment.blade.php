@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

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
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
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

        #paginationInfo {
            font-size: 9px !important;
            font-weight: 800 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        @media (max-width: 768px) {

            th,
            td {
                padding: 0 8px !important;
                height: 28px !important;
            }

            .pagination-container {
                min-height: 35px !important;
                padding: 0.4rem 0.75rem !important;
            }

            .pagination-btn,
            .page-link-btn {
                height: 24px !important;
                min-width: 24px !important;
                font-size: 9px !important;
            }
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
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
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        #feeMonthGrid > div:hover:not(.cursor-not-allowed) {
            transform: scale(1.05);
            z-index: 1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 0; }

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
                            <input type="text" id="paySearch" onkeyup="fetchPayments(1)"
                                placeholder="Search Student ID, Name..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        {{-- <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button> --}}

                        <button id="btnPaymentSlip"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export Payment Slip
                        </button>

                        <button onclick="openPaymentModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Collect Fee
                        </button>


                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="paySearchMobile" onkeyup="fetchPayments(1)"
                        placeholder="Search Student ID, Name..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div id="filterModal" class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    {{-- Modal Title Section --}}
                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Payment filter
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

            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-6 sm:p-20">
                <div class="bg-white w-full max-w-sm modal-content-sharp shadow-2xl border border-gray-100" style="border-radius:0;">

                    {{-- Header --}}
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-[13px] font-medium text-gray-800 capitalize tracking-normal">Export Payments</h3>
                        <button id="closeExport" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Filters --}}
                    <div class="px-5 pt-4 pb-2 space-y-3">
                        <p class="text-[9px] text-gray-400 uppercase tracking-widest">Filter by (optional)</p>

                        {{-- Student ID Quick Search --}}
                        <div>
                            <label class="block text-[10px] text-blue-600 font-medium mb-1">Student ID (Quick Search)</label>
                            <div class="relative">
                                <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="exportStudentIdSearch"
                                    placeholder="Type student ID..."
                                    class="w-full border border-blue-200 bg-blue-50/20 pl-7 pr-3 py-1.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                                    style="border-radius:0;" />
                            </div>
                            <p id="exportIdNotFound" class="hidden text-[9px] text-red-500 mt-1">No student found with this ID.</p>
                        </div>

                        <div class="h-px bg-gray-100"></div>

                        {{-- Class --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Class</label>
                            <div class="relative">
                                <select id="exportClassFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Classes</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Group</label>
                            <div class="relative">
                                <select id="exportGroupFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Groups</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Section</label>
                            <div class="relative">
                                <select id="exportSectionFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Sections</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Session</label>
                            <div class="relative">
                                <select id="exportSessionFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Sessions</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Student --}}
                        <div>
                            <label class="block text-[10px] mb-1 font-medium" style="color:#2563eb;">
                                Student <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="exportStudentFilter" required
                                    class="w-full border py-1.5 pl-2.5 pr-7 text-xs outline-none appearance-none h-[32px] border-blue-300 bg-blue-50/30 focus:border-blue-500"
                                    style="border-radius:0;">
                                    <option value="">— Select Student —</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                            <p id="exportStudentError" class="hidden text-[9px] text-red-500 mt-1">Please select a student.</p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="h-px bg-gray-100 mx-5 my-3"></div>

                    {{-- Export Buttons --}}
                    <div class="px-5 space-y-2">
                        {{-- PDF --}}
                        <button id="exportPdf"
                            class="w-full flex items-center gap-3 px-4 py-2.5 border border-gray-200 hover:border-blue-500 hover:bg-blue-50/40 text-gray-600 hover:text-blue-600 transition-all group"
                            style="border-radius:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 group-hover:text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <div class="text-left">
                                <div class="text-[11px] font-semibold tracking-wide uppercase">Download Report</div>
                                <div class="text-[9px] text-gray-400 mt-0.5">Save as A4 PDF file</div>
                            </div>
                        </button>

                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-4">
                        <button id="closeExportFooter"
                            class="w-full h-[30px] text-[10px] text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 transition-all tracking-widest uppercase"
                            style="border-radius:0;">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            {{-- Payment Slip Modal --}}
            <div id="slipModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-6 sm:p-20">
                <div class="bg-white w-full max-w-sm modal-content-sharp shadow-2xl border border-gray-100" style="border-radius:0;">

                    {{-- Header --}}
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-[13px] font-medium text-gray-800 capitalize tracking-normal">Payment Slip</h3>
                        <button id="closeSlip" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Filters --}}
                    <div class="px-5 pt-4 pb-2 space-y-3">
                        <p class="text-[9px] text-gray-400 uppercase tracking-widest">Filter by</p>

                        {{-- Student ID Quick Search --}}
                        <div>
                            <label class="block text-[10px] text-blue-600 font-medium mb-1">Student ID (Quick Search)</label>
                            <div class="relative">
                                <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="slipStudentIdSearch"
                                    placeholder="Type student ID..."
                                    class="w-full border border-blue-200 bg-blue-50/20 pl-7 pr-3 py-1.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                                    style="border-radius:0;" />
                            </div>
                            <p id="slipIdNotFound" class="hidden text-[9px] text-red-500 mt-1">No student found with this ID.</p>
                        </div>

                        <div class="h-px bg-gray-100"></div>

                        {{-- Class --}}
                        <div>
                            <label class="block text-[10px] text-blue-600 font-medium mb-1">Class</label>
                            <div class="relative">
                                <select id="slipClassFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Classes</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Group</label>
                            <div class="relative">
                                <select id="slipGroupFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Groups</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Section</label>
                            <div class="relative">
                                <select id="slipSectionFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Sections</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session --}}
                        <div>
                            <label class="block text-[10px] text-gray-500 mb-1">Session</label>
                            <div class="relative">
                                <select id="slipSessionFilter"
                                    class="w-full border border-gray-200 bg-white py-1.5 pl-2.5 pr-7 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                    style="border-radius:0;">
                                    <option value="">All Sessions</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Date Range --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1">From Date</label>
                                <input type="date" id="slipFromDate"
                                    class="w-full border border-gray-200 bg-white py-1.5 px-2.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                                    style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1">To Date</label>
                                <input type="date" id="slipToDate"
                                    class="w-full border border-gray-200 bg-white py-1.5 px-2.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                                    style="border-radius:0;" />
                            </div>
                        </div>

                        {{-- Student --}}
                        <div>
                            <label class="block text-[10px] mb-1 font-medium" style="color:#2563eb;">
                                Student <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="slipStudentFilter" required
                                    class="w-full border py-1.5 pl-2.5 pr-7 text-xs outline-none appearance-none h-[32px] border-blue-300 bg-blue-50/30 focus:border-blue-500"
                                    style="border-radius:0;">
                                    <option value="">— Select Student —</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                            <p id="slipStudentError" class="hidden text-[9px] text-red-500 mt-1">Please select a student.</p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="h-px bg-gray-100 mx-5 my-3"></div>

                    {{-- Show slip page --}}
                    <div class="px-5">
                        <button id="slipShowPage"
                            class="w-full flex items-center gap-3 px-4 py-2.5 border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/40 text-gray-600 hover:text-indigo-600 transition-all group"
                            style="border-radius:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400 group-hover:text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"/>
                            </svg>
                            <div class="text-left">
                                <div class="text-[11px] font-semibold tracking-wide uppercase">Show Payment Slip</div>
                                <div class="text-[9px] text-gray-400 mt-0.5">View slip page with Print, PDF &amp; Excel download</div>
                            </div>
                        </button>
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-4">
                        <button id="closeSlipFooter"
                            class="w-full h-[30px] text-[10px] text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 transition-all tracking-widest uppercase"
                            style="border-radius:0;">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card" id="printArea">
                <div class="table-responsive">
                    <table class="min-w-[1500px]">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th width="110">Pay Date</th>
                                <th width="120">Pay Method</th>
                                <th width="90">Class</th>
                                <th width="90">Group</th>
                                <th width="90">Section</th>
                                <th width="90">Session</th>
                                <th width="110">Student Id</th>
                                <th>Student Name</th>
                                <th width="140">Fee Type</th>
                                <th width="140">Fee Name</th>
                                <th width="110">Total Fee</th>
                                <th width="110">Paid Fee</th>
                                <th width="110">Due Fee</th>

                                <th class="text-center no-print" width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div class="pagination-container no-print">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Collect Fee Modal (redesigned with cascade selects + month grid + advance integration) --}}
    <div id="paymentModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-4 sm:px-20 py-8 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-4xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[90vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Collect Fee
                </h3>
            </div>

            <form id="paymentForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="admission_student_id">
                <input type="hidden" id="for_month" value="">
                <input type="hidden" id="is_advance" value="0">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    {{-- Cascade Select Row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-4">
                        <div>
                            <label class="block text-[10px] capitalize text-blue-600 mb-1 font-medium">Student ID</label>
                            <input type="text" id="feeStudentIdSearch" placeholder="Type ID..."
                                class="w-full border border-blue-200 bg-blue-50/20 pl-2 pr-2 py-1.5 text-xs outline-none focus:border-blue-500 h-[32px]"
                                style="border-radius:0;" />
                            <p id="feeIdNotFound" class="hidden text-[9px] text-red-500 mt-0.5">Not found.</p>
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize text-gray-500 mb-1">Class</label>
                            <select id="feeClassFilter"
                                class="w-full border border-gray-200 bg-white py-1.5 pl-2 pr-6 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                style="border-radius:0;">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize text-gray-500 mb-1">Group</label>
                            <select id="feeGroupFilter"
                                class="w-full border border-gray-200 bg-white py-1.5 pl-2 pr-6 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                style="border-radius:0;">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize text-gray-500 mb-1">Section</label>
                            <select id="feeSectionFilter"
                                class="w-full border border-gray-200 bg-white py-1.5 pl-2 pr-6 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                style="border-radius:0;">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] capitalize text-gray-500 mb-1">Session</label>
                            <select id="feeSessionFilter"
                                class="w-full border border-gray-200 bg-white py-1.5 pl-2 pr-6 text-xs outline-none focus:border-blue-500 appearance-none h-[32px]"
                                style="border-radius:0;">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-span-1 sm:col-span-5">
                            <label class="block text-[10px] mb-1 font-medium text-blue-600">Student <span class="text-red-500">*</span></label>
                            <select id="feeStudentFilter" required
                                class="w-full border py-1.5 pl-2.5 pr-7 text-xs outline-none appearance-none h-[32px] border-blue-300 bg-blue-50/30 focus:border-blue-500"
                                style="border-radius:0;">
                                <option value="">— Select Student —</option>
                            </select>
                            <p id="feeStudentError" class="hidden text-[9px] text-red-500 mt-0.5">Please select a student.</p>
                        </div>
                    </div>

                    {{-- Student Details (auto-filled on select) --}}
                    <div id="feeStudentInfo" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 p-3 bg-white border border-gray-200">
                        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Name</span><p id="feeDispName" class="text-xs font-medium text-blue-600 truncate">-</p></div>
                        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Class</span><p id="feeDispClass" class="text-xs">-</p></div>
                        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Group</span><p id="feeDispGroup" class="text-xs">-</p></div>
                        <div><span class="text-[9px] text-gray-400 uppercase tracking-wider">Section</span><p id="feeDispSection" class="text-xs">-</p></div>
                    </div>

                    {{-- Month Grid --}}
                    <div id="feeMonthGridWrap" class="hidden mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[9px] text-gray-400 uppercase tracking-widest">Session Months</span>
                            <span id="feeClearanceStatus" class="text-[9px] font-medium hidden"></span>
                        </div>
                        <div id="feeMonthGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-1.5"></div>
                    </div>

                    {{-- Payment Information --}}
                    <div id="feePaymentSection" class="hidden">
                        <div class="border-b border-gray-200/60 pb-1 mb-3">
                            <span class="text-gray-400 text-[9px] tracking-widest">Payment Information</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Fees Type</label>
                                <select id="fees_type" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs outline-none h-[32px]"
                                    style="border-radius: 0;">
                                    <option value="">Select Fees Type</option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Fee Name</label>
                                <select id="fee_name" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs outline-none h-[32px]"
                                    style="border-radius: 0;">
                                    <option value="">Select Fee Name</option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total Payable</label>
                                <input type="number" id="total_payable" readonly
                                    class="form-input-fixed w-full border border-gray-200 bg-gray-100 py-1.5 px-3 text-xs text-gray-700 cursor-not-allowed h-[32px]"
                                    style="border-radius: 0;" />
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white p-4 border border-gray-100">
                                    <div>
                                        <label class="block text-[10px] capitalize tracking-normal text-green-600 mb-1.5 font-medium">Paid Amount (৳)</label>
                                        <input type="number" id="type_amount" required placeholder="0.00"
                                            class="form-input-fixed w-full border border-green-200 bg-green-50/30 py-1.5 px-3 text-xs text-green-700 outline-none h-[32px]"
                                            style="border-radius: 0;" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] capitalize tracking-normal text-red-600 mb-1.5 font-medium">Remaining Due</label>
                                        <input type="number" id="payable_due" readonly placeholder="0.00"
                                            class="form-input-fixed w-full border border-red-200 bg-red-50/10 py-1.5 px-3 text-xs text-red-600 cursor-not-allowed h-[32px]"
                                            style="border-radius: 0;" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Payment Method</label>
                                <select id="pay_method"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs outline-none h-[32px]"
                                    style="border-radius: 0;">
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Payment Date</label>
                                <input type="date" id="pay_date" value="{{ date('Y-m-d') }}" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs outline-none h-[32px]"
                                    style="border-radius: 0;" />
                            </div>
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
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center gap-1.5 whitespace-nowrap transition-all duration-200"
                        style="border-radius: 0;">
                        <svg id="saveBtnSpinner" class="hidden animate-spin h-3 w-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span id="saveBtnText">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // --- Configuration & Global State ---
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let allStudents = [];
        let allClasses = [];
        let currentPage = 1;
        let editId = null;

        // Filter State
        let activeFilters = {
            class: null,
            group: null,
            section: null,
            session: null,
            student: null
        };

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', async function() {
            // First load students and classes so we can build filters
            await loadStudents();
            await loadClasses();
            setupFilterListeners();

            // Then fetch payments
            fetchPayments();

            document.getElementById('pay_method').addEventListener('change', function() {
                if (this.value === 'Bank') {
                    Swal.fire({
                        title: 'Gateway Unavailable',
                        text: 'Bank payment is not available right now. Please pay with cash.',
                        icon: 'info',
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Understood'
                    });
                    this.value = 'Cash';
                }
            });

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };

            // --- Export Modal ---
            const exportModal = document.getElementById('exportModal');

            // Helper: populate a select with an array of string options
            function exportPopulate(selectId, options, placeholder) {
                const sel = document.getElementById(selectId);
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                options.forEach(o => { if (o) sel.innerHTML += `<option value="${o}">${o}</option>`; });
            }

            // Open: populate class list and reset downstream
            document.getElementById('btnExport').addEventListener('click', function() {
                exportPopulate('exportClassFilter',   allClasses, 'All Classes');
                exportPopulate('exportGroupFilter',   [], 'All Groups');
                exportPopulate('exportSectionFilter', [], 'All Sections');
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                const studentSel = document.getElementById('exportStudentFilter');
                studentSel.classList.remove('border-red-400');
                studentSel.classList.add('border-blue-300');
                document.getElementById('exportStudentError').classList.add('hidden');
                document.getElementById('exportStudentIdSearch').value = '';
                document.getElementById('exportIdNotFound').classList.add('hidden');
                exportModal.classList.remove('hidden');
            });

            const closeExportModal = () => exportModal.classList.add('hidden');
            document.getElementById('closeExport').addEventListener('click', closeExportModal);
            document.getElementById('closeExportFooter').addEventListener('click', closeExportModal);

            // Class → Group cascade
            document.getElementById('exportClassFilter').addEventListener('change', function() {
                const cls = this.value;
                exportPopulate('exportGroupFilter',   [], 'All Groups');
                exportPopulate('exportSectionFilter', [], 'All Sections');
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls) return;
                const groups = [...new Set(
                    allStudents.filter(s => s.class_name === cls).map(s => s.group_name)
                )].filter(Boolean);
                exportPopulate('exportGroupFilter', groups, 'All Groups');
            });

            // Group → Section cascade
            document.getElementById('exportGroupFilter').addEventListener('change', function() {
                const cls   = document.getElementById('exportClassFilter').value;
                const grp   = this.value;
                exportPopulate('exportSectionFilter', [], 'All Sections');
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls || !grp) return;
                const sections = [...new Set(
                    allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name)
                )].filter(Boolean);
                exportPopulate('exportSectionFilter', sections, 'All Sections');
            });

            // Section → Session cascade
            document.getElementById('exportSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('exportClassFilter').value;
                const grp = document.getElementById('exportGroupFilter').value;
                const sec = this.value;
                exportPopulate('exportSessionFilter', [], 'All Sessions');
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(
                    allStudents
                        .filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec)
                        .map(s => s.session_year)
                )].filter(Boolean);
                exportPopulate('exportSessionFilter', sessions, 'All Sessions');
            });

            // Session → Student cascade
            document.getElementById('exportSessionFilter').addEventListener('change', function() {
                const cls = document.getElementById('exportClassFilter').value;
                const grp = document.getElementById('exportGroupFilter').value;
                const sec = document.getElementById('exportSectionFilter').value;
                const ses = this.value;
                exportPopulate('exportStudentFilter', [], '— Select Student —');
                if (!ses) return;
                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name   === cls) &&
                    (!grp || s.group_name   === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const sel = document.getElementById('exportStudentFilter');
                sel.innerHTML = '<option value="">— Select Student —</option>';
                filtered.forEach(s => {
                    sel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
            });

            // Hide error on student select
            document.getElementById('exportStudentFilter').addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('exportStudentError').classList.add('hidden');
                    this.classList.remove('border-red-400');
                    this.classList.add('border-blue-300');
                }
            });

            // Student ID quick search — auto-fills all cascades and selects the student
            document.getElementById('exportStudentIdSearch').addEventListener('input', function() {
                const sid = this.value.trim();
                const errEl = document.getElementById('exportIdNotFound');

                if (!sid) {
                    errEl.classList.add('hidden');
                    return;
                }

                const student = allStudents.find(s => String(s.student_id_number) === sid);

                if (!student) {
                    errEl.classList.remove('hidden');
                    // Reset all cascading fields
                    exportPopulate('exportClassFilter',   allClasses, 'All Classes');
                    exportPopulate('exportGroupFilter',   [], 'All Groups');
                    exportPopulate('exportSectionFilter', [], 'All Sections');
                    exportPopulate('exportSessionFilter', [], 'All Sessions');
                    exportPopulate('exportStudentFilter', [], '— Select Student —');
                    const studentSel = document.getElementById('exportStudentFilter');
                    studentSel.classList.remove('border-red-400');
                    studentSel.classList.add('border-blue-300');
                    document.getElementById('exportStudentError').classList.add('hidden');
                    return;
                }

                errEl.classList.add('hidden');

                // Fill Class
                exportPopulate('exportClassFilter', allClasses, 'All Classes');
                document.getElementById('exportClassFilter').value = student.class_name;

                // Fill Group
                const groups = [...new Set(
                    allStudents.filter(s => s.class_name === student.class_name).map(s => s.group_name)
                )].filter(Boolean);
                exportPopulate('exportGroupFilter', groups, 'All Groups');
                document.getElementById('exportGroupFilter').value = student.group_name;

                // Fill Section
                const sections = [...new Set(
                    allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name).map(s => s.section_name)
                )].filter(Boolean);
                exportPopulate('exportSectionFilter', sections, 'All Sections');
                document.getElementById('exportSectionFilter').value = student.section_name;

                // Fill Session
                const sessions = [...new Set(
                    allStudents.filter(s =>
                        s.class_name   === student.class_name &&
                        s.group_name   === student.group_name &&
                        s.section_name === student.section_name
                    ).map(s => s.session_year)
                )].filter(Boolean);
                exportPopulate('exportSessionFilter', sessions, 'All Sessions');
                document.getElementById('exportSessionFilter').value = student.session_year;

                // Fill Student dropdown and select this student
                const peers = allStudents.filter(s =>
                    s.class_name   === student.class_name &&
                    s.group_name   === student.group_name &&
                    s.section_name === student.section_name &&
                    s.session_year === student.session_year
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));

                const studentSel = document.getElementById('exportStudentFilter');
                studentSel.innerHTML = '<option value="">— Select Student —</option>';
                peers.forEach(s => {
                    studentSel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
                studentSel.value = student.id;

                // Clear any validation error
                studentSel.classList.remove('border-red-400');
                studentSel.classList.add('border-blue-300');
                document.getElementById('exportStudentError').classList.add('hidden');
            });

            // Build params from export modal filters
            function getExportParams() {
                const params = new URLSearchParams();
                const cls  = document.getElementById('exportClassFilter').value;
                const grp  = document.getElementById('exportGroupFilter').value;
                const sec  = document.getElementById('exportSectionFilter').value;
                const ses  = document.getElementById('exportSessionFilter').value;
                const stu  = document.getElementById('exportStudentFilter').value;
                if (cls) params.append('class',   cls);
                if (grp) params.append('group',   grp);
                if (sec) params.append('section', sec);
                if (ses) params.append('session', ses);
                if (stu) params.append('student', stu);
                const search = document.getElementById('paySearch')?.value?.trim();
                if (search) params.append('search', search);
                return params;
            }

            // Download report as A4 PNG image using html2canvas
            document.getElementById('exportPdf').addEventListener('click', async function() {
                const studentSel = document.getElementById('exportStudentFilter');
                if (!studentSel.value) {
                    studentSel.classList.remove('border-blue-300');
                    studentSel.classList.add('border-red-400');
                    document.getElementById('exportStudentError').classList.remove('hidden');
                    studentSel.focus();
                    return;
                }

                closeExportModal();

                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const studentId  = studentSel.value;
                    const studentObj = allStudents.find(s => String(s.id) === String(studentId));
                    const cls = document.getElementById('exportClassFilter').value   || '-';
                    const grp = document.getElementById('exportGroupFilter').value   || '-';
                    const sec = document.getElementById('exportSectionFilter').value || '-';
                    const ses = document.getElementById('exportSessionFilter').value || '-';

                    const [payRes, schoolRes] = await Promise.all([
                        axios.get('/api/school/payments', { params: { per_page: 1000 } }),
                        axios.get('/api/get-school-info')
                    ]);

                    const payments = (payRes.data.data || [])
                        .filter(p => String(p.admission_student_id) === String(studentId))
                        .sort((a, b) => new Date(a.pay_date) - new Date(b.pay_date));

                    const school     = schoolRes.data.data || schoolRes.data || {};
                    const schoolName = school.school_name || 'School';
                    const addressLine1 = school.village || '';
                    const addressLine2 = [school.upazila, school.district, school.division].filter(Boolean).join(', ');

                    // Totals
                    const seenFees = new Set();
                    let grandTotal = 0, grandPaid = 0;
                    payments.forEach(p => {
                        const k = p.fees_type + '|' + p.fee_name;
                        if (!seenFees.has(k)) { seenFees.add(k); grandTotal += parseFloat(p.total_payable) || 0; }
                        grandPaid += parseFloat(p.type_amount) || 0;
                    });
                    const grandDue = Math.max(grandTotal - grandPaid, 0);

                    const fmt     = n => Number(n).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const fmtDate = d => { const dt = new Date(d); return String(dt.getDate()).padStart(2,'0') + '/' + String(dt.getMonth()+1).padStart(2,'0') + '/' + dt.getFullYear(); };
                    const now     = new Date();
                    const genDate = fmtDate(now);
                    const today   = new Date(); today.setHours(0,0,0,0);

                    // ── A4 at 96 dpi: 794 x 1123 px ──────────────────────
                    // Use scale:2 so output is 1588 x 2246 px (print quality)
                    const W = 794;
                    const H = 1123;

                    // Table rows
                    const shownFees = new Set();
                    const border    = '1px solid #d1d5db';
                    const tdS       = 'padding:5px 7px;border:' + border + ';vertical-align:middle;font-size:9.5px;color:#111827;';
                    const rows = payments.length === 0
                        ? '<tr><td colspan="10" style="' + tdS + 'text-align:center;color:#6b7280;font-style:italic;padding:20px;">No payment records found.</td></tr>'
                        : payments.map(function(p, i) {
                            const total   = parseFloat(p.total_payable) || 0;
                            const paid    = parseFloat(p.type_amount)   || 0;
                            const due     = parseFloat(p.payable_due)   || 0;
                            const fk      = p.fees_type + '|' + p.fee_name;
                            const showTot = !shownFees.has(fk); if (showTot) shownFees.add(fk);
                            const pd      = new Date(p.pay_date);
                            const overdue = due > 0 && pd < today;
                            const sl      = (p.status || 'unpaid').charAt(0).toUpperCase() + (p.status || 'unpaid').slice(1);
                            const rowBg   = i % 2 === 0 ? '#ffffff' : '#f9fafb';
                            return '<tr style="background:' + rowBg + ';">'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + (i + 1) + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + fmtDate(p.pay_date) + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.pay_method || '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + sl + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.fees_type || '-') + '</td>'
                                + '<td style="' + tdS + 'padding-bottom: 50px;">' + (p.fee_name  || '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + (showTot ? fmt(total) : '-') + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + fmt(paid) + '</td>'
                                + '<td style="' + tdS + 'text-align:right;padding-bottom: 50px;">' + fmt(due) + '</td>'
                                + '<td style="' + tdS + 'text-align:center;padding-bottom: 50px;">' + (overdue ? 'YES' : '-') + '</td>'
                                + '</tr>';
                        }).join('');

                    const tfoot = payments.length > 0
                        ? '<tfoot><tr style="background:#f3f4f6;">'
                            + '<td colspan="6" style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">Grand Total</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandTotal) + '</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandPaid) + '</td>'
                            + '<td style="' + tdS + 'text-align:right;font-weight:700;border-top:2px solid #374151;">' + fmt(grandDue) + '</td>'
                            + '<td style="' + tdS + 'border-top:2px solid #374151;"></td>'
                            + '</tr></tfoot>'
                        : '';

                    // ── Off-screen A4 wrapper ─────────────────────────────
                    const wrap = document.createElement('div');
                    wrap.id    = '__rpt__';
                    wrap.style.cssText = [
                        'position:fixed', 'left:-9999px', 'top:0',
                        'width:' + W + 'px',
                        'background:#fff',
                        'font-family:Arial,sans-serif',
                        'font-size:10px',
                        'color:#111827',
                        'box-sizing:border-box',
                        'padding:36px 40px'
                    ].join(';');

                    // ── Header: school left, student right ────────────────
                    const headerHtml =
                        '<table style="width:100%;border-collapse:collapse;margin-bottom:0;border:1px solid #e5e7eb;">'
                        + '<tr>'
                        // Left: school info
                        + '<td style="width:50%;vertical-align:top;padding:14px 18px;border-right:1px solid #e5e7eb;background:#f9fafb;">'
                            + '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#6b7280;margin-bottom:5px;">School Information</div>'
                            + '<div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:5px;line-height:1.3;">' + schoolName + '</div>'
                            + (addressLine1        ? '<div style="font-size:10px;color:#374151;margin-bottom:2px;"><strong>Address:</strong> ' + addressLine1 + '</div>' : '')
                            + (addressLine2        ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;">' + addressLine2 + '</div>' : '')
                            + (school.mobile       ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Phone:</strong> ' + school.mobile + '</div>' : '')
                            + (school.email        ? '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Email:</strong> ' + school.email + '</div>' : '')
                            + (school.eiin_number  ? '<div style="font-size:10px;color:#374151;margin-bottom:14px;"><strong>EIIN:</strong> ' + school.eiin_number + '</div>' : '<div style="margin-bottom:14px;"></div>')
                        + '</td>'
                        // Right: student info
                        + '<td style="width:50%;vertical-align:top;padding:14px 18px;background:#f9fafb;">'
                            + '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#6b7280;margin-bottom:5px;">Student Information</div>'
                            + '<div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:5px;line-height:1.3;">' + (studentObj ? studentObj.student_name : '-') + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Student ID:</strong> ' + (studentObj ? studentObj.student_id_number : '-') + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Class:</strong> ' + cls + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Group:</strong> ' + grp + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:3px;"><strong>Section:</strong> ' + sec + '</div>'
                            + '<div style="font-size:10px;color:#374151;margin-bottom:14px;"><strong>Session:</strong> ' + ses + '</div>'
                        + '</td>'
                        + '</tr>'
                        + '</table>';

                    // ── Title bar ─────────────────────────────────────────
                    const titleHtml =
                        '<div style="text-align:center;margin:14px 0 4px;padding-bottom:14px;">'
                        + '<div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#111827;">Payment Report</div>'
                        + '<div style="font-size:8.5px;color:#6b7280;margin-top:2px;">Generated: ' + genDate + '</div>'
                        + '</div>'
                        + '<div style="border-top:2px solid #111827;margin-bottom:14px;"></div>';

                    // ── Summary row ───────────────────────────────────────
                    const summaryHtml =
                        '<table style="width:100%;border-collapse:collapse;margin-bottom:24px;border:1px solid #d1d5db;">'
                        + '<tr style="background:#f3f4f6;">'
                        + '<td style="padding:14px 16px 30px;border-right:1px solid #d1d5db;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Total Fee</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandTotal) + '</div>'
                        + '</td>'
                        + '<td style="padding:14px 16px 30px;border-right:1px solid #d1d5db;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Total Paid</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandPaid) + '</div>'
                        + '</td>'
                        + '<td style="padding:14px 16px 30px;text-align:center;">'
                            + '<div style="font-size:8.5px;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;">Remaining Due</div>'
                            + '<div style="font-size:16px;font-weight:700;color:#111827;padding-bottom: 30px;">&#2547; ' + fmt(grandDue) + '</div>'
                        + '</td>'
                        + '</tr>'
                        + '</table>';

                    // ── Table ─────────────────────────────────────────────
                    const thS = 'padding:6px 7px;border:' + border + ';font-size:8.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#111827;background:#f3f4f6;white-space:nowrap;';
                    const tableHtml =
                        '<table style="width:100%;border-collapse:collapse;">'
                        + '<thead><tr>'
                        + '<th style="' + thS + 'text-align:center;width:28px;">SL</th>'
                        + '<th style="' + thS + 'width:70px;">Pay Date</th>'
                        + '<th style="' + thS + 'width:68px;">Method</th>'
                        + '<th style="' + thS + 'text-align:center;width:60px;">Status</th>'
                        + '<th style="' + thS + '">Fee Type</th>'
                        + '<th style="' + thS + '">Fee Name</th>'
                        + '<th style="' + thS + 'text-align:right;width:72px;">Total Fee</th>'
                        + '<th style="' + thS + 'text-align:right;width:64px;">Paid</th>'
                        + '<th style="' + thS + 'text-align:right;width:64px;">Due</th>'
                        + '<th style="' + thS + 'text-align:center;width:52px;">Overdue</th>'
                        + '</tr></thead>'
                        + '<tbody>' + rows + '</tbody>'
                        + tfoot
                        + '</table>';

                    // ── Footer ────────────────────────────────────────────
                    const footerHtml =
                        '<div style="margin-top:20px;padding-top:8px;border-top:1px solid #d1d5db;display:flex;justify-content:space-between;font-size:8px;color:#6b7280;">'
                        + '<span>' + schoolName + ' — Confidential Payment Record</span>'
                        + '<span>Page 1 | ' + genDate + '</span>'
                        + '</div>';

                    wrap.innerHTML = headerHtml + titleHtml + summaryHtml + tableHtml + footerHtml;
                    document.body.appendChild(wrap);

                    const canvas = await html2canvas(wrap, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        width: W,
                        height: wrap.scrollHeight,
                        windowWidth: W,
                        logging: false,
                        onclone: function(doc) {
                            const el = doc.getElementById('__rpt__');
                            if (el) { el.style.left = '0'; el.style.position = 'relative'; }
                        }
                    });

                    document.body.removeChild(wrap);

                    // ── Embed canvas into A4 PDF and download ───────────
                    const { jsPDF } = window.jspdf;

                    // A4 dimensions in mm at 72dpi: 210 × 297
                    const pdf      = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                    const pageW    = pdf.internal.pageSize.getWidth();   // 210 mm
                    const pageH    = pdf.internal.pageSize.getHeight();  // 297 mm

                    const imgData  = canvas.toDataURL('image/png');
                    const imgW     = canvas.width;
                    const imgH     = canvas.height;

                    // Scale image to fit page width; if taller than one page, add more pages
                    const ratio    = pageW / imgW;
                    const scaledH  = imgH * ratio;                       // mm

                    if (scaledH <= pageH) {
                        // Fits on one page
                        pdf.addImage(imgData, 'PNG', 0, 0, pageW, scaledH);
                    } else {
                        // Multi-page: slice the canvas into page-height chunks
                        const pxPerPage = Math.floor(pageH / ratio);    // canvas px per page
                        let   yOffset   = 0;

                        while (yOffset < imgH) {
                            const sliceH = Math.min(pxPerPage, imgH - yOffset);

                            // Draw the slice onto a temp canvas
                            const slice = document.createElement('canvas');
                            slice.width  = imgW;
                            slice.height = sliceH;
                            slice.getContext('2d').drawImage(canvas, 0, yOffset, imgW, sliceH, 0, 0, imgW, sliceH);

                            const sliceData = slice.toDataURL('image/png');
                            const sliceMmH  = sliceH * ratio;

                            if (yOffset > 0) pdf.addPage();
                            pdf.addImage(sliceData, 'PNG', 0, 0, pageW, sliceMmH);

                            yOffset += pxPerPage;
                        }
                    }

                    const pad2    = n => String(n).padStart(2, '0');
                    const fname   = 'payment_report_' + (studentObj ? studentObj.student_id_number : studentId)
                                  + '_' + now.getFullYear() + pad2(now.getMonth() + 1) + pad2(now.getDate()) + '.pdf';
                    pdf.save(fname);

                    Swal.close();
                    Toastify({ text: 'Report downloaded', style: { background: '#10b981' } }).showToast();

                } catch (err) {
                    console.error('Canvas export error:', err);
                    Swal.fire('Error', 'Could not generate the report. Please try again.', 'error');
                }
            });


        });

        // --- Filter Logic ---
        function setupFilterListeners() {
            const populateOptions = (selectId, options, placeholder) => {
                const select = document.getElementById(selectId);
                select.innerHTML = `<option value="">${placeholder}</option>`;
                options.forEach(opt => {
                    if (opt) select.innerHTML += `<option value="${opt}">${opt}</option>`;
                });
            };

            // Initialize: Show all classes from the database (not just from enrolled students)
            populateOptions('classFilter', allClasses, 'All Classes');

            // Class change handler - cascades to group
            document.getElementById('classFilter').addEventListener('change', function() {
                const selectedClass = this.value;
                
                // Reset downstream filters
                document.getElementById('groupFilter').innerHTML = '<option value="">All Groups</option>';
                document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
                document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';

                if (selectedClass) {
                    // Filter students by selected class
                    const filteredStudents = allStudents.filter(s => s.class_name === selectedClass);
                    const groups = [...new Set(filteredStudents.map(s => s.group_name))].filter(Boolean);
                    populateOptions('groupFilter', groups, 'All Groups');
                }
            });

            // Group change handler - cascades to section
            document.getElementById('groupFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = this.value;
                
                // Reset downstream filters
                document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
                document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';

                if (selectedClass && selectedGroup) {
                    // Filter students by selected class and group
                    const filteredStudents = allStudents.filter(s => 
                        s.class_name === selectedClass && s.group_name === selectedGroup
                    );
                    const sections = [...new Set(filteredStudents.map(s => s.section_name))].filter(Boolean);
                    populateOptions('sectionFilter', sections, 'All Sections');
                }
            });

            // Section change handler - cascades to session
            document.getElementById('sectionFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = document.getElementById('groupFilter').value;
                const selectedSection = this.value;
                
                // Reset downstream filters
                document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';
                document.getElementById('studentFilter').innerHTML = '<option value="">All Students</option>';

                if (selectedClass && selectedGroup && selectedSection) {
                    // Filter students by selected class, group, and section
                    const filteredStudents = allStudents.filter(s => 
                        s.class_name === selectedClass && 
                        s.group_name === selectedGroup && 
                        s.section_name === selectedSection
                    );
                    const sessions = [...new Set(filteredStudents.map(s => s.session_year))].filter(Boolean);
                    populateOptions('sessionFilter', sessions, 'All Sessions');
                }
            });

            // Session change handler - cascades to student
            document.getElementById('sessionFilter').addEventListener('change', function() {
                const selectedClass = document.getElementById('classFilter').value;
                const selectedGroup = document.getElementById('groupFilter').value;
                const selectedSection = document.getElementById('sectionFilter').value;
                const selectedSession = this.value;
                
                // Reset downstream filters
                document.getElementById('studentFilter').innerHTML = '<option value="">All Students</option>';

                if (selectedClass && selectedGroup && selectedSection && selectedSession) {
                    // Filter students by selected class, group, section, and session
                    const filteredStudents = allStudents.filter(s => 
                        s.class_name === selectedClass && 
                        s.group_name === selectedGroup && 
                        s.section_name === selectedSection &&
                        s.session_year === selectedSession
                    );
                    // Sort students by name alphabetically
                    const sortedStudents = filteredStudents.sort((a, b) => 
                        a.student_name.localeCompare(b.student_name)
                    );
                    // Populate student dropdown with ID and Name
                    const studentSelect = document.getElementById('studentFilter');
                    studentSelect.innerHTML = '<option value="">All Students</option>';
                    sortedStudents.forEach(student => {
                        studentSelect.innerHTML += `<option value="${student.id}">${student.student_id_number} - ${student.student_name}</option>`;
                    });
                }
            });

            document.getElementById('applyFilter').onclick = () => {
                updateActiveFilters();
                fetchPayments(1);
                document.getElementById('filterModal').classList.add('hidden');
            };

            document.getElementById('resetFilter').onclick = () => {
                document.getElementById('classFilter').value = "";
                document.getElementById('groupFilter').innerHTML = '<option value="">All Groups</option>';
                document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
                document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';
                document.getElementById('studentFilter').innerHTML = '<option value="">All Students</option>';
                updateActiveFilters();
                fetchPayments(1);
                document.getElementById('filterModal').classList.add('hidden');
            };
        }

        function updateActiveFilters() {
            activeFilters.class = document.getElementById('classFilter').value;
            activeFilters.group = document.getElementById('groupFilter').value;
            activeFilters.section = document.getElementById('sectionFilter').value;
            activeFilters.session = document.getElementById('sessionFilter').value;
            activeFilters.student = document.getElementById('studentFilter').value;
        }

        // --- Modal Logic ---
        async function openPaymentModal() {
            document.getElementById('paymentForm').reset();
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = "Collect Fee";
            document.getElementById('fees_type').innerHTML = '<option value="">Select Fees Type</option>';
            document.getElementById('feeMonthGridWrap').classList.add('hidden');
            document.getElementById('feePaymentSection').classList.add('hidden');
            document.getElementById('feeStudentInfo').classList.add('hidden');
            document.getElementById('for_month').value = '';
            document.getElementById('is_advance').value = '0';
            editId = null;
            await loadStudents();
            feePopulateCascades();
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            editId = null;
            document.getElementById('paymentForm').reset();
        }

        // --- Data Loading ---
        async function loadStudents() {
            try {
                // Request all students without pagination for filter dropdowns
                const res = await axios.get('/api/school/students?all=true');
                console.log('Raw response:', res.data);
                allStudents = Array.isArray(res.data) ? res.data : res.data.data;
                console.log('allStudents count:', allStudents.length);
                console.log('Sample student data:', allStudents.slice(0, 3));
                
                // Check class names
                const classList = [...new Set(allStudents.map(s => s.class_name))].filter(Boolean);
                console.log('Unique classes found in students:', classList);
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

        async function findStudentByCascade(student) {
            document.getElementById('feeStudentInfo').classList.remove('hidden');
            document.getElementById('admission_student_id').value = student.id;
            document.getElementById('feeDispName').textContent = student.student_name;
            document.getElementById('feeDispClass').textContent = student.class_name || 'N/A';
            document.getElementById('feeDispGroup').textContent = student.group_name || 'N/A';
            document.getElementById('feeDispSection').textContent = student.section_name || 'N/A';

            const classId = student.school_class?.id || student.class;
            const sessionId = student.school_session?.id || student.session;

            if (classId && sessionId) {
                await loadFilteredFees(classId, sessionId);
                await loadAndRenderMonthGrid(student.id);
            }
        }

        async function loadAndRenderMonthGrid(studentId) {
            const wrap = document.getElementById('feeMonthGridWrap');
            const grid = document.getElementById('feeMonthGrid');
            const statusEl = document.getElementById('feeClearanceStatus');

            try {
                const res = await axios.get(`/api/students/${studentId}/payment-months`);
                const data = res.data;
                wrap.classList.remove('hidden');
                grid.innerHTML = '';



                data.months.forEach(m => {
                    const cell = document.createElement('div');
                    cell.className = 'relative border text-center px-1 py-2 cursor-pointer transition-all text-[10px] leading-tight select-none';

                    // Status-based styling
                    switch (m.status) {
                        case 'paid':    cell.className += ' bg-green-100 border-green-300 text-green-800'; break;
                        case 'partial': cell.className += ' bg-blue-50 border-blue-300 text-blue-700'; break;
                        case 'overdue': cell.className += ' bg-red-100 border-red-300 text-red-800 font-bold'; break;
                        case 'current': cell.className += ' bg-yellow-50 border-yellow-400 text-yellow-800 font-bold ring-2 ring-yellow-400'; break;
                        case 'advance': cell.className += ' bg-teal-100 border-teal-300 text-teal-800'; break;
                        case 'locked':  cell.className += ' bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'; break;
                        case 'no_fees': cell.className += ' bg-white border-gray-200 text-gray-400'; break;
                        default:        cell.className += ' bg-white border-gray-200 text-gray-600';
                    }

                    const shortName = m.month_name.substring(0, 3);
                    const amountText = m.status === 'locked' || m.status === 'no_fees' ? '' : `${m.due > 0 ? '৳' + m.due : '✓'}`;

                    cell.innerHTML = `<div class="font-semibold">${shortName}</div><div class="text-[9px]">${amountText}</div>`;

                    // Click handler
                    cell.addEventListener('click', function() {
                        if (m.status === 'locked') {
                            Swal.fire('Locked', 'Advance payment not available until dues are cleared.', 'info');
                            return;
                        }
                        if (m.status === 'no_fees') {
                            Swal.fire('No Fees', 'No fee configuration found for this month.', 'info');
                            return;
                        }
                        if (m.status === 'paid') {
                            Swal.fire('Already Paid', 'This month has been fully paid.', 'info');
                            return;
                        }

                        document.getElementById('for_month').value = m.year_month;
                        document.getElementById('is_advance').value = m.is_future ? '1' : '0';
                        document.getElementById('feePaymentSection').classList.remove('hidden');

                        // For advance payments, restrict fee types to Food & Tuition
                        if (m.is_future) {
                            const feeTypeSelect = document.getElementById('fees_type');
                            const currentVal = feeTypeSelect.value;
                            Array.from(feeTypeSelect.options).forEach(opt => {
                                opt.hidden = opt.value && !['Food', 'Tuition'].includes(opt.value);
                            });
                            if (!['Food', 'Tuition'].includes(currentVal)) {
                                feeTypeSelect.value = '';
                                document.getElementById('fee_name').innerHTML = '<option value="">Select Fee Name</option>';
                                document.getElementById('total_payable').value = '';
                                document.getElementById('payable_due').value = '';
                                document.getElementById('payable_due').removeAttribute('data-original-due');
                            }
                        } else {
                            document.getElementById('fees_type').querySelectorAll('option').forEach(opt => opt.hidden = false);
                        }

                        // Highlight this month
                        grid.querySelectorAll('.ring-2').forEach(el => el.classList.remove('ring-2'));
                        this.classList.add('ring-2', 'ring-blue-500');
                    });

                    grid.appendChild(cell);
                });

                if (data.has_due) {
                    statusEl.className = 'text-[9px] font-medium text-red-500';
                    statusEl.textContent = '⚠ Due pending';
                } else {
                    statusEl.className = 'text-[9px] font-medium text-green-600';
                    statusEl.textContent = '✓ All cleared';
                }
                statusEl.classList.remove('hidden');

                // Auto-select current month
                const currentMonthData = data.months.find(m => m.is_current);
                if (currentMonthData) {
                    const cells = grid.children;
                    for (let i = 0; i < data.months.length; i++) {
                        if (data.months[i].year_month === currentMonthData.year_month) {
                            cells[i]?.click();
                            break;
                        }
                    }
                }

            } catch (e) {
                console.error('Month grid load error:', e);
                wrap.classList.add('hidden');
            }
        }

        // Helper to populate a select from string array
        function feePopulate(selectId, options, placeholder) {
            const sel = document.getElementById(selectId);
            sel.innerHTML = `<option value="">${placeholder}</option>`;
            options.forEach(o => { if (o) sel.innerHTML += `<option value="${o}">${o}</option>`; });
        }

        function feePopulateCascades() {
            feePopulate('feeClassFilter', allClasses, 'All');
            feePopulate('feeGroupFilter', [], 'All');
            feePopulate('feeSectionFilter', [], 'All');
            feePopulate('feeSessionFilter', [], 'All');
            feePopulate('feeStudentFilter', [], '— Select Student —');
        }

        let currentFeesList = [];
        async function loadFilteredFees(classId, sessionId) {
            try {
                const res = await axios.get('/api/fee-templates', {
                    params: {
                        class_id: classId,
                        session_id: sessionId,
                        all: true
                    }
                });
                const fees = Array.isArray(res.data) ? res.data : res.data.data;
                currentFeesList = fees;

                const typeSelect = document.getElementById('fees_type');
                typeSelect.innerHTML = '<option value="">Select Fees Type</option>';

                const filteredTypes = [...new Set(fees.map(f => f.fee_type_name))];

                if (filteredTypes.length > 0) {
                    filteredTypes.forEach(type => {
                        typeSelect.innerHTML += `<option value="${type}">${type}</option>`;
                    });
                }

            } catch (e) {
                console.error("Fee loading failed", e);
            }
        }

        document.getElementById('fees_type').addEventListener('change', function() {
            const selectedType = this.value;
            const nameSelect = document.getElementById('fee_name');
            nameSelect.innerHTML = '<option value="">Select Fee Name</option>';

            // Clear total payable and due when fee type changes
            document.getElementById('total_payable').value = '';
            document.getElementById('payable_due').value = '';
            document.getElementById('payable_due').removeAttribute('data-original-due');

            // currentFeesList is already filtered by class+session from the API, so just filter by type
            const seenNames = new Set();
            currentFeesList
                .filter(f => f.fee_type_name === selectedType)
                .forEach(fee => {
                    if (!seenNames.has(fee.fee_name)) {
                        nameSelect.innerHTML +=
                            `<option value="${fee.fee_name}" data-amount="${fee.amount}">${fee.fee_name}</option>`;
                        seenNames.add(fee.fee_name);
                    }
                });
        });

        async function fetchTotalPayable() {
            const feeName = document.getElementById('fee_name').value;
            const feesType = document.getElementById('fees_type').value;
            const admissionId = document.getElementById('admission_student_id').value;

            // Only proceed if all necessary fields are present
            if (!feeName || !feesType || !admissionId) return;

            try {
                const response = await axios.get('/api/school/payments/get-total-fee', {
                    params: {
                        fees_type: feesType,
                        fee_name: feeName,
                        admission_id: admissionId
                    }
                });

                const data = response.data;

                const totalField = document.getElementById('total_payable');
                totalField.value = data.total_payable;
                totalField.style.color = data.has_discount ? '#10b981' : 'inherit';

                const payableDueField = document.getElementById('payable_due');
                payableDueField.value = data.remaining_due;
                payableDueField.setAttribute('data-original-due', data.remaining_due);

                calculateDue();

            } catch (error) {
                console.error("Fee Fetch Error:", error);
                Swal.fire({
                    title: 'Error',
                    text: 'Could not retrieve the fee amount. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
            }
        }

        document.getElementById('fee_name').addEventListener('change', function() {
            fetchTotalPayable();
        });


        // Realtime calculation
        document.getElementById('type_amount').addEventListener('input', function () {

            const inputField = this;

            // Original due (remaining amount the student can pay)
            const originalDue = parseFloat(
                document.getElementById('payable_due').getAttribute('data-original-due')
            );

            // If no fee is selected yet, do nothing
            if (isNaN(originalDue)) return;

            // Typed amount
            let typedAmount = parseFloat(inputField.value) || 0;

            // Prevent entering more than due
            if (typedAmount > originalDue) {

                typedAmount = originalDue;

                inputField.value = originalDue;

                // Red border
                inputField.style.border = '2px solid #ef4444';

            } else {

                // Green border
                inputField.style.border = '2px solid #10b981';
            }

            calculateDue();
        });


        function calculateDue() {

            // Original due amount
            const originalDue = parseFloat(
                document.getElementById('payable_due').getAttribute('data-original-due')
            ) || 0;

            // Typed amount
            const typedAmount = parseFloat(
                document.getElementById('type_amount').value
            ) || 0;

            // Calculate due
            let due = originalDue - typedAmount;

            // Prevent negative
            if (due < 0) {
                due = 0;
            }

            // Update field
            document.getElementById('payable_due').value = due.toFixed(2);

            // Status
            let status = 'unpaid';

            if (typedAmount > 0 && due > 0) {
                status = 'partial';
            } else if (due <= 0 && originalDue > 0) {
                status = 'paid';
            }

            return status;
        }

        // --- CRUD Operations ---
        async function fetchPayments(page = 1) {
            currentPage = page;
            const searchInput = document.getElementById('paySearch');
            const search = searchInput ? searchInput.value : '';

            try {
                const res = await axios.get(`/api/school/payments?page=${page}&search=${search}`);
                const tbody = document.getElementById('paymentTableBody');
                tbody.innerHTML = '';

                // --- Client Side Filtering Logic ---
                const rawData = res.data.data;
                const filteredData = rawData.filter(p => {
                    const className = p.student?.school_class?.class_name || 'N/A';
                    const groupName = p.student?.school_group?.group_name || 'N/A';
                    const sectionName = p.student?.school_section?.section_name || 'N/A';
                    const sessionYear = p.student?.school_session?.session_year || 'N/A';
                    const studentId = p.student?.id;

                    return (!activeFilters.class || className === activeFilters.class) &&
                        (!activeFilters.group || groupName === activeFilters.group) &&
                        (!activeFilters.section || sectionName === activeFilters.section) &&
                        (!activeFilters.session || sessionYear === activeFilters.session) &&
                        (!activeFilters.student || studentId == activeFilters.student);
                });

                filteredData.forEach((p, i) => {
                    const payDate = new Date(p.pay_date);
                    const formattedDate =
                        `${payDate.getDate().toString().padStart(2,'0')}/${(payDate.getMonth()+1).toString().padStart(2,'0')}/${payDate.getFullYear()}`;

                    const className = p.student?.school_class?.class_name || 'N/A';
                    const groupName = p.student?.school_group?.group_name || 'N/A';
                    const sectionName = p.student?.school_section?.section_name || 'N/A';
                    const sessionYear = p.student?.school_session?.session_year || 'N/A';

                    const due = parseFloat(p.payable_due) || 0;
                    const dueDisplay = due > 0
                        ? `<span style="color:#ef4444;font-weight:700;">${due.toFixed(2)}</span>`
                        : `<span style="color:#10b981;font-weight:700;">0.00</span>`;

                    tbody.innerHTML += `
                    <tr>
                        <td>${res.data.from + i}</td>
                        <td>${formattedDate}</td>
                        <td>${p.pay_method}</td>
                        <td>${className}</td>
                        <td>${groupName}</td>
                        <td>${sectionName}</td>
                        <td>${sessionYear}</td>
                        <td>${p.student?.student_id_number || '---'}</td>
                        <td>${p.student?.student_name || 'Unknown'}</td>
                        <td>${p.fees_type}</td>
                        <td>${p.fee_name || '---'}</td>
                        <td>${p.total_payable}</td>
                        <td>${p.type_amount}</td>
                        <td>${dueDisplay}</td>
                        <td class="text-center no-print">
                            <div class="flex justify-center gap-3">
                                <button onclick="editPayment(${p.id})" class="action-icon-btn text-blue-500 flex items-center justify-center leading-none">
                                    <i class="far fa-edit" style="font-size: 15px; display: block;"></i></button>
                                <button onclick="deletePayment(${p.id})" class="action-icon-btn text-red-400 flex items-center justify-center leading-none">
                                    <i class="far fa-trash-alt" style="font-size: 15px; display: block;"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });

                document.getElementById('paginationInfo').innerText = `${res.data.to || 0} of ${res.data.total || 0}`;
                renderPagination(res.data);
            } catch (e) {
                console.error("Fetch error", e);
            }
        }

        function renderPagination(data) {
            const wrap = document.getElementById('paginationControls');
            wrap.innerHTML = '';
            if (!data.links) return;
            data.links.forEach(link => {
                const activeClass = link.active ? 'active' : '';
                const disabled = !link.url ? 'disabled' : '';
                let label = link.label;
                if (label.includes('Previous')) label = '<i class="mdi mdi-chevron-left"></i>';
                else if (label.includes('Next')) label = '<i class="mdi mdi-chevron-right"></i>';

                const pageNum = link.url ? new URL(link.url).searchParams.get('page') : null;
                const btn = document.createElement('button');
                btn.className = `page-link-btn ${activeClass}`;
                btn.innerHTML = label;
                if (disabled) btn.disabled = true;
                if (pageNum) btn.onclick = () => fetchPayments(pageNum);
                wrap.appendChild(btn);
            });
        }

        document.getElementById('paymentForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveBtn');
            const spinner = document.getElementById('saveBtnSpinner');
            const btnText = document.getElementById('saveBtnText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Saving...';

            const studentId = document.getElementById('admission_student_id').value;
            const feesType = document.getElementById('fees_type').value;
            const feeName = document.getElementById('fee_name').value;
            const amount = document.getElementById('type_amount').value;
            const payDate = document.getElementById('pay_date').value;
            const payMethod = document.getElementById('pay_method').value;
            const isAdvance = document.getElementById('is_advance').value === '1';
            const forMonth = document.getElementById('for_month').value;

            try {
                if (editId) {
                    const data = {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        total_payable: document.getElementById('total_payable').value,
                        type_amount: amount,
                        payable_due: document.getElementById('payable_due').value,
                        pay_date: payDate,
                        pay_method: payMethod,
                        status: calculateDue()
                    };
                    await axios.put(`/api/school/payments/${editId}`, data);
                } else if (isAdvance) {
                    await axios.post('/api/school/payments/store-advance', {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        amount: amount,
                        for_month: forMonth,
                        pay_date: payDate,
                        pay_method: payMethod,
                    });
                } else {
                    const data = {
                        admission_student_id: studentId,
                        fees_type: feesType,
                        fee_name: feeName,
                        total_payable: document.getElementById('total_payable').value,
                        type_amount: amount,
                        payable_due: document.getElementById('payable_due').value,
                        pay_date: payDate,
                        pay_method: payMethod,
                        for_month: forMonth,
                        status: calculateDue()
                    };
                    await axios.post('/api/school/payments', data);
                }

                const student = allStudents.find(s => s.id == studentId);
                if (student) {
                    document.getElementById('classFilter').value = student.class_name;
                    document.getElementById('groupFilter').value = student.group_name;
                    document.getElementById('sectionFilter').value = student.section_name;
                    document.getElementById('sessionFilter').value = student.session_year;
                    updateActiveFilters();
                }

                closeModal();
                fetchPayments(currentPage);
                Toastify({
                    text: editId ? "Record Updated" : isAdvance ? "Advance Payment Recorded" : "Payment Recorded",
                    style: { background: "#10b981" }
                }).showToast();
            } catch (err) {
                const msg = err.response?.data?.message || 'Check student/fee selection';
                Swal.fire('Error', msg, 'error');
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
                btnText.textContent = 'Save';
            }
        };

        async function editPayment(id) {
            try {
                const res = await axios.get(`/api/school/payments/${id}`);
                const p = res.data;
                editId = id;
                document.getElementById('paymentModal').classList.remove('hidden');
                document.getElementById('modalTitle').innerText = "Update Payment Record";
                document.getElementById('is_advance').value = '0';
                await loadStudents();

                // Hide cascade/grid sections, show payment section only
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.remove('hidden');

                // Show student info
                document.getElementById('feeStudentInfo').classList.remove('hidden');
                document.getElementById('admission_student_id').value = p.admission_student_id;
                document.getElementById('feeDispName').textContent = p.student?.student_name || '';
                document.getElementById('feeDispClass').textContent = p.student?.school_class?.class_name || 'N/A';
                document.getElementById('feeDispGroup').textContent = p.student?.school_group?.group_name || 'N/A';
                document.getElementById('feeDispSection').textContent = p.student?.school_section?.section_name || 'N/A';

                const cId = p.student?.school_class?.id || p.student?.class;
                const sId = p.student?.school_session?.id || p.student?.session;
                if (cId && sId) await loadFilteredFees(cId, sId);
                document.getElementById('fees_type').value = p.fees_type;
                document.getElementById('fees_type').dispatchEvent(new Event('change'));
                document.getElementById('fee_name').value = p.fee_name || '';
                document.getElementById('total_payable').value = p.total_payable;
                document.getElementById('type_amount').value = p.type_amount;
                document.getElementById('payable_due').value = p.payable_due;
                document.getElementById('payable_due').setAttribute('data-original-due', parseFloat(p.payable_due) + parseFloat(p.type_amount));
                document.getElementById('pay_method').value = p.pay_method;
                document.getElementById('pay_date').value = p.pay_date;
            } catch (error) {
                Swal.fire('Error', 'Failed to fetch details.', 'error');
            }
        }

        async function deletePayment(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "Delete this?",
                icon: 'warning',
                showCancelButton: true
            });
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/school/payments/${id}`);
                    fetchPayments(currentPage);
                    Toastify({
                        text: "Deleted",
                        style: {
                            background: "#ef4444"
                        }
                    }).showToast();
                } catch (err) {
                    Swal.fire('Error', 'Could not delete', 'error');
                }
            }
        }

        // Modal Toggles
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtn = document.getElementById('btnFilter');
            const filterModal = document.getElementById('filterModal');
            if (filterBtn) filterBtn.onclick = () => filterModal.classList.remove('hidden');
            window.addEventListener('click', (e) => {
                if (e.target === filterModal) filterModal.classList.add('hidden');
            });
        });

        // ==================== FEE MODAL CASCADE ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Class → Group cascade
            document.getElementById('feeClassFilter').addEventListener('change', function() {
                const cls = this.value;
                feePopulate('feeGroupFilter', [], 'All');
                feePopulate('feeSectionFilter', [], 'All');
                feePopulate('feeSessionFilter', [], 'All');
                feePopulate('feeStudentFilter', [], '— Select Student —');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls) return;
                const groups = [...new Set(allStudents.filter(s => s.class_name === cls).map(s => s.group_name))].filter(Boolean);
                feePopulate('feeGroupFilter', groups, 'All');
            });

            // Group → Section cascade
            document.getElementById('feeGroupFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = this.value;
                feePopulate('feeSectionFilter', [], 'All');
                feePopulate('feeSessionFilter', [], 'All');
                feePopulate('feeStudentFilter', [], '— Select Student —');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls || !grp) return;
                const sections = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name))].filter(Boolean);
                feePopulate('feeSectionFilter', sections, 'All');
            });

            // Section → Session cascade
            document.getElementById('feeSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = document.getElementById('feeGroupFilter').value;
                const sec = this.value;
                feePopulate('feeSessionFilter', [], 'All');
                feePopulate('feeStudentFilter', [], '— Select Student —');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(allStudents.filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec).map(s => s.session_year))].filter(Boolean);
                feePopulate('feeSessionFilter', sessions, 'All');
            });

            // Session → Student cascade
            document.getElementById('feeSessionFilter').addEventListener('change', function() {
                const cls = document.getElementById('feeClassFilter').value;
                const grp = document.getElementById('feeGroupFilter').value;
                const sec = document.getElementById('feeSectionFilter').value;
                const ses = this.value;
                feePopulate('feeStudentFilter', [], '— Select Student —');
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!ses) return;
                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name === cls) &&
                    (!grp || s.group_name === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const sel = document.getElementById('feeStudentFilter');
                sel.innerHTML = '<option value="">— Select Student —</option>';
                filtered.forEach(s => {
                    sel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
            });

            // Student select → load month grid + info
            document.getElementById('feeStudentFilter').addEventListener('change', function() {
                const id = this.value;
                document.getElementById('feeStudentInfo').classList.add('hidden');
                document.getElementById('feeMonthGridWrap').classList.add('hidden');
                document.getElementById('feePaymentSection').classList.add('hidden');
                if (!id) return;
                const student = allStudents.find(s => s.id == id);
                if (student) findStudentByCascade(student);
            });

            // Student ID quick search
            document.getElementById('feeStudentIdSearch').addEventListener('input', function() {
                const sid = this.value.trim();
                const errEl = document.getElementById('feeIdNotFound');
                if (!sid) { errEl.classList.add('hidden'); return; }

                const student = allStudents.find(s => String(s.student_id_number) === sid);
                if (!student) {
                    errEl.classList.remove('hidden');
                    feePopulateCascades();
                    document.getElementById('feeStudentInfo').classList.add('hidden');
                    document.getElementById('feeMonthGridWrap').classList.add('hidden');
                    document.getElementById('feePaymentSection').classList.add('hidden');
                    return;
                }

                errEl.classList.add('hidden');

                // Fill cascades
                feePopulate('feeClassFilter', allClasses, 'All');
                document.getElementById('feeClassFilter').value = student.class_name;

                const groups = [...new Set(allStudents.filter(s => s.class_name === student.class_name).map(s => s.group_name))].filter(Boolean);
                feePopulate('feeGroupFilter', groups, 'All');
                document.getElementById('feeGroupFilter').value = student.group_name;

                const sections = [...new Set(allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name).map(s => s.section_name))].filter(Boolean);
                feePopulate('feeSectionFilter', sections, 'All');
                document.getElementById('feeSectionFilter').value = student.section_name;

                const sessions = [...new Set(allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name && s.section_name === student.section_name).map(s => s.session_year))].filter(Boolean);
                feePopulate('feeSessionFilter', sessions, 'All');
                document.getElementById('feeSessionFilter').value = student.session_year;

                // Fill student dropdown and select
                const peers = allStudents.filter(s =>
                    s.class_name === student.class_name && s.group_name === student.group_name &&
                    s.section_name === student.section_name && s.session_year === student.session_year
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const studentSel = document.getElementById('feeStudentFilter');
                studentSel.innerHTML = '<option value="">— Select Student —</option>';
                peers.forEach(s => { studentSel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`; });
                studentSel.value = student.id;

                findStudentByCascade(student);
            });
        });

        // ==================== PAYMENT SLIP MODAL ====================
        (function() {
            const slipModal = document.getElementById('slipModal');
            const closeSlipModal = () => slipModal.classList.add('hidden');

            document.getElementById('closeSlip').addEventListener('click', closeSlipModal);
            document.getElementById('closeSlipFooter').addEventListener('click', closeSlipModal);

            function slipPopulate(selectId, options, placeholder) {
                const sel = document.getElementById(selectId);
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                options.forEach(o => { if (o) sel.innerHTML += `<option value="${o}">${o}</option>`; });
            }

            // Open modal
            document.getElementById('btnPaymentSlip').addEventListener('click', function() {
                slipPopulate('slipClassFilter',   allClasses, 'All Classes');
                slipPopulate('slipGroupFilter',   [], 'All Groups');
                slipPopulate('slipSectionFilter', [], 'All Sections');
                slipPopulate('slipSessionFilter', [], 'All Sessions');
                slipPopulate('slipStudentFilter', [], '— Select Student —');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                document.getElementById('slipStudentError').classList.add('hidden');
                document.getElementById('slipIdNotFound').classList.add('hidden');
                document.getElementById('slipStudentIdSearch').value = '';
                slipModal.classList.remove('hidden');
            });

            // Class → Group cascade
            document.getElementById('slipClassFilter').addEventListener('change', function() {
                const cls = this.value;
                slipPopulate('slipGroupFilter',   [], 'All Groups');
                slipPopulate('slipSectionFilter', [], 'All Sections');
                slipPopulate('slipSessionFilter', [], 'All Sessions');
                slipPopulate('slipStudentFilter', [], '— Select Student —');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls) return;
                const groups = [...new Set(
                    allStudents.filter(s => s.class_name === cls).map(s => s.group_name)
                )].filter(Boolean);
                slipPopulate('slipGroupFilter', groups, 'All Groups');
            });

            // Group → Section cascade
            document.getElementById('slipGroupFilter').addEventListener('change', function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = this.value;
                slipPopulate('slipSectionFilter', [], 'All Sections');
                slipPopulate('slipSessionFilter', [], 'All Sessions');
                slipPopulate('slipStudentFilter', [], '— Select Student —');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls || !grp) return;
                const sections = [...new Set(
                    allStudents.filter(s => s.class_name === cls && s.group_name === grp).map(s => s.section_name)
                )].filter(Boolean);
                slipPopulate('slipSectionFilter', sections, 'All Sections');
            });

            // Section → Session cascade
            document.getElementById('slipSectionFilter').addEventListener('change', function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = document.getElementById('slipGroupFilter').value;
                const sec = this.value;
                slipPopulate('slipSessionFilter', [], 'All Sessions');
                slipPopulate('slipStudentFilter', [], '— Select Student —');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!cls || !grp || !sec) return;
                const sessions = [...new Set(
                    allStudents
                        .filter(s => s.class_name === cls && s.group_name === grp && s.section_name === sec)
                        .map(s => s.session_year)
                )].filter(Boolean);
                slipPopulate('slipSessionFilter', sessions, 'All Sessions');
            });

            // Session → auto-fill dates + Student cascade
            document.getElementById('slipSessionFilter').addEventListener('change', async function() {
                const cls = document.getElementById('slipClassFilter').value;
                const grp = document.getElementById('slipGroupFilter').value;
                const sec = document.getElementById('slipSectionFilter').value;
                const ses = this.value;
                slipPopulate('slipStudentFilter', [], '— Select Student —');
                document.getElementById('slipFromDate').value = '';
                document.getElementById('slipToDate').value = '';
                if (!ses) return;

                // Auto-fill dates from session
                try {
                    const matchedStudent = allStudents.find(s => s.session_year === ses &&
                        (!cls || s.class_name === cls) &&
                        (!grp || s.group_name === grp) &&
                        (!sec || s.section_name === sec));
                    const sessionId = matchedStudent?.session
                        || matchedStudent?.school_session?.id
                        || matchedStudent?.session_id;
                    if (sessionId) {
                        const res = await axios.get(`/api/school-sessions/${sessionId}/dates`);
                        if (res.data.start_date) document.getElementById('slipFromDate').value = res.data.start_date;
                        if (res.data.end_date) document.getElementById('slipToDate').value = res.data.end_date;
                    }
                } catch (e) {
                    console.warn('Could not fetch session dates', e);
                }

                // Populate students
                const filtered = allStudents.filter(s =>
                    (!cls || s.class_name === cls) &&
                    (!grp || s.group_name === grp) &&
                    (!sec || s.section_name === sec) &&
                    s.session_year === ses
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const sel = document.getElementById('slipStudentFilter');
                sel.innerHTML = '<option value="">— Select Student —</option>';
                filtered.forEach(s => {
                    sel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
            });

            // Hide error on student select
            document.getElementById('slipStudentFilter').addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('slipStudentError').classList.add('hidden');
                }
            });

            // Student ID quick search — auto-fills all cascades and selects the student
            document.getElementById('slipStudentIdSearch').addEventListener('input', function() {
                const sid = this.value.trim();
                const errEl = document.getElementById('slipIdNotFound');

                if (!sid) {
                    errEl.classList.add('hidden');
                    return;
                }

                const student = allStudents.find(s => String(s.student_id_number) === sid);

                if (!student) {
                    errEl.classList.remove('hidden');
                    slipPopulate('slipClassFilter',   allClasses, 'All Classes');
                    slipPopulate('slipGroupFilter',   [], 'All Groups');
                    slipPopulate('slipSectionFilter', [], 'All Sections');
                    slipPopulate('slipSessionFilter', [], 'All Sessions');
                    slipPopulate('slipStudentFilter', [], '— Select Student —');
                    document.getElementById('slipFromDate').value = '';
                    document.getElementById('slipToDate').value = '';
                    return;
                }

                errEl.classList.add('hidden');

                // Fill Class
                slipPopulate('slipClassFilter', allClasses, 'All Classes');
                document.getElementById('slipClassFilter').value = student.class_name;

                // Fill Group
                const groups = [...new Set(
                    allStudents.filter(s => s.class_name === student.class_name).map(s => s.group_name)
                )].filter(Boolean);
                slipPopulate('slipGroupFilter', groups, 'All Groups');
                document.getElementById('slipGroupFilter').value = student.group_name;

                // Fill Section
                const sections = [...new Set(
                    allStudents.filter(s => s.class_name === student.class_name && s.group_name === student.group_name).map(s => s.section_name)
                )].filter(Boolean);
                slipPopulate('slipSectionFilter', sections, 'All Sections');
                document.getElementById('slipSectionFilter').value = student.section_name;

                // Fill Session
                const sessions = [...new Set(
                    allStudents.filter(s =>
                        s.class_name   === student.class_name &&
                        s.group_name   === student.group_name &&
                        s.section_name === student.section_name
                    ).map(s => s.session_year)
                )].filter(Boolean);
                slipPopulate('slipSessionFilter', sessions, 'All Sessions');
                document.getElementById('slipSessionFilter').value = student.session_year;

                // Auto-fill dates from session
                (async () => {
                    const sessionId = student.session || student?.school_session?.id || student?.session_id;
                    if (sessionId) {
                        try {
                            const res = await axios.get(`/api/school-sessions/${sessionId}/dates`);
                            if (res.data.start_date) document.getElementById('slipFromDate').value = res.data.start_date;
                            if (res.data.end_date) document.getElementById('slipToDate').value = res.data.end_date;
                        } catch (e) { console.warn('Could not fetch session dates', e); }
                    }
                })();

                // Fill Student dropdown and select this student
                const peers = allStudents.filter(s =>
                    s.class_name   === student.class_name &&
                    s.group_name   === student.group_name &&
                    s.section_name === student.section_name &&
                    s.session_year === student.session_year
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));

                const studentSel = document.getElementById('slipStudentFilter');
                studentSel.innerHTML = '<option value="">— Select Student —</option>';
                peers.forEach(s => {
                    studentSel.innerHTML += `<option value="${s.id}">${s.student_id_number} — ${s.student_name}</option>`;
                });
                studentSel.value = student.id;
            });

            // Show Payment Slip page
            document.getElementById('slipShowPage').addEventListener('click', function() {
                const studentSel = document.getElementById('slipStudentFilter');
                if (!studentSel.value) {
                    document.getElementById('slipStudentError').classList.remove('hidden');
                    studentSel.focus();
                    return;
                }
                closeSlipModal();
                const params = new URLSearchParams();
                params.append('student_id', studentSel.value);
                const fromDate = document.getElementById('slipFromDate').value;
                const toDate = document.getElementById('slipToDate').value;
                if (fromDate) params.append('from_date', fromDate);
                if (toDate) params.append('to_date', toDate);
                window.location.href = `/school/payment/slip?${params.toString()}`;
            });

            // Close on outside click
            window.addEventListener('click', function(e) {
                if (e.target === slipModal) closeSlipModal();
            });
        })();
    </script>
@endsection