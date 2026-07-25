@extends('layouts.admin')

@section('title', 'School Approval Management')
@section('page-title', 'Pending School Approvals')

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
                        <h2 class="text-xl font-bold text-gray-800 leading-tight">School Approval Management</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black">Pending School Approvals
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative w-full sm:w-56">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="search" placeholder="Search school..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500" />
                        </div>

                        <select id="filterDate"
                            class="border border-gray-200 px-2 py-2 text-xs w-full sm:w-44 bg-white outline-none cursor-pointer">
                            <option value="">All Dates</option>
                            <option value="latest">Latest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table class="min-w-[1500px] text-[11px]">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>School</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>EIIN</th>
                                <th>Approval</th>
                                <th>Subscription</th>
                                <th>Package</th>
                                <th>Monthly Fee</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="schoolContainer">
                            <tr>
                                <td colspan="10" class="text-center py-4 text-gray-400 italic">Loading schools...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const apiUrl = "/api/schools/pending";

            function showToast(message, type = "success") {
                Toastify({
                    text: message,
                    duration: 2500,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === "success" ? "#10b981" : "#ef4444",
                    stopOnFocus: true
                }).showToast();
            }

            window.loadSchools = function() {
                $.get(apiUrl, {
                    search: $('#search').val(),
                    date_filter: $('#filterDate').val()
                }, function(res) {
                    let html = '';
                    if (res.length === 0) {
                        html =
                            `<tr><td colspan="10" class="text-center py-4 text-gray-400 italic">No pending schools found...</td></tr>`;
                    } else {
                        res.forEach(s => {
                            html += `
                    <tr id="schoolRow${s.id}">
                        <td><img src="/storage/${s.logo}" class="w-10 h-10 border border-gray-100 object-cover" onerror="this.src='https://via.placeholder.com/40'"/></td>
                        <td class="font-semibold">${s.school_name}</td>
                        <td>${s.division}, ${s.district}, ${s.upazila}</td>
                        <td>${s.email}<br>${s.mobile}</td>
                        <td>${s.eiin_number}</td>
                        <td class="capitalize font-medium" id="approvalStatus${s.id}">${s.approval_status}</td>
                        <td class="capitalize text-green-600 font-medium" id="subscriptionStatus${s.id}">${s.subscription_status ?? '-'}</td>
                        <td>${s.package_name ?? '-'}</td>
                        <td>${s.total_payable ? s.total_payable + ' ৳' : '-'}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-1 flex-wrap">
                                <button onclick="updateStatus(${s.id},'approved')" class="action-btn action-active" title="Approve"><i class="fa fa-check"></i></button>
                                <button onclick="updateStatus(${s.id},'rejected')" class="action-btn action-reject" title="Reject"><i class="fa fa-xmark"></i></button>
                                <button onclick="deleteSchool(${s.id})" class="action-btn action-delete" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                        });
                    }
                    $('#schoolContainer').html(html);
                });
            }

            window.updateStatus = function(id, status) {
                Swal.fire({
                    title: status === 'approved' ? 'Approve this school?' : 'Reject this school?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/schools/approve/${id}`,
                            method: 'POST',
                            data: {
                                status: status
                            },
                            success: function() {
                                showToast(`School ${status} successfully`, 'success');
                                $('#approvalStatus' + id).text(status);
                                $('#subscriptionStatus' + id).text(status === 'approved' ?
                                    'active' : 'cancelled');
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message ||
                                    'Failed to update status', 'error');
                            }
                        });
                    }
                });
            }

            window.deleteSchool = function(id) {
                Swal.fire({
                    title: 'Delete this school?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/schools/delete/${id}`,
                            method: 'DELETE',
                            success: function() {
                                showToast('School deleted successfully', 'success');
                                $('#schoolRow' + id).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message ||
                                    'Failed to delete school', 'error');
                            }
                        });
                    }
                });
            }

            loadSchools();
            $('#search, #filterDate').on('keyup change', loadSchools);
        });
    </script>

@endsection
