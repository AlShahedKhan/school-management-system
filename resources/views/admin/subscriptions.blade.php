@extends('layouts.admin')

@section('title', 'School Subscriptions Management')
@section('page-title', 'Active School Subscriptions')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
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
            letter-spacing: 0.025em;
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

        .action-active {
            background: #10b981;
            color: #fff;
        }

        .action-cancel {
            background: #f97316;
            color: #fff;
        }

        .action-edit {
            background: #3b82f6;
            color: #fff;
        }

        .action-upgrade {
            background: #8b5cf6;
            color: #fff;
        }

        .action-delete {
            background: #6b7280;
            color: #fff;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        .toast-success {
            background: #10b981 !important;
            border-radius: 10px !important;
        }

        .toast-error {
            background: #ef4444 !important;
            border-radius: 10px !important;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">School Subscriptions</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black">Full Management of school
                            database records</p>
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
                    <table class="min-w-[1800px] text-[10px]">
                        <thead>
                            <tr>
                                <th>School Information</th>
                                <th>Main Package</th>
                                <th>Upgrade Type</th>
                                <th>Duration</th>
                                <th>User Limits</th>
                                <th>Pricing Structure</th>
                                <th>Final Payable</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Special Dates</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subscriptionContainer">
                            <tr>
                                <td colspan="12" class="text-center py-4 text-gray-400 italic">Loading subscriptions...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white shadow-2xl w-full max-w-lg p-6 relative">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold">Edit Subscription Details</h3>
                <button onclick="closeModal('editModal')" class="text-gray-500 hover:bg-gray-200 p-2"><i
                        class="fas fa-times"></i></button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editSubId">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1">Subscription Package</label>
                        <select name="package_id" id="editPackage"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-blue-500"
                            required></select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Start Date</label>
                        <input type="date" name="start_date" id="editStartDate"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" id="editExpiryDate"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1">Total Payable Price (৳)</label>
                        <input type="number" step="0.01" name="final_price" id="editFinalPrice"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('editModal')"
                        class="px-4 py-2 bg-gray-300 text-xs font-semibold">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Upgrade Modal --}}
    <div id="upgradeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white shadow-2xl w-full max-w-xl p-6 relative">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold">Package Custom Upgrade</h3>
                    <p class="text-[10px] text-purple-600 font-bold uppercase tracking-tighter">Modify parameters for
                        specific school</p>
                </div>
                <button onclick="closeModal('upgradeModal')" class="text-gray-500 hover:bg-gray-200 p-2"><i
                        class="fas fa-times"></i></button>
            </div>

            <form id="upgradeForm">
                <input type="hidden" id="upgradeSubId">
                <div class="mb-4">
                    <label class="block text-xs font-bold mb-1 text-gray-600">Choose Upgrade Category</label>
                    <select id="upgradeCategory" name="upgrade_category"
                        class="w-full border-2 border-purple-200 px-3 py-2 text-sm font-bold outline-none focus:border-purple-500"
                        required>
                        <option value="">Select Category</option>
                        <option value="sale">Sale (One-time)</option>
                        <option value="contract">Contract (Custom Agreement)</option>
                        <option value="subscription">Subscription (Per Unit)</option>
                    </select>
                </div>

                <div id="upgradeFields" class="grid grid-cols-2 gap-3 hidden">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold mb-1">Student Limit</label>
                        <input type="number" name="student_limit"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-purple-500 calc-trigger"
                            required>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs font-bold mb-1">Teacher Limit</label>
                        <input type="number" name="teacher_limit"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-purple-500"
                            required>
                    </div>

                    <div class="col-span-1 sale-field hidden">
                        <label class="block text-xs font-bold mb-1 text-green-600">Sale Amount (৳)</label>
                        <input type="number" name="sale_amount"
                            class="w-full border border-green-300 px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-1 sale-field hidden">
                        <label class="block text-xs font-bold mb-1">Sale Date</label>
                        <input type="date" name="sale_date"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none">
                    </div>

                    <div class="col-span-1 contract-field subscription-field hidden">
                        <label class="block text-xs font-bold mb-1">Per Student Price (৳)</label>
                        <input type="number" step="0.01" name="per_student"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none calc-trigger">
                    </div>
                    <div class="col-span-1 contract-field subscription-field hidden">
                        <label class="block text-xs font-bold mb-1 text-blue-600">Total Payable (৳)</label>
                        <input type="number" step="0.01" name="total_payable" id="auto_total"
                            class="w-full border border-blue-200 bg-blue-50 px-2 py-1 text-sm font-bold outline-none"
                            readonly>
                    </div>

                    <div class="col-span-1 contract-field hidden">
                        <label class="block text-xs font-bold mb-1">Contract Start Date</label>
                        <input type="date" name="contract_start"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-1 contract-field hidden">
                        <label class="block text-xs font-bold mb-1">Contract Close Date</label>
                        <input type="date" name="contract_close"
                            class="w-full border border-gray-300 px-2 py-1 text-sm outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('upgradeModal')"
                        class="px-4 py-2 bg-gray-300 text-xs font-semibold">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700">Confirm
                        Upgrade</button>
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

            const apiUrl = "/api/subscriptions";

            function formatDate(dateStr) {
                if (!dateStr || dateStr === 'null') return '-';
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return '-';
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                return `${day}/${month}/${year}`;
            }

            function showToast(msg, type = "success") {
                Toastify({
                    text: msg,
                    duration: 2500,
                    close: true,
                    gravity: "top",
                    position: "right",
                    className: type === "success" ? "toast-success" : "toast-error"
                }).showToast();
            }

            window.loadSubscriptions = function() {
                $.get(apiUrl, {
                    search: $('#search').val(),
                    date_filter: $('#filterDate').val()
                }, function(res) {
                    let html = '';
                    if (res.length === 0) {
                        html =
                            '<tr><td colspan="12" class="text-center py-4 text-gray-400 italic">No subscriptions found...</td></tr>';
                    } else {
                        res.forEach(s => {
                            // Logic for dynamic date column
                            let specialDateHtml = '<span class="text-gray-400">Standard</span>';
                            if (s.upgrade_type === 'sale') {
                                specialDateHtml =
                                    `Sale Date: <span class="font-bold text-green-600">${formatDate(s.sale_date)}</span>`;
                            } else if (s.upgrade_type === 'contract') {
                                specialDateHtml =
                                    `Close Date: <span class="font-bold text-blue-600">${formatDate(s.contract_close_date)}</span>`;
                            }

                            html += `
                            <tr id="subRow${s.id}">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <img src="/storage/${s.logo}" class="w-8 h-8 border object-cover" onerror="this.src='https://via.placeholder.com/40'">
                                        <div>
                                            <div class="font-bold text-gray-800">${s.school_name}</div>
                                            <div class="text-[9px] text-gray-500">${s.district}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-semibold text-blue-600">${s.package_type ?? 'N/A'}</td>
                                <td class="capitalize font-medium">${s.upgrade_type ?? 'Standard'}</td>
                                <td>${s.duration_months} Months</td>
                                <td>
                                    Students: <span class="font-bold">${s.student_limit}</span><br>
                                    Teachers: <span class="font-bold">${s.teacher_limit}</span>
                                </td>
                                <td>
                                    Original: ${s.original_price} ৳<br>
                                    Discount: ${s.discount_percent}%
                                    ${s.per_student_price ? `<br>Per-Student: ${s.per_student_price} ৳` : ''}
                                </td>
                                <td class="font-black text-gray-900">${s.final_price ?? s.total_payable} ৳</td>
                                <td>${formatDate(s.start_date)}</td>
                                <td>${formatDate(s.expiry_date)}</td>
                                <td>${specialDateHtml}</td>
                                <td id="subStatus${s.id}">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase ${s.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                        ${s.status}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center gap-1 flex-wrap">
                                        <button onclick="updateStatus(${s.id},'active')" class="action-btn action-active" title="Activate"><i class="fa fa-check"></i></button>
                                        <button onclick="updateStatus(${s.id},'cancelled')" class="action-btn action-cancel" title="Cancel"><i class="fa fa-xmark"></i></button>
                                        <button onclick="editSubscription(${s.id})" class="action-btn action-edit" title="Edit"><i class="fa fa-pen"></i></button>
                                        <button onclick="openUpgradeModal(${s.id})" class="action-btn action-upgrade" title="Upgrade"><i class="fa fa-arrow-up"></i></button>
                                        <button onclick="deleteSubscription(${s.id})" class="action-btn action-delete" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#subscriptionContainer').html(html);
                });
            }

            window.updateStatus = function(id, status) {
                $.post(`/api/subscriptions/status/${id}`, {
                    status: status
                }, function() {
                    showToast(`Subscription ${status}`, 'success');
                    loadSubscriptions();
                });
            }

            window.deleteSubscription = function(id) {
                if (confirm('Are you sure you want to delete this subscription?')) {
                    $.ajax({
                        url: `/api/subscriptions/${id}`,
                        type: 'DELETE',
                        success: function() {
                            showToast('Deleted Successfully', 'success');
                            loadSubscriptions();
                        }
                    });
                }
            }

            window.openUpgradeModal = function(id) {
                $('#upgradeSubId').val(id);
                $('#upgradeForm')[0].reset();
                $('#upgradeFields').addClass('hidden');
                $('#upgradeModal').removeClass('hidden').addClass('flex');
            }

            $('#upgradeCategory').change(function() {
                const cat = $(this).val();
                if (!cat) {
                    $('#upgradeFields').addClass('hidden');
                    return;
                }
                $('#upgradeFields').removeClass('hidden');
                $('.sale-field, .contract-field, .subscription-field').addClass('hidden');

                if (cat === 'sale') $('.sale-field').removeClass('hidden');
                if (cat === 'contract') $('.contract-field').removeClass('hidden');
                if (cat === 'subscription') $('.subscription-field').removeClass('hidden');

                calculateTotal();
            });

            $('.calc-trigger').on('input', calculateTotal);

            function calculateTotal() {
                const qty = parseFloat($('input[name="student_limit"]').val()) || 0;
                const price = parseFloat($('input[name="per_student"]').val()) || 0;
                $('#auto_total').val((qty * price).toFixed(2));
            }

            $('#upgradeForm').submit(function(e) {
                e.preventDefault();
                $.post(`/api/subscriptions/upgrade/${$('#upgradeSubId').val()}`, $(this).serialize(),
                    function() {
                        showToast('Package Upgraded Successfully!', 'success');
                        closeModal('upgradeModal');
                        loadSubscriptions();
                    }).fail(err => {
                    showToast(err.responseJSON?.message || 'Upgrade Failed', 'error');
                });
            });

            window.closeModal = function(modalId) {
                $(`#${modalId}`).addClass('hidden').removeClass('flex');
            }

            window.editSubscription = function(id) {
                $.get(`/api/subscriptions/${id}/edit`, function(res) {
                    $('#editSubId').val(res.id);
                    $('#editStartDate').val(res.start_date ? res.start_date.split('T')[0] : '');
                    $('#editExpiryDate').val(res.expiry_date ? res.expiry_date.split('T')[0] : '');
                    $('#editFinalPrice').val(res.final_price);

                    $.get('/api/packages-fetch', function(packages) {
                        $('#editPackage').empty();
                        packages.forEach(p => {
                            const selected = p.id == res.package_id ? 'selected' : '';
                            $('#editPackage').append(
                                `<option value="${p.id}" ${selected}>${p.package_type}</option>`
                            );
                        });
                        $('#editModal').removeClass('hidden').addClass('flex');
                    });
                });
            }

            $('#editForm').submit(function(e) {
                e.preventDefault();
                $.post(`/api/subscriptions/${$('#editSubId').val()}/update`, $(this).serialize(),
                    function() {
                        showToast('Updated Successfully', 'success');
                        closeModal('editModal');
                        loadSubscriptions();
                    });
            });

            loadSubscriptions();
            $('#search, #filterDate').on('keyup change', loadSubscriptions);
        });
    </script>
@endsection
