@extends('layouts.admin')

@section('title', 'Registered Schools')
@section('page-title', 'Approved Schools')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Reset and Layout */
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

        /* Table Styling */
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

        /* Action Buttons */
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
            border-radius: 0 !important;
        }

        .action-btn i {
            font-size: .65rem;
        }

        .action-edit {
            background: #3b82f6;
            color: #fff;
        }

        .action-edit:hover {
            background: #2563eb;
            transform: scale(1.05);
        }

        .action-delete {
            background: #ef4444;
            color: #fff;
        }

        .action-delete:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        /* Modal Inputs & Buttons */
        input,
        select,
        button {
            border-radius: 0 !important;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 leading-tight">Registered Schools</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black">All approved schools and
                            their subscription/package details</p>
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
                                <th>ID Number</th>
                                <th>EIIN</th>
                                <th>Package</th>
                                <th>Duration</th>
                                <th>Total Payable</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="schoolContainer">
                            <tr>
                                <td colspan="12" class="text-center py-4 text-gray-400 italic">Loading registered
                                    schools...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="schoolModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white shadow-2xl w-full max-w-lg p-6 relative">

            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold">School Details</h3>
                <button id="modalCloseBtn"
                    class="h-8 w-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editForm">
                <input type="hidden" id="editSchoolId">

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1">School Name</label>
                        <input type="text" id="editSchoolName"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Email Address</label>
                        <input type="email" id="editEmail"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Mobile Number</label>
                        <input type="text" id="editMobile"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">EIIN Number</label>
                        <input type="text" id="editEIIN"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Division</label>
                        <input type="text" id="editDivision"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">District</label>
                        <input type="text" id="editDistrict"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Upazila</label>
                        <input type="text" id="editUpazila"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1">Village/Area</label>
                        <input type="text" id="editVillage"
                            class="w-full border border-gray-300 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="modalCancelBtn"
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-xs font-bold hover:bg-gray-400 transition-all">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-all">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const apiUrl = "/api/schools/registered";

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

                // Helper to format date to DD/MM/YYYY
                function formatDate(dateString) {
                    if (!dateString || dateString === '-' || dateString === 'N/A') return '-';
                    try {
                        const date = new Date(dateString);
                        // Check if valid date
                        if (isNaN(date.getTime())) return dateString;
                        
                        return new Intl.DateTimeFormat('en-GB').format(date); // DD/MM/YYYY
                    } catch (e) {
                        return dateString;
                    }
                }

                window.loadSchools = function() {
                    $.get(apiUrl, {
                        search: $('#search').val(),
                        date_filter: $('#filterDate').val()
                    }, function(res) {
                        let html = '';
                        if (res.length === 0) {
                            html =
                                '<tr><td colspan="12" class="text-center py-4 text-gray-400 italic">No registered schools found...</td></tr>';
                        }
                        res.forEach(s => {
                            html += `
                <tr id="schoolRow${s.id}">
                    <td><img src="/storage/${s.logo}" class="w-10 h-10 border object-cover" onerror="this.src='https://via.placeholder.com/40'"></td>
                    <td class="font-semibold">${s.school_name}</td>
                    <td>${s.division}, ${s.district}, ${s.upazila}, ${s.village}</td>
                    <td>${s.email}<br>${s.mobile}</td>
                    <td>${s.id_number??'-'}</td>
                    <td>${s.eiin_number}</td>
                    <td>${s.package_type??'-'}</td>
                    <td>${s.duration_months??'-'}</td>
                    <td>${s.total_payable??'-'}</td>
                    <td>${formatDate(s.start_date)}</td>
                    <td>${formatDate(s.expiry_date)}</td>
                    <td class="font-semibold text-green-600">${s.subscription_status??'-'}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-1">
                            <button onclick="openEditModal(${s.id})" class="action-btn action-edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button onclick="deleteSchool(${s.id})" class="action-btn action-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
                        });
                        $('#schoolContainer').html(html);
                    });
                }

                window.openEditModal = function(id) {
                    $.get(`/api/schools/get/${id}`, function(s) {
                        $('#editSchoolId').val(s.id);
                        $('#editSchoolName').val(s.school_name);
                        $('#editEmail').val(s.email);
                        $('#editMobile').val(s.mobile);
                        $('#editEIIN').val(s.eiin_number);
                        $('#editDivision').val(s.division);
                        $('#editDistrict').val(s.district);
                        $('#editUpazila').val(s.upazila);
                        $('#editVillage').val(s.village);
                        $('#schoolModal').removeClass('hidden').addClass('flex');
                    });
                }

                $('#modalCancelBtn, #modalCloseBtn').click(function() {
                    $('#schoolModal').addClass('hidden').removeClass('flex');
                });

                $('#editForm').submit(function(e) {
                    e.preventDefault();
                    let id = $('#editSchoolId').val();
                    let data = {
                        school_name: $('#editSchoolName').val(),
                        email: $('#editEmail').val(),
                        mobile: $('#editMobile').val(),
                        eiin_number: $('#editEIIN').val(),
                        division: $('#editDivision').val(),
                        district: $('#editDistrict').val(),
                        upazila: $('#editUpazila').val(),
                        village: $('#editVillage').val()
                    };

                    $.ajax({
                        url: `/api/schools/update/${id}`,
                        method: 'PUT',
                        data: data,
                        success: function() {
                            showToast('School updated successfully');
                            $('#schoolModal').addClass('hidden').removeClass('flex');
                            loadSchools();
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON?.message || 'Failed', 'error');
                        }
                    });
                });

                window.deleteSchool = function(id) {
                    Swal.fire({
                        title: 'Delete this school?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
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
                                    showToast(xhr.responseJSON?.message || 'Failed', 'error');
                                }
                            });
                        }
                    });
                }

                loadSchools();
                $('#search,#filterDate').on('keyup change', loadSchools);
            });
        </script>
    @endpush

@endsection