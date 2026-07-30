<!-- Created on 2026-07-07: Standalone Promotion History blade view -->
@extends('layouts.school')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Modified on 2026-07-09: Imported SweetAlert2 and Toastify JS/CSS for alerts and toast notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        /* Global & Table Styles (Matching student-lists.blade.php style structure) */
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
            padding: 1rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }

        .table-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 0px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        /* ================= Table Container ================= */
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 0 !important;
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
            border-radius: 3px !important;
        }

        /* ================= Table Core ================= */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            border: 1px solid #e5e7eb !important;
            font-size: 13px !important;
        }

        /* ================= Table Header ================= */
        th {
            padding: 12px 16px !important;
            background: #f8fafc !important;
            color: #374151 !important;
            font-weight: 600 !important;
            vertical-align: middle !important;
            text-align: left !important;
            text-transform: none !important;
            border-bottom: 2px solid #e5e7eb !important;
            border-right: 1px solid #e5e7eb !important;
            white-space: nowrap !important;
        }

        th:last-child {
            border-right: none !important;
        }

        /* ================= Table Body ================= */
        tr {
            border-bottom: 1px solid #e5e7eb !important;
        }

        tr:last-child {
            border-bottom: none !important;
        }

        tr:hover {
            background: #f9fafb !important;
        }

        td {
            padding: 12px 16px !important;
            vertical-align: middle !important;
            border-right: 1px solid #e5e7eb !important;
            font-size: 13px !important;
            color: #374151 !important;
            background: transparent !important;
            white-space: nowrap !important;
        }

        td:first-child {
            border-left: none !important;
        }

        td:last-child {
            border-right: none !important;
        }

        /* Premium Buttons */
        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
        }

        .btn-outline-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-premium:hover::before {
            opacity: 1;
        }

        .btn-outline-premium:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        /* Modified on 2026-07-09: Action button styling matching student-lists.blade.php */
        .btn-action {
            width: 32px !important;
            height: 32px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            border: 1px solid #e5e7eb !important;
            background: #ffffff !important;
            color: #4b5563 !important;
        }

        .btn-action:hover {
            background: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #edf2f7;
        }

        .pagination-btn {
            padding: 5px 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all .2s;
            cursor: pointer;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .pagination-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            th {
                padding: 10px 12px !important;
                font-size: 11px !important;
            }

            td {
                padding: 10px 12px !important;
                font-size: 12px !important;
            }

            .pagination-container {
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.75rem;
            }

            .pagination-btn {
                padding: 5px 10px;
                font-size: 12px;
            }
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="w-full lg:w-auto">
                        <h2 class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Student Promotion History</h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span class="text-slate-500">Promotion History</span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="historySearch" placeholder="Search by name, ID number..." oninput="fetchHistory(1)"
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <a href="{{ route('school.student-promote') }}"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            <i class="fa-solid fa-user-plus hidden sm:block"></i> New Promotion
                        </a>
                    </div>
                </div>

                {{-- Mobile Search --}}
                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="historySearchMobile" placeholder="Search by name, ID number..." oninput="syncMobileSearch(this.value)"
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table>
                        <thead>
                            <tr>
                                <!-- Modified on 2026-07-09: Display Student ID (Permanent) alongside Dynamic Admission IDs -->
                                <th class="text-left">SI</th>
                                <th class="text-left">Student Name</th>
                                <th class="text-left">Student ID</th>
                                <th class="text-left">From Admission ID</th>
                                <th class="text-left">To Admission ID</th>
                                <th class="text-left">From Academic</th>
                                <th class="text-left">To Academic</th>
                                <th class="text-left">Promote Date</th>
                                <th class="text-left">Promote Fee</th>
                                <!-- Modified on 2026-07-09: Added Action header -->
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <tr>
                                <td colspan="10" class="p-6 text-center text-gray-400">Loading history...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="historyPaginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="historyPaginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const localApi = axios.create({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        // Modified on 2026-07-09: Keep track of the current history page for list refreshes
        let currentHistoryPage = 1;

        document.addEventListener('DOMContentLoaded', () => {
            fetchHistory(1);
        });

        // Sync mobile and desktop search inputs
        function syncMobileSearch(val) {
            document.getElementById('historySearch').value = val;
            fetchHistory(1);
        }

        async function fetchHistory(page = 1) {
            currentHistoryPage = page;
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '<tr><td colspan="9" class="p-6 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading history...</td></tr>';
            
            const search = document.getElementById('historySearch').value;
            // Keep mobile search in sync
            document.getElementById('historySearchMobile').value = search;

            try {
                const res = await localApi.get('/api/school/promote/history', {
                    params: {
                        search: search,
                        page: page
                    }
                });

                const meta = res.data;
                const items = res.data.data || [];
                tbody.innerHTML = '';

                 if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="p-6 text-center text-gray-400">No promotion records found.</td></tr>';
                    document.getElementById('historyPaginationInfo').textContent = '0 of 0';
                    document.getElementById('historyPaginationControls').innerHTML = '';
                    return;
                }

                items.forEach((item, index) => {
                    const fromAcademic = `${item.from_class} (${item.from_session})<br><span class="text-[10px] text-gray-400">Section: ${item.from_section}${item.from_group ? ', Group: ' + item.from_group : ''}</span>`;
                    const toAcademic = `${item.to_class} (${item.to_session})<br><span class="text-[10px] text-gray-400">Section: ${item.to_section}${item.to_group ? ', Group: ' + item.to_group : ''}</span>`;
                    const promoteDate = formatDate(item.promote_date);

                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 text-gray-500 font-normal">${(meta.from || 0) + index}</td>
                            <td class="p-3 text-gray-800 font-semibold">${item.student_name}</td>
                            <td class="p-3 font-semibold text-gray-600 font-mono">${item.from_student_id_number}</td>
                            <td class="p-3 font-semibold text-gray-700 font-mono">${item.from_admission_id || item.from_student_id_number}</td>
                            <td class="p-3 font-semibold text-gray-700 font-mono">${item.to_admission_id || item.to_student_id_number}</td>
                            <td class="p-3 text-gray-600">${fromAcademic}</td>
                            <td class="p-3 text-gray-600">${toAcademic}</td>
                            <td class="p-3 text-gray-500">${promoteDate}</td>
                            <td class="p-3 font-semibold text-blue-600">${parseFloat(item.promote_fee).toFixed(2)}</td>
                            <!-- Modified on 2026-07-09: Render action buttons for student details, editing, deletion and status toggling -->
                            <td class="p-3 text-center">
                                <div class="action-buttons flex justify-center gap-2">
                                    <a href="{{ route('school.students') }}?student_id=${item.student_id}&action=view" title="View Details" class="btn-action view text-emerald-500">
                                        <i class="far fa-eye" style="font-size: 15px;"></i>
                                    </a>
                                    <a href="{{ route('school.students') }}?student_id=${item.student_id}&action=edit" title="Edit" class="btn-action edit text-blue-500">
                                        <i class="far fa-edit" style="font-size: 15px;"></i>
                                    </a>
                                    <button onclick="deleteStudent(${item.student_id})" title="Delete" class="btn-action delete text-red-400">
                                        <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                                    </button>
                                    <!-- Modified on 2026-07-09: Updated status toggle icon to circle representation with inline colors to override parent styles -->
                                    <button onclick="toggleStudentStatus(${item.student_id}, '${item.status}', '${item.student_name}', '${item.to_student_id_number}', '${item.inactive_date || ''}')" 
                                            title="${item.status === 'Inactive' ? 'Activate Student' : 'Deactivate Student'}" 
                                            class="btn-action status">
                                        <i class="fas fa-circle" style="font-size: 15px; color: ${item.status === 'Inactive' ? '#ef4444' : '#10b981'} !important;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('historyPaginationInfo').textContent = `${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0}`;
                renderHistoryPagination(meta);

            } catch (e) {
                console.error("Failed to fetch promotion history", e);
                tbody.innerHTML = '<tr><td colspan="10" class="p-6 text-center text-red-500">Failed to load promotion history records.</td></tr>';
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '---';
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return dateString;
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            return `${d.getDate()}-${months[d.getMonth()]}-${d.getFullYear()}`;
        }

        function renderHistoryPagination(meta) {
            const container = document.getElementById('historyPaginationControls');
            container.innerHTML = '';

            if (meta.last_page <= 1) return;

            // Prev Button
            const prevBtn = document.createElement('button');
            prevBtn.className = `pagination-btn`;
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.disabled = (meta.current_page === 1);
            if (meta.current_page > 1) {
                prevBtn.onclick = () => fetchHistory(meta.current_page - 1);
            }
            container.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    const btn = document.createElement('button');
                    btn.className = `pagination-btn ${i === meta.current_page ? 'active' : ''}`;
                    btn.textContent = i;
                    btn.onclick = () => fetchHistory(i);
                    container.appendChild(btn);
                } else if (i === 2 || i === meta.last_page - 1) {
                    const span = document.createElement('span');
                    span.className = 'px-1.5 py-1 text-xs text-gray-400';
                    span.textContent = '...';
                    container.appendChild(span);
                }
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = `pagination-btn`;
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.disabled = (meta.current_page === meta.last_page);
            if (meta.current_page < meta.last_page) {
                nextBtn.onclick = () => fetchHistory(meta.current_page + 1);
            }
            container.appendChild(nextBtn);
        }

        // Modified on 2026-07-09: Delete student function with confirmation popup
        function deleteStudent(id) {
            Swal.fire({
                title: 'Delete Student?',
                text: "This will permanently delete the student and their login user.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'CONFIRM DELETE',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`{{ url('/api/school/students') }}/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        Toastify({
                            text: "Student deleted successfully!",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchHistory(currentHistoryPage);
                    }).catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete student.' });
                    });
                }
            });
        }

        // Modified on 2026-07-09: Status toggling function with SweetAlert2 confirmation popups and inputs
        function toggleStudentStatus(id, currentStatus, studentName, studentIdNumber, inactiveSince) {
            const today = new Date().toISOString().split('T')[0];
            if (currentStatus === 'Inactive') {
                // Activate Student Popup
                Swal.fire({
                    title: 'Activate Student',
                    html: `
                        <div class="text-left text-xs space-y-2 p-2">
                            <p><strong>Student Name:</strong> ${studentName}</p>
                            <p><strong>Student ID:</strong> ${studentIdNumber}</p>
                            <p><strong>Current Status:</strong> <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700">Inactive</span></p>
                            <p><strong>Inactive Since:</strong> ${inactiveSince || 'N/A'}</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#10b981', // Green for active
                    cancelButtonColor: '#64748b',
                    customClass: {
                        popup: 'modal-content-sharp'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                            status: 'Active',
                            active_date: today
                        }, {
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(res => {
                            Toastify({
                                text: "Student Activated Successfully!",
                                style: { background: "#10b981" }
                            }).showToast();
                            fetchHistory(currentHistoryPage);
                        }).catch(err => {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to activate student.' });
                        });
                    }
                });
            } else {
                // Deactivate Student Popup
                Swal.fire({
                    title: 'Deactivate Student',
                    html: `
                        <div class="text-left text-xs space-y-3 p-2">
                            <p><strong>Student Name:</strong> ${studentName}</p>
                            <p><strong>Student ID:</strong> ${studentIdNumber}</p>
                            <p><strong>Current Status:</strong> <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-green-100 text-green-700">Active</span></p>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1.5 capitalize">Inactive Date</label>
                                <input type="date" id="swal_inactive_date" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" value="${today}">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1.5 capitalize">Reason (Optional)</label>
                                <textarea id="swal_inactive_reason" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs" style="border-radius:0; height:60px;" placeholder="Enter reason..."></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#ef4444', // Red for inactive
                    cancelButtonColor: '#64748b',
                    customClass: {
                        popup: 'modal-content-sharp'
                    },
                    preConfirm: () => {
                        return {
                            inactive_date: document.getElementById('swal_inactive_date').value,
                            inactive_reason: document.getElementById('swal_inactive_reason').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;
                        axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                            status: 'Inactive',
                            inactive_date: data.inactive_date,
                            inactive_reason: data.inactive_reason
                        }, {
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(res => {
                            Toastify({
                                text: "Student Inactivated Successfully!",
                                style: { background: "#ef4444" }
                            }).showToast();
                            fetchHistory(currentHistoryPage);
                        }).catch(err => {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to deactivate student.' });
                        });
                    }
                });
            }
        }
    </script>
@endsection
