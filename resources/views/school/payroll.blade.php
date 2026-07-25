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
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
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
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            display: block;
            background: white;
        }


        /* --- Custom Horizontal Scrollbar Style (Guardian Style) --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: white;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* .table-responsive::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                } */


        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 8px;
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 2px solid #edf2f7;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            text-align: left;
        }

        td {
            padding: 8px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all .2s;
            border-radius: 0;
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
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1;
            padding: .6rem .75rem;
            border-radius: 0;
            font-size: .85rem;
            outline: none;
            transition: border 0.2s;
        }

        .form-input-fixed:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .form-input-readonly {
            background: #f8fafc !important;
            cursor: not-allowed;
            color: #64748b;
        }

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #edf2f7;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 12px;
            cursor: pointer;
        }

        .pagination-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        /* Modal Polish */
        .modal-content-sharp {
            border-radius: 0 !important;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            width: 95%;
            margin: auto;
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
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        {{-- Dynamic Page Title using the current route name / menu name --}}
                        <h2 id="pageHeader" class="text-xl font-bold text-gray-800 leading-tight"></h2>

                        <div class="flex items-center text-slate-400 text-xs mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1 text-[8px]"></i>
                            <span id="pageTitle" class="font-medium text-slate-500"></span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" id="paySearch" placeholder="Search name or month..."
                            class="flex-grow sm:flex-grow-0 pl-3 pr-3 py-2 border border-gray-200 text-xs outline-none focus:border-blue-500">
                        <button onclick="openModal()"
                            class="w-full sm:w-auto btn-outline-premium px-4 py-2 text-[10px] uppercase tracking-wider flex items-center justify-center gap-1">
                            <i class="mdi mdi-cash-check"></i> Process Payroll
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1600px]">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Employee Name</th>
                                <th>Mobile Number</th>
                                <th>Designation</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Present Days</th>
                                <th>Absent Days</th>
                                <th>Leave Days</th>
                                <th>Total Payable</th>
                                <th>Payable Due</th>
                                <th>Advance Status</th>
                                <th>Pay Type</th>
                                <th>Paid Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="payrollTableBody"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase" id="paginationInfo">Showing 0 to 0 of 0
                        entries</div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payroll Modal (Fully Synced Design) --}}
    <div id="payrollModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[95vh]">

            <div class="px-5 py-3 border-b flex justify-between items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center shadow-lg">
                        <i class="mdi mdi-cash-register text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 uppercase text-sm leading-tight">Create Payroll Record</h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Input monthly disbursement
                            details</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-black p-1 transition-colors">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form id="payrollForm" class="flex flex-col overflow-hidden m-0">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Select Employee *</label>
                            <select id="school_employee_id" class="form-input-fixed w-full" required
                                onchange="loadEmployeeData(this.value)">
                                <option value="">Choose Employee...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Designation</label>
                            <input type="text" id="display_designation" class="form-input-fixed w-full bg-gray-100 "
                                readonly placeholder="Auto-filled">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Payroll Month *</label>
                            <select id="month" class="form-input-fixed w-full" required>
                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                    <option value="{{ $m }}" {{ date('F') == $m ? 'selected' : '' }}>
                                        {{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Payroll Year *</label>
                            <input type="number" id="year" class="form-input-fixed w-full" value="{{ date('Y') }}"
                                required>
                        </div>

                        <div
                            class="col-span-1 sm:col-span-2 bg-white p-4 border border-dashed border-gray-200 rounded-sm my-1">
                            <p class="text-[9px] font-bold text-blue-600 uppercase mb-3 flex items-center gap-2">
                                <i class="mdi mdi-calendar-check"></i> Attendance Summary
                            </p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Present</label>
                                    <input type="number" id="present" class="form-input-fixed w-full text-center"
                                        value="0">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Absent</label>
                                    <input type="number" id="absent" class="form-input-fixed w-full text-center"
                                        value="0">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Leave</label>
                                    <input type="number" id="leave" class="form-input-fixed w-full text-center"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Total Salary
                                Payable</label>
                            <input type="number" id="total_payable" class="form-input-fixed w-full font-bold" required
                                placeholder="0.00">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Payable Due</label>
                            <input type="number" id="payable_due" class="form-input-fixed w-full text-red-500"
                                value="0">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Advance Taken?</label>
                            <select id="advance_status" class="form-input-fixed w-full">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Pay Method *</label>
                            <select id="pay_type" class="form-input-fixed w-full" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Mobile Banking">Mobile Banking</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[9px] font-bold text-green-600 uppercase mb-1">Net Paid Amount
                                *</label>
                            <input type="number" id="paid_amount"
                                class="form-input-fixed w-full border-green-200 bg-green-50/30 text-green-700 font-black text-lg"
                                required placeholder="0.00">
                        </div>

                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-full sm:w-auto btn-outline-secondary px-6 py-2 text-[10px] uppercase tracking-widest order-2 sm:order-1">
                        Discard
                    </button>
                    <button type="submit" id="saveBtn"
                        class="w-full sm:w-auto btn-outline-premium px-8 py-2 text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 order-1 sm:order-2">
                        <i class="mdi mdi-check-decagram-outline text-sm"></i>
                        Submit Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden Print Area --}}
    <div id="printArea" class="hidden">
        <div style="padding: 40px; border: 2px solid #000; margin: 20px; font-family: sans-serif;">
            <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 26px; text-transform: uppercase;">
                    {{ Auth::user()->school->name ?? 'School Management System' }}</h1>
                <p style="margin: 5px 0 0; font-size: 14px; font-weight: bold; letter-spacing: 2px;">OFFICIAL SALARY SLIP
                </p>
            </div>
            <table style="width: 100%; font-size: 14px; border-collapse: collapse; margin-bottom: 30px;">
                <tr>
                    <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd; width: 25%;">Employee Name:</td>
                    <td id="pr_name" style="padding: 8px; border: 1px solid #ddd;"></td>
                    <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd; width: 25%;">Period:</td>
                    <td id="pr_date" style="padding: 8px; border: 1px solid #ddd;"></td>
                </tr>
                <tr>
                    <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd;">Designation:</td>
                    <td id="pr_desig" style="padding: 8px; border: 1px solid #ddd;"></td>
                    <td style="padding: 8px; font-weight: bold; border: 1px solid #ddd;">Contact:</td>
                    <td id="pr_mobile" style="padding: 8px; border: 1px solid #ddd;"></td>
                </tr>
            </table>

            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; padding: 12px; border: 1px solid #eee;">
                    <span style="font-weight: bold;">Gross Payable Amount:</span>
                    <span id="pr_total"></span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; padding: 12px; background: #f0fff4; border: 1px solid #c6f6d5;">
                    <span style="color: #22543d; font-weight: bold;">Net Amount Paid:</span>
                    <span id="pr_paid" style="font-weight: bold; color: #22543d;"></span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; padding: 12px; border: 1px solid #fed7d7; border-top: none;">
                    <span style="color: #c53030;">Total Arrears / Due:</span>
                    <span id="pr_due" style="color: #c53030; font-weight: bold;"></span>
                </div>
            </div>

            <div style="margin-top: 80px; display: flex; justify-content: space-between; padding: 0 20px;">
                <div
                    style="border-top: 1.5px solid #000; width: 180px; text-align: center; font-size: 11px; padding-top: 5px; font-weight: bold;">
                    Authority Signature</div>
                <div
                    style="border-top: 1.5px solid #000; width: 180px; text-align: center; font-size: 11px; padding-top: 5px; font-weight: bold;">
                    Recipient Signature</div>
            </div>
            <p style="margin-top: 50px; text-align: center; font-size: 9px; color: #888;">This is a computer-generated
                document and requires no stamp for internal use.</p>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let allEmployees = [];
        let currentPayrolls = [];

        function loadEmployees() {
            axios.get('/api/employees').then(res => {
                allEmployees = res.data.data || res.data;
                const select = document.getElementById('school_employee_id');
                allEmployees.forEach(e => {
                    select.innerHTML += `<option value="${e.id}">${e.name}</option>`;
                });
            });
        }

        function loadEmployeeData(id) {
            const emp = allEmployees.find(e => e.id == id);
            if (emp) {
                document.getElementById('display_designation').value = emp.designation;
                document.getElementById('total_payable').value = emp.salary_amount;
            }
        }

        function fetchPayrolls(page = 1) {
            const search = document.getElementById('paySearch').value;
            axios.get(`/api/school-payrolls?page=${page}&search=${search}`).then(res => {
                const {
                    data: payrolls,
                    ...meta
                } = res.data;
                currentPayrolls = payrolls;
                const tbody = document.getElementById('payrollTableBody');
                tbody.innerHTML = '';
                payrolls.forEach((p, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${meta.from + index}</td>
                            <td class="font-bold text-blue-600">${p.employee_name}</td>
                            <td>${p.mobile_number}</td>
                            <td>${p.designation}</td>
                            <td>${p.month}</td>
                            <td>${p.year}</td>
                            <td class="text-center">${p.present}</td>
                            <td class="text-center">${p.absent}</td>
                            <td class="text-center">${p.leave}</td>
                            <td class="font-bold">${p.total_payable}</td>
                            <td class="text-red-500">${p.payable_due}</td>
                            <td><span class="px-2 py-0.5 rounded ${p.advance_status == 'Yes' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100'}">${p.advance_status}</span></td>
                            <td>${p.pay_type}</td>
                            <td class="font-bold text-green-600">${p.paid_amount}</td>
                            <td>
                                <div class="flex items-center gap-2 justify-center">
                                    <button onclick="printPayslip(${p.id})" class="text-blue-500 hover:scale-110 transition-transform" title="Print Slip">
                                        <i class="mdi mdi-printer text-lg"></i>
                                    </button>
                                    <button onclick="deletePayroll(${p.id})" class="text-red-400 hover:scale-110 transition-transform">
                                        <i class="mdi mdi-trash-can-outline text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });
                renderPagination(meta);
            });
        }

        function printPayslip(id) {
            const p = currentPayrolls.find(item => item.id == id);
            if (!p) return;
            document.getElementById('pr_name').innerText = p.employee_name;
            document.getElementById('pr_date').innerText = p.month + ' ' + p.year;
            document.getElementById('pr_desig').innerText = p.designation;
            document.getElementById('pr_mobile').innerText = p.mobile_number;
            document.getElementById('pr_total').innerText = p.total_payable;
            document.getElementById('pr_paid').innerText = p.paid_amount;
            document.getElementById('pr_due').innerText = p.payable_due;
            window.print();
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText =
                `Showing ${meta.from||0} to ${meta.to||0} of ${meta.total} entries`;
            controls.innerHTML = `
                <button onclick="fetchPayrolls(${meta.current_page - 1})" class="pagination-btn" ${meta.current_page === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>
                <button class="pagination-btn active">${meta.current_page}</button>
                <button onclick="fetchPayrolls(${meta.current_page + 1})" class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>
            `;
        }

        document.getElementById('payrollForm').onsubmit = function(e) {
            e.preventDefault();
            const data = {
                school_employee_id: document.getElementById('school_employee_id').value,
                month: document.getElementById('month').value,
                year: document.getElementById('year').value,
                present: document.getElementById('present').value,
                absent: document.getElementById('absent').value,
                leave: document.getElementById('leave').value,
                total_payable: document.getElementById('total_payable').value,
                payable_due: document.getElementById('payable_due').value,
                advance_status: document.getElementById('advance_status').value,
                pay_type: document.getElementById('pay_type').value,
                paid_amount: document.getElementById('paid_amount').value,
            };

            axios.post('/api/school-payrolls', data).then(() => {
                Toastify({
                    text: "Payroll ledger updated!",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                fetchPayrolls();
            }).catch(err => {
                Swal.fire('Input Error', 'Please verify all fields and try again.', 'error');
            });
        };

        function deletePayroll(id) {
            Swal.fire({
                title: 'Remove Record?',
                text: "This will permanently remove this disbursement entry.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-payrolls/${id}`).then(() => fetchPayrolls());
            });
        }

        function openModal() {
            document.getElementById('payrollModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('payrollModal').classList.add('hidden');
            document.getElementById('payrollForm').reset();
            document.getElementById('year').value = new Date().getFullYear();
        }

        document.getElementById('paySearch').addEventListener('input', () => fetchPayrolls(1));

        loadEmployees();
        fetchPayrolls();
    </script>
@endsection
