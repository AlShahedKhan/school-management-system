@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto !important;
            display: block;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 8px !important;
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 2px solid #edf2f7;
            color: #64748b;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 10px 8px !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all .2s;
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
            transition: all .2s;
            border-radius: 0;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
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
            min-width: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .form-input-fixed:disabled {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
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

        .select2-container--default .select2-selection--single {
            border-radius: 0 !important;
            border: 1px solid #cbd5e1 !important;
            height: 38px !important;
            padding-top: 5px;
        }

        .select2-search--dropdown::before {
            content: "\F0349";
            font-family: "Material Design Icons";
            position: absolute;
            left: 12px;
            top: 12px;
            color: #94a3b8;
            font-size: 16px;
        }

        .select2-search--dropdown .select2-search__field {
            padding-left: 35px !important;
            border-radius: 0 !important;
            border: 1px solid #cbd5e1 !important;
        }

        .premium-modal {
            backdrop-filter: blur(4px);
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4" style="border-radius:0;">
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

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative w-full sm:w-56">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="incomeSearch" placeholder="Search records..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius:0;">
                        </div>

                        <button type="button" id="openIncomeModal"
                            class="btn-outline-premium px-5 py-2 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="mdi mdi-plus-box-outline"></i> Collect Income
                        </button>

                        <a href="{{ url('/api/incomes-export?format=pdf') }}" target="_blank"
                            class="btn-outline-secondary px-5 py-2 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="mdi mdi-file-pdf-box"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1100px]">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th width="100">Member No</th>
                                <th width="90">Date</th>
                                <th width="150">Source</th>
                                <th>Payer Name</th>
                                <th>Address</th>
                                <th width="110">Mobile</th>
                                <th width="100">Amount</th>
                                <th class="text-center" width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody id="incomeTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">Showing 0
                        entries</div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Collect Income Modal (Fully Synced Design) --}}
    <div id="incomeModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[95vh]">

            <div class="px-5 py-3 border-b flex justify-between items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center shadow-lg">
                        <i class="mdi mdi-cash-register text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-gray-800 uppercase text-sm leading-tight">Collect Income
                        </h3>
                        <p id="modalSubtitle" class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Financial
                            Transaction</p>
                    </div>
                </div>
                <button type="button" onclick="closeIncomeModal()"
                    class="text-gray-400 hover:text-black p-1 transition-colors">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form id="incomeForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="income_id">
                <input type="hidden" id="member_no_hidden">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div id="member_search_group" class="col-span-1 sm:col-span-2">
                            <label class="text-[9px] font-bold text-blue-600 uppercase mb-1 block">Quick Search
                                Member</label>
                            <select id="member_select" class="form-input-fixed w-full" style="width: 100%"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Member No</label>
                            <input type="text" id="member_no_display" readonly placeholder="General"
                                class="form-input-fixed bg-gray-100 font-bold text-blue-600 w-full cursor-not-allowed">
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Collection Date *</label>
                            <input type="date" id="date" required class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Income Source *</label>
                            <input type="text" id="income_source" placeholder="e.g. Donation" required
                                class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-green-600 uppercase mb-1 block">Amount (৳) *</label>
                            <input type="number" id="amount" placeholder="0.00" required
                                class="form-input-fixed w-full font-bold text-green-700 bg-green-50/30 border-green-200 focus:border-green-500">
                        </div>

                        <div class="col-span-1 sm:col-span-2 border-b border-gray-100 pb-1 mt-2">
                            <span class="text-gray-400 font-bold text-[9px] uppercase tracking-wider">Payer
                                Information</span>
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Payer Name *</label>
                            <input type="text" id="name" placeholder="Full Name" required
                                class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Mobile Number *</label>
                            <input type="text" id="mobile" placeholder="017..." required
                                class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Address</label>
                            <input type="text" id="address" placeholder="Full Address"
                                class="form-input-fixed w-full">
                        </div>
                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeIncomeModal()"
                        class="w-full sm:w-auto btn-outline-secondary border border-gray-300 px-6 py-2 text-[10px] uppercase tracking-widest order-2 sm:order-1 transition-all hover:bg-gray-50 flex items-center justify-center gap-2">
                        <i class="mdi mdi-close-circle-outline text-sm"></i>
                        Cancel
                    </button>

                    <button type="submit" id="saveBtn"
                        class="w-full sm:w-auto btn-outline-premium px-8 py-2 text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 order-1 sm:order-2">
                        <i class="mdi mdi-check-circle-outline text-sm"></i>
                        <span id="saveBtnText">Save & Send SMS</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        const incomeModal = document.getElementById('incomeModal');

        $(document).ready(function() {
            initSelect2();
            fetchIncomes();

            document.getElementById('openIncomeModal').addEventListener('click', function() {
                resetForm();
                incomeModal.classList.remove('hidden');
                incomeModal.classList.add('flex');
                document.getElementById('date').valueAsDate = new Date();
            });
        });

        function initSelect2() {
            $('#member_select').select2({
                dropdownParent: $('#incomeModal'),
                placeholder: 'Search by Member No or Name...',
                allowClear: true,
                ajax: {
                    url: '/api/membership-list',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        search: params.term
                    }),
                    processResults: data => ({
                        results: data.map(m => ({
                            id: m.id,
                            text: `[${m.member_no}] ${m.name}`,
                            member_data: m
                        }))
                    })
                }
            }).on('select2:select', function(e) {
                const m = e.params.data.member_data;
                document.getElementById('member_no_hidden').value = m.member_no;
                document.getElementById('member_no_display').value = m.member_no;
                document.getElementById('name').value = m.name;
                document.getElementById('mobile').value = m.mobile_number;
                document.getElementById('address').value = m.address || '';
                document.getElementById('income_source').value = m.income_source || '';
                document.getElementById('amount').value = m.amount || '';
            }).on('select2:clear', function() {
                document.getElementById('member_no_hidden').value = '';
                document.getElementById('member_no_display').value = '';
            });
        }

        function resetForm() {
            const form = document.getElementById('incomeForm');
            form.reset();
            document.getElementById('income_id').value = '';
            document.getElementById('member_no_hidden').value = '';
            document.getElementById('member_no_display').value = '';
            $('#member_select').val(null).trigger('change');

            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = false;
            saveBtn.innerHTML =
                '<i class="mdi mdi-check-circle-outline"></i> <span id="saveBtnText">Save & Send SMS</span>';

            document.getElementById('modalTitle').innerText = "Collect Income";
            document.getElementById('modalSubtitle').innerText = "Financial Transaction";
        }

        function closeIncomeModal() {
            incomeModal.classList.add('hidden');
            incomeModal.classList.remove('flex');
            resetForm();
        }

        function fetchIncomes(page = 1) {
            currentPage = page;
            const search = document.getElementById('incomeSearch').value;
            axios.get('/api/incomes', {
                    params: {
                        search,
                        page
                    }
                })
                .then(res => {
                    const {
                        data,
                        from,
                        to,
                        total,
                        last_page,
                        current_page
                    } = res.data;
                    const tbody = document.getElementById('incomeTableBody');

                    let html = '';
                    if (data && data.length) {
                        data.forEach((i, index) => {
                            html += `
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="font-bold text-gray-400">${from + index}</td>
                                <td class="font-bold text-blue-600">${i.member_no || '<span class="text-gray-300">General</span>'}</td>
                                <td>${formatDate(i.date)}</td>
                                <td class="uppercase font-semibold text-gray-500">${i.income_source}</td>
                                <td class="font-bold text-gray-800">${i.name}</td>
                                <td class="text-gray-500">${i.address || '—'}</td>
                                <td>${i.mobile}</td>
                                <td class="font-bold text-blue-600">${i.amount} ৳</td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <button onclick="resendSMS(${i.id})" title="Resend SMS" class="action-icon-btn text-green-500"><i class="mdi mdi-message-text-fast-outline"></i></button>
                                        <button onclick="printReceipt(${i.id})" title="Print Receipt" class="action-icon-btn text-orange-500"><i class="mdi mdi-printer-eye"></i></button>
                                        <button onclick="editIncome(${i.id})" title="Edit" class="action-icon-btn text-blue-500"><i class="mdi mdi-pencil-outline"></i></button>
                                        <button onclick="deleteIncome(${i.id})" title="Delete" class="action-icon-btn text-red-400"><i class="mdi mdi-trash-can-outline"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html =
                            '<tr><td colspan="9" class="text-center py-10 text-gray-400 uppercase font-black">No records found</td></tr>';
                    }
                    tbody.innerHTML = html;
                    renderPagination({
                        from,
                        to,
                        total,
                        last_page,
                        current_page
                    });
                });
        }

        document.getElementById('incomeForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('income_id').value;
            const btn = document.getElementById('saveBtn');
            const data = {
                date: document.getElementById('date').value,
                income_source: document.getElementById('income_source').value,
                name: document.getElementById('name').value,
                address: document.getElementById('address').value,
                mobile: document.getElementById('mobile').value,
                amount: document.getElementById('amount').value,
                member_no: document.getElementById('member_no_hidden').value
            };

            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Processing...';

            const url = id ? `/api/incomes/${id}` : '/api/incomes';
            const method = id ? 'put' : 'post';
            axios({
                method,
                url,
                data
            }).then(res => {
                Toastify({
                    text: "Success: Data Logged",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();

                if (!id && res.data.sms_status && res.data.sms_status.success === false) {

                    const errorMessage = res.data.sms_status.details?.message || 'SMS could not be sent.';

                    // Check if the error is actually related to balance/credits
                    const isBalanceError = errorMessage.toLowerCase().includes('balance') ||
                        errorMessage.toLowerCase().includes('insufficient') ||
                        errorMessage.toLowerCase().includes('credit');

                    Swal.fire({
                        title: 'Saved (SMS Failed)',
                        html: `<p style="color: #dc3545; font-weight: 600; margin-bottom: 0;">${errorMessage}</p>`,
                        icon: 'warning',
                        // Only show "Buy" button and "Later" if it's a balance issue
                        showCancelButton: isBalanceError,
                        confirmButtonText: isBalanceError ?
                            '<i class="mdi mdi-cart-outline"></i> Buy SMS Package' : 'OK',
                        cancelButtonText: 'Later',
                        customClass: {
                            confirmButton: isBalanceError ? 'btn btn-outline-primary mx-2' :
                                'btn btn-primary',
                            cancelButton: 'btn btn-secondary mx-2'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        // Only redirect if the "Buy" button was shown and clicked
                        if (isBalanceError && result.isConfirmed) {
                            window.location.href = "{{ route('school.sms-package') }}";
                        }
                    });
                }

                closeIncomeModal();
                fetchIncomes(currentPage);
            }).catch(err => {
                Swal.fire('Error', err.response?.data?.message || 'Transaction failed', 'error');
            }).finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Save & Send SMS';
            });
        };

        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const [year, month, day] = dateStr.split('-');
            return `${day}/${month}/${year}`;
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText =
                `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total} entries`;
            controls.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = `pagination-btn`;
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchIncomes(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    const btn = document.createElement('button');
                    btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                    btn.innerText = i;
                    btn.onclick = () => fetchIncomes(i);
                    controls.appendChild(btn);
                }
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = `pagination-btn`;
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchIncomes(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function editIncome(id) {
            resetForm();
            incomeModal.classList.remove('hidden');
            incomeModal.classList.add('flex');
            document.getElementById('modalTitle').innerText = "Loading...";
            axios.get(`/api/incomes/${id}`).then(res => {
                const i = res.data;
                document.getElementById('income_id').value = i.id;
                document.getElementById('date').value = i.date;
                document.getElementById('income_source').value = i.income_source;
                document.getElementById('name').value = i.name;
                document.getElementById('address').value = i.address || '';
                document.getElementById('mobile').value = i.mobile;
                document.getElementById('amount').value = i.amount;
                document.getElementById('member_no_hidden').value = i.member_no || '';
                document.getElementById('member_no_display').value = i.member_no || 'General';

                document.getElementById('modalTitle').innerText = "Update Record";
                document.getElementById('saveBtnText').innerText = "Save Changes";
            }).catch(() => {
                closeIncomeModal();
                Swal.fire('Error', 'Record not found', 'error');
            });
        }

        function resendSMS(id) {
            Toastify({
                text: "Requesting SMS Gateway...",
                style: {
                    background: "#3b82f6"
                }
            }).showToast();
            axios.post(`/api/incomes/${id}/resend-sms`).then(res => {
                if (res.data.sms_status && res.data.sms_status.success)
                    Toastify({
                        text: "SMS Sent",
                        style: {
                            background: "#10b981"
                        }
                    }).showToast();
                else
                    Swal.fire('Error', res.data.sms_status?.gateway_msg || 'SMS Failed', 'error');
            }).catch(() => Swal.fire('Error', 'Gateway Offline', 'error'));
        }

        function printReceipt(id) {
            window.open(`/api/incomes-receipt/${id}`, '_blank');
        }

        function deleteIncome(id) {
            Swal.fire({
                title: 'Delete?',
                text: "This action is permanent.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/incomes/${id}`).then(() => {
                        Toastify({
                            text: "Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchIncomes(currentPage);
                    });
                }
            });
        }

        let searchTimeout;
        document.getElementById('incomeSearch').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchIncomes(1), 300);
        });
    </script>
@endsection
