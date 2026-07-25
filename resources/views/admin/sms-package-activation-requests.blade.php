@extends('layouts.admin')

@section('title', 'SMS Request Management')
@section('page-title', 'SMS Activation Requests')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
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
            background: #fff;
            border-radius: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
            width: 100%;
            overflow: hidden;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            display: block;
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
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 8px 8px !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        table tbody tr:hover {
            background: #f9fafb;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        /* Status Badges */
        .status-pill {
            font-weight: 800 !important;
            padding: 4px 10px !important;
            border-radius: 4px !important;
            color: #000 !important;
            font-size: 10px;
            display: inline-block;
            text-transform: uppercase;
        }

        .bg-pending {
            background-color: #fef08a !important;
        }

        .bg-approved {
            background-color: #bbf7d0 !important;
        }

        .bg-rejected {
            background-color: #fecaca !important;
        }

        /* Standardized Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .25rem;
            font-size: .7rem;
            padding: .25rem .5rem;
            font-weight: 500;
            transition: all .2s;
            cursor: pointer;
            border: none;
            border-radius: 0;
        }

        .action-btn i {
            font-size: .65rem;
        }

        .action-active {
            background: #10b981;
            color: #fff;
        }

        .action-reject {
            background: #ef4444;
            color: #fff;
        }

        .action-edit {
            background: #3b82f6;
            color: #fff;
        }

        .action-delete {
            background: #6b7280;
            color: #fff;
        }

        .action-active:hover {
            background: #059669;
            transform: scale(1.05);
        }

        .action-reject:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .action-edit:hover {
            background: #2563eb;
            transform: scale(1.05);
        }

        .action-delete:hover {
            background: #374151;
            transform: scale(1.05);
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4" style="border-radius:0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 leading-tight">SMS Activation Hub</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black">Manage Bundle Requests</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative w-full sm:w-56">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="searchBox" placeholder="Search school..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500" />
                        </div>

                        <select id="filterStatus"
                            class="border border-gray-200 px-2 py-2 text-xs w-full sm:w-44 bg-white outline-none cursor-pointer">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table class="min-w-[1300px] text-[11px]">
                        <thead>
                            <tr>
                                <th class="px-4">School</th>
                                <th>Transaction ID</th>
                                <th>Package Name</th>
                                <th>Credit</th>
                                <th>Price</th>
                                <th>Profit</th>
                                <th>Status</th>
                                <th>Activation Date</th>
                                <th>Expiry Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestContainer">
                            <tr>
                                <td colspan="10" class="text-center py-10 text-gray-400 italic">Synchronizing Secure
                                    Data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md p-6 shadow-xl" style="border-radius:0;">
            <h3 class="text-lg font-bold mb-4">Edit SMS Request</h3>
            <form id="editForm">
                <input type="hidden" id="edit_id">
                <div class="mb-4">
                    <label class="block text-xs font-bold mb-1">Transaction ID</label>
                    <input type="text" id="edit_trx"
                        class="w-full border p-2 text-sm outline-none focus:border-blue-500" style="border-radius:0;">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold mb-1">Admin Note</label>
                    <textarea id="edit_note" class="w-full border p-2 text-sm outline-none focus:border-blue-500" rows="3"
                        style="border-radius:0;"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const fetchUrl = "/api/sms-requests";

            function showToast(msg, type = "success") {
                Toastify({
                    text: msg,
                    duration: 2500,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === "success" ? "#10b981" : "#ef4444"
                }).showToast();
            }

            // Simple date formatter helper
            function formatDate(dateString) {
                if (!dateString) return '<span class="text-gray-300">---</span>';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            window.loadRequests = function() {
                $.get(fetchUrl, function(res) {
                    let html = '';
                    let data = res.data || res;

                    if (!data || data.length === 0) {
                        html =
                            '<tr><td colspan="10" class="text-center py-10 text-gray-400 italic">No requests found...</td></tr>';
                    } else {
                        data.forEach(item => {
                            let statusClass = item.status === 'approved' ? 'bg-approved' : (item
                                .status === 'rejected' ? 'bg-rejected' : 'bg-pending');
                            let schoolName = item.school ? item.school.school_name :
                                'Unknown School';
                            let packageName = item.package ? item.package.name : 'SMS Bundle';

                            html += `
                            <tr id="row_${item.id}">
                                <td class="px-4 font-semibold">${schoolName}</td>
                                <td><code class="text-blue-600 font-bold">${item.trx_id}</code></td>
                                <td>${packageName}</td>
                                <td class="font-bold">${item.total_sms.toLocaleString()}</td>
                                <td class="font-bold">৳${item.sale_price}</td>
                                <td class="text-green-600 font-bold">৳${item.profit}</td>
                                <td><span class="status-pill ${statusClass}">${item.status}</span></td>
                                <td class="text-gray-600">${formatDate(item.purchase_date)}</td>
                                <td class="text-gray-600 font-medium">${formatDate(item.expiry_date)}</td>
                                <td class="text-center">
                                    <div class="flex justify-center gap-1 flex-wrap">
                                        <button onclick="handleProcess(${item.id}, 'approve')" class="action-btn action-active" title="Approve"><i class="fa fa-check"></i></button>
                                        <button onclick="handleProcess(${item.id}, 'reject')" class="action-btn action-reject" title="Reject"><i class="fa fa-xmark"></i></button>
                                        <button onclick="openEdit(${item.id}, '${item.trx_id}', '${item.admin_note ?? ''}')" class="action-btn action-edit" title="Edit"><i class="fa fa-edit"></i></button>
                                        <button onclick="handleDelete(${item.id})" class="action-btn action-delete" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#requestContainer').html(html);
                }).fail(e => showToast("Failed to load data", "error"));
            };

            window.handleProcess = function(id, action) {
                Swal.fire({
                    title: action === 'approve' ? 'Approve this request?' : 'Reject this request?',
                    text: action === 'approve' ?
                        'This will activate the bundle and update school balance.' : '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                    input: action === 'reject' ? 'text' : null,
                    inputPlaceholder: 'Reason for rejection...'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/api/sms-requests/${id}/${action}`,
                            method: 'POST',
                            data: {
                                admin_note: r.value || 'Processed via Admin'
                            },
                            success: function(res) {
                                showToast(res.message || "Updated successfully");
                                loadRequests();
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message || 'Action Failed',
                                    'error');
                            }
                        });
                    }
                });
            };

            window.handleDelete = function(id) {
                Swal.fire({
                    title: 'Delete this request?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Delete'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/api/sms-requests/${id}`,
                            method: 'DELETE',
                            success: function(res) {
                                showToast("Record Deleted", "error");
                                $(`#row_${id}`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message || 'Delete Failed',
                                    'error');
                            }
                        });
                    }
                });
            };

            window.openEdit = (id, trx, note) => {
                $('#edit_id').val(id);
                $('#edit_trx').val(trx);
                $('#edit_note').val(note);
                $('#editModal').removeClass('hidden');
            };

            window.closeModal = () => $('#editModal').addClass('hidden');

            $('#editForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: `/api/sms-requests/${$('#edit_id').val()}`,
                    method: 'PUT',
                    data: {
                        trx_id: $('#edit_trx').val(),
                        admin_note: $('#edit_note').val()
                    },
                    success: function(res) {
                        showToast("Updated Successfully");
                        closeModal();
                        loadRequests();
                    }
                });
            });

            loadRequests();

            $('#searchBox').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $("#requestContainer tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('#filterStatus').on('change', function() {
                let value = $(this).val().toLowerCase();
                if (!value) {
                    $("#requestContainer tr").show();
                    return;
                }
                $("#requestContainer tr").filter(function() {
                    $(this).toggle($(this).find('.status-pill').text().toLowerCase().indexOf(
                        value) > -1)
                });
            });
        });
    </script>
@endsection
