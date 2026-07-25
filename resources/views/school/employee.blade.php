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
            padding: .4rem .6rem;
            border-radius: 0;
            font-size: .8rem;
            outline: none;
        }

        .form-input-fixed:focus {
            border-color: #2563eb;
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

        .modal-content-sharp {
            border-radius: 0 !important;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
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
                        <input type="text" id="empSearch" placeholder="Search name, mobile..."
                            class="pl-3 pr-3 py-2 border border-gray-200 text-xs outline-none focus:border-blue-500">
                        <button onclick="openModal()"
                            class="btn-outline-premium px-4 py-2 text-[10px] uppercase tracking-wider flex items-center gap-1">
                            <i class="mdi mdi-plus"></i> Add Employee
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1400px]">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Employee Name</th>
                                <th>Mobile Number</th>
                                <th>Designation</th>
                                <th>Monthly Leave</th>
                                <th>Salary</th>
                                <th>Payroll Date</th>
                                <th>Bank Name</th>
                                <th>Branch</th>
                                <th>Routing</th>
                                <th>A/C Holder</th>
                                <th>A/C Number</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody"></tbody>
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

    {{-- Employee Modal --}}
    <div id="employeeModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">
        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[95vh]">
            <div class="px-5 py-3 border-b flex justify-between items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center shadow-lg">
                        <i class="mdi mdi-account-tie text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-gray-800 uppercase text-sm leading-tight">Add New Employee
                        </h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Staff Management</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-black p-1 transition-colors">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form id="employeeForm" class="flex flex-col overflow-hidden m-0">
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow">
                    <input type="hidden" id="edit_id">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Employee Name *</label>
                            <input type="text" id="employee_name" class="form-input-fixed w-full" required
                                placeholder="Full Name">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Mobile Number *</label>
                            <input type="text" id="mobile_number" class="form-input-fixed w-full" required
                                placeholder="017xx-xxxxxx">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Designation *</label>
                            <input type="text" id="designation" class="form-input-fixed w-full" required
                                placeholder="e.g. Senior Teacher">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Payroll Date *</label>
                            <input type="date" id="payroll_date" class="form-input-fixed w-full" required>
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Monthly Leave
                                (Days)</label>
                            <input type="number" id="monthly_leave" class="form-input-fixed w-full" value="0"
                                min="0" max="31">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-blue-600 uppercase mb-1 block">Salary Amount *</label>
                            <input type="number" id="salary_amount"
                                class="form-input-fixed w-full font-bold border-blue-100" required placeholder="0.00">
                        </div>

                        <div class="col-span-1 sm:col-span-2 border-b border-gray-100 pb-1 mt-2">
                            <span class="text-blue-600 font-bold text-[9px] uppercase tracking-wider">Bank Details
                                (Optional)</span>
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Bank Name</label>
                            <input type="text" id="bank_name" class="form-input-fixed w-full"
                                placeholder="e.g. Dutch Bangla Bank">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Branch Name</label>
                            <input type="text" id="branch" class="form-input-fixed w-full">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Routing Number</label>
                            <input type="text" id="routing_number" class="form-input-fixed w-full">
                        </div>
                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">A/C Holder Name</label>
                            <input type="text" id="ac_holder_name" class="form-input-fixed w-full">
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">A/C Number</label>
                            <input type="text" id="ac_number" class="form-input-fixed w-full"
                                placeholder="Enter full account number">
                        </div>
                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-full sm:w-auto btn-outline-secondary px-6 py-2 text-[10px] uppercase tracking-widest order-2 sm:order-1">Cancel</button>
                    <button type="submit" id="saveBtn"
                        class="w-full sm:w-auto btn-outline-premium px-8 py-2 text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 order-1 sm:order-2">
                        <i class="mdi mdi-check-circle-outline text-sm"></i> Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // CRITICAL FIX: Ensure Axios sends session cookies to the API
        axios.defaults.withCredentials = true;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;

        function fetchEmployees(page = 1) {
            currentPage = page;
            const search = document.getElementById('empSearch').value;
            axios.get(`/api/school-employees?page=${page}&search=${search}`)
                .then(res => {
                    const {
                        data: employees,
                        ...meta
                    } = res.data;
                    const tbody = document.getElementById('employeeTableBody');
                    tbody.innerHTML = '';

                    if (employees.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="13" class="text-center py-4">No employees found.</td></tr>';
                    }

                    employees.forEach((e, index) => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${meta.from + index}</td>
                                <td class="font-bold text-blue-600">${e.employee_name}</td>
                                <td>${e.mobile_number}</td>
                                <td><span class="bg-gray-100 px-2 py-0.5 rounded">${e.designation}</span></td>
                                <td class="text-center font-bold">${e.monthly_leave || 0} Days</td>
                                <td class="font-bold text-green-600">${e.salary_amount}</td>
                                <td>${e.payroll_date}</td>
                                <td>${e.bank_name || '-'}</td>
                                <td>${e.branch || '-'}</td>
                                <td>${e.routing_number || '-'}</td>
                                <td>${e.ac_holder_name || '-'}</td>
                                <td>${e.ac_number || '-'}</td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <button onclick="editEmployee(${e.id})" class="text-blue-500 hover:scale-110 transition-transform"><i class="mdi mdi-pencil text-lg"></i></button>
                                        <button onclick="deleteEmployee(${e.id})" class="text-red-400 hover:scale-110 transition-transform"><i class="mdi mdi-trash-can-outline text-lg"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                    renderPagination(meta);
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    if (err.response && err.response.status === 401) {
                        Swal.fire('Session Expired', 'Please refresh the page and login again.', 'error');
                    }
                });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText =
                `Showing ${meta.from||0} to ${meta.to||0} of ${meta.total} entries`;

            let buttons = '';
            buttons +=
                `<button onclick="fetchEmployees(${meta.current_page - 1})" class="pagination-btn" ${meta.current_page === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>`;

            // Simple range to avoid too many buttons
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    buttons +=
                        `<button onclick="fetchEmployees(${i})" class="pagination-btn ${meta.current_page === i ? 'active' : ''}">${i}</button>`;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    buttons += `<span class="px-2">...</span>`;
                }
            }

            buttons +=
                `<button onclick="fetchEmployees(${meta.current_page + 1})" class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>`;
            controls.innerHTML = buttons;
        }

        document.getElementById('employeeForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const data = {
                employee_name: document.getElementById('employee_name').value,
                mobile_number: document.getElementById('mobile_number').value,
                designation: document.getElementById('designation').value,
                payroll_date: document.getElementById('payroll_date').value,
                monthly_leave: parseInt(document.getElementById('monthly_leave').value) || 0,
                salary_amount: document.getElementById('salary_amount').value,
                bank_name: document.getElementById('bank_name').value,
                branch: document.getElementById('branch').value,
                routing_number: document.getElementById('routing_number').value,
                ac_holder_name: document.getElementById('ac_holder_name').value,
                ac_number: document.getElementById('ac_number').value,
            };

            const request = id ? axios.put(`/api/school-employees/${id}`, data) : axios.post('/api/school-employees',
                data);

            request.then(() => {
                Toastify({
                    text: id ? "Updated Successfully" : "Created Successfully",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                fetchEmployees(currentPage);
            }).catch(err => {
                console.error("Error details:", err.response.data);
                const errorMsg = err.response.data.message || 'Check required fields.';
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: errorMsg,
                    footer: 'Verify if your account is linked to a School ID'
                });
            });
        };

        function editEmployee(id) {
            axios.get(`/api/school-employees/${id}`).then(res => {
                const e = res.data;
                document.getElementById('edit_id').value = e.id;
                document.getElementById('employee_name').value = e.employee_name;
                document.getElementById('mobile_number').value = e.mobile_number;
                document.getElementById('designation').value = e.designation;
                document.getElementById('payroll_date').value = e.payroll_date;
                document.getElementById('monthly_leave').value = e.monthly_leave;
                document.getElementById('salary_amount').value = e.salary_amount;
                document.getElementById('bank_name').value = e.bank_name;
                document.getElementById('branch').value = e.branch;
                document.getElementById('routing_number').value = e.routing_number;
                document.getElementById('ac_holder_name').value = e.ac_holder_name;
                document.getElementById('ac_number').value = e.ac_number;
                document.getElementById('modalTitle').innerText = "Edit Employee";
                openModal();
            });
        }

        function deleteEmployee(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This record will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete(`/api/school-employees/${id}`).then(() => {
                        Toastify({
                            text: "Deleted successfully",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchEmployees(currentPage);
                    });
                }
            });
        }

        function openModal() {
            document.getElementById('employeeModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closeModal() {
            document.getElementById('employeeModal').classList.add('hidden');
            document.getElementById('employeeForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = "Add New Employee";
            document.body.style.overflow = 'auto'; // Restore scroll
        }

        document.getElementById('empSearch').addEventListener('input', () => fetchEmployees(1));

        // Initial Fetch
        fetchEmployees();
    </script>
@endsection
