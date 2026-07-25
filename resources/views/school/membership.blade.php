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
            justify-content: center;
            min-width: 35px;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
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
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
        }

        .premium-modal {
            backdrop-filter: blur(4px);
        }

        .modal-content-sharp {
            border-radius: 0 !important;
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
                            <input type="text" id="memberSearch" placeholder="Search name or ID..."
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

                        <button onclick="openMemberModal()" id="openMemberModal"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Member
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="memberSearchMobile" placeholder="Search name or ID..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">
                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Member Filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4">
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Name</label>
                            <div class="relative">
                                <input type="text" id="nameFilter" placeholder="Enter name..."
                                    class="w-full py-1.5 px-2 text-xs border border-gray-100 outline-none focus:border-blue-500 bg-white"
                                    style="border-radius: 0; height: 32px;">
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

            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl" style="border-radius: 0;">
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
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th width="60">Sl</th>
                                <th>Member No.</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>Income Source</th>
                                <th>Amount</th>
                                <th width="120" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="memberTableBody" class="bg-white divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="memberModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'memberModal')">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Add new member
                </h3>
            </div>

            <form id="memberForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="member_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Member
                                no</label>
                            <input type="text" id="member_no" required placeholder="e.g. m-001"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Full
                                name</label>
                            <input type="text" id="name" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Mobile
                                number</label>
                            <input type="text" id="mobile_number" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Income
                                source</label>
                            <input type="text" id="income_source"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Address</label>
                            <textarea id="address" rows="2" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs"
                                style="resize:none; border-radius: 0;"></textarea>
                        </div>

                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Amount</label>
                            <input type="number" id="amount" step="0.01" required
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeMemberModal()"
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        axios.defaults.withCredentials = true;

        let currentPage = 1;

        function openMemberModal() {
            document.getElementById('memberForm').reset();
            document.getElementById('member_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add New Member';
            document.getElementById('memberModal').classList.remove('hidden');
        }

        function closeMemberModal() {
            document.getElementById('memberModal').classList.add('hidden');
        }

        function fetchMemberships(page = 1) {
            currentPage = page;
            const search = document.getElementById('memberSearch').value;

            axios.get('/api/memberships', {
                    params: {
                        search,
                        page
                    }
                })
                .then(res => {
                    const data = res.data.data;
                    const meta = res.data;
                    const tbody = document.getElementById('memberTableBody');
                    tbody.innerHTML = '';

                    if (!data || data.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="8" class="text-center py-10 text-gray-400 uppercase font-black tracking-tighter">No records found</td></tr>`;
                        document.getElementById('paginationInfo').innerText = "Showing 0 entries";
                        document.getElementById('paginationControls').innerHTML = "";
                        return;
                    }

                    data.forEach((item, index) => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="text-gray-500">${meta.from + index}</td>
                            <td class="text-gray-700 capitalize">${(item.member_no || 'N/A').toLowerCase()}</td>
                            <td class="text-gray-700 capitalize">${item.name.toLowerCase()}</td>
                            <td class="text-gray-600 capitalize">${(item.address || '-').toLowerCase()}</td>
                            <td class="text-gray-600">${item.mobile_number}</td>
                            <td class="text-gray-500 capitalize">${(item.income_source || '-').toLowerCase()}</td>
                            <td class="text-gray-700">${item.amount}</td>
                            <td>
                                <div class="flex justify-center gap-3">
                                    <button onclick="editMember(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i>
                                    </button>
                                    <button onclick="deleteMember(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i>
                                </div>
                            </td>
                        </tr>`;
                    });
                    renderPagination(meta);
                });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText =
                `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = `pagination-btn`;
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchMemberships(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchMemberships(i);
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = `pagination-btn`;
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchMemberships(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('memberSearch').addEventListener('input', () => fetchMemberships(1));

        document.getElementById('memberForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('member_id').value;
            const saveBtn = document.getElementById('saveBtn');

            const data = {
                member_no: document.getElementById('member_no').value,
                name: document.getElementById('name').value,
                address: document.getElementById('address').value,
                mobile_number: document.getElementById('mobile_number').value,
                income_source: document.getElementById('income_source').value,
                amount: document.getElementById('amount').value,
            };

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Processing...';

            const url = id ? `/api/memberships/update/${id}` : '/api/memberships';

            axios.post(url, data)
                .then(() => {
                    Toastify({
                        text: "Member successfully saved",
                        style: {
                            background: "#10b981"
                        }
                    }).showToast();
                    closeMemberModal();
                    fetchMemberships(currentPage);
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Error processing request';
                    Swal.fire('Error', msg, 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Save Member';
                });
        });

        function editMember(id) {
            axios.get('/api/memberships', {
                params: {
                    page: currentPage
                }
            }).then(res => {
                const item = res.data.data.find(m => m.id === id);
                if (item) {
                    document.getElementById('member_id').value = item.id;
                    document.getElementById('member_no').value = item.member_no;
                    document.getElementById('name').value = item.name;
                    document.getElementById('address').value = item.address;
                    document.getElementById('mobile_number').value = item.mobile_number;
                    document.getElementById('income_source').value = item.income_source;
                    document.getElementById('amount').value = item.amount;
                    document.getElementById('modalTitle').innerText = "Edit Member Details";
                    document.getElementById('memberModal').classList.remove('hidden');
                }
            });
        }

        function deleteMember(id) {
            Swal.fire({
                title: 'Delete Member?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/memberships/' + id).then(() => {
                        Toastify({
                            text: "Member Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchMemberships(currentPage);
                    });
                }
            });
        }


        //Filter & Print modals opening Script
        document.addEventListener('DOMContentLoaded', function() {
            // Helper to toggle modal
            const toggleModal = (id, show) => {
                document.getElementById(id).classList.toggle('hidden', !show);
            };

            // Filter Modal
            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('resetFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('applyFilter').addEventListener('click', () => toggleModal('filterModal',
                false));

            // Export Modal
            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            // Close on outside click
            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });

        fetchMemberships();
    </script>
@endsection