@extends('layouts.admin')

@section('title', 'Package Management')
@section('page-title', 'Package Management')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        /* Toastify */
        .toast-success {
            background: #10b981 !important;
            border-radius: 10px !important;
            font-family: inherit !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
        }

        .toast-error {
            background: #ef4444 !important;
            border-radius: 10px !important;
            font-family: inherit !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
        }

        /* Cards & Buttons - No border radius */
        .package-card,
        button,
        select,
        input {
            border-radius: 0 !important;
        }

        .package-card {
            transition: all .3s;
            border: 1px solid #e5e7eb;
            padding: 1rem;
            background: #ffffff;
            margin-bottom: 1rem;
        }

        .package-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }
    </style>

    <div class="p-4 md:p-6 bg-gray-50 min-h-screen">

        <div
            class="bg-white shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Subscription Plans</h2>
                <p class="text-xs text-gray-500">Manage and configure your system packages</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa fa-search text-xs"></i>
                    </span>
                    <input type="text" id="search"
                        class="pl-9 pr-3 py-2 border border-gray-200 text-sm w-full focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Search packages...">
                </div>

                <select id="filterType"
                    class="border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Types</option>
                    <option>Basic</option>
                    <option>Standard</option>
                    <option>Premium</option>
                    <option>Advance</option>
                </select>

                <button id="createBtn"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-medium transition-all">
                    <i class="fa fa-plus-circle"></i> Create Package
                </button>

            </div>
        </div>

        <div id="packageContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 custom-scrollbar"></div>

    </div>

    <div id="packageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white shadow-2xl w-full max-w-lg p-6 relative">

            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold" id="modalTitle">Package Details</h3>
                <button id="modalCloseBtn" class="h-8 w-8 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="packageForm">
                <input type="hidden" id="packageId">

                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <label class="block text-xs font-bold mb-1">Package Type</label>
                        <select id="package_type" class="w-full border border-gray-300 px-2 py-1 text-sm" required>
                            <option value="">Select</option>
                            <option>Basic</option>
                            <option>Standard</option>
                            <option>Premium</option>
                            <option>Advance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Student Limit</label>
                        <input type="number" id="student_limit" class="w-full border border-gray-300 px-2 py-1 text-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Teacher Limit</label>
                        <input type="number" id="teacher_limit" class="w-full border border-gray-300 px-2 py-1 text-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Trial Days</label>
                        <input type="number" id="free_trial_days" class="w-full border border-gray-300 px-2 py-1 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Price / Student</label>
                        <input type="number" step="0.01" id="per_student_price"
                            class="w-full border border-gray-300 px-2 py-1 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Discount %</label>
                        <input type="number" step="0.01" id="annual_discount_percent"
                            class="w-full border border-gray-300 px-2 py-1 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">SMS Limit</label>
                        <input type="number" id="sms_limit" class="w-full border border-gray-300 px-2 py-1 text-sm">
                    </div>

                    <div class="col-span-2 grid grid-cols-2 gap-3 bg-blue-50 p-3 border border-blue-100">
                        <div>
                            <label class="text-[10px] font-bold text-blue-600 uppercase">Total Payable</label>
                            <input type="text" id="total_payable"
                                class="w-full bg-transparent font-bold text-gray-700 text-sm outline-none" readonly>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-blue-600 uppercase">After Discount</label>
                            <input type="text" id="after_discount"
                                class="w-full bg-transparent font-bold text-green-600 text-sm outline-none" readonly>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" id="modalCancelBtn" class="px-4 py-2 bg-gray-300 text-xs">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs">Save</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // CSRF Setup inside ready to ensure meta tag availability
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const apiUrl = "/api/packages";

            // Toast
            function showToast(msg, type = "success") {
                Toastify({
                    text: msg,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === "success" ? "#10b981" : "#ef4444",
                    stopOnFocus: true,
                    className: type === "success" ? "toast-success" : "toast-error"
                }).showToast();
            }

            // Calculation
            function calculate() {
                let student = parseFloat($('#student_limit').val()) || 0;
                let price = parseFloat($('#per_student_price').val()) || 0;
                let discount = parseFloat($('#annual_discount_percent').val()) || 0;

                let total = student * price;
                let after = total - (total * (discount / 100));

                $('#total_payable').val('৳' + total.toFixed(2));
                $('#after_discount').val('৳' + after.toFixed(2));
            }

            // Load Packages
            window.loadPackages = function() {
                $.get(apiUrl, {
                    search: $('#search').val(),
                    type: $('#filterType').val()
                }, function(res) {
                    let packages = res.data || res;
                    let html = '';
                    if (!packages.length) {
                        html =
                            '<div class="col-span-full text-center text-gray-400 italic py-6">No packages found...</div>';
                    } else {
                        packages.forEach(p => {
                            html += `
                    <div class="package-card flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">${p.package_type}</h3>
                            <p class="text-xs text-gray-500 mt-1">${p.free_trial_days||0} Days Trial</p>
                            <p class="text-sm mt-1"><span class="font-semibold">Students:</span> ${p.student_limit}, <span class="font-semibold">Teachers:</span> ${p.teacher_limit}</p>
                            <p class="text-sm mt-1">৳${p.per_student_price} / student</p>
                            <p class="text-xs text-gray-400 line-through">৳${p.total_payable}</p>
                            <p class="text-sm font-bold text-green-600">৳${p.after_discount}</p>
                            <p class="text-xs text-gray-500 mt-1">SMS Limit: ${parseInt(p.sms_limit).toLocaleString()}</p>
                        </div>
                        <div class="flex gap-2 mt-3 md:mt-0">
                            <button onclick="editPackage(${p.id})" class="h-8 w-8 border border-blue-100 text-blue-600 hover:bg-blue-50 flex items-center justify-center"><i class="fas fa-edit text-[10px]"></i></button>
                            <button onclick="deletePackage(${p.id})" class="h-8 w-8 border border-red-100 text-red-600 hover:bg-red-50 flex items-center justify-center"><i class="fas fa-trash text-[10px]"></i></button>
                        </div>
                    </div>`;
                        });
                    }
                    $('#packageContainer').html(html);
                });
            }

            // Initial Load & Event Handlers
            loadPackages();
            $('#search,#filterType').on('keyup change', loadPackages);
            $('#student_limit,#per_student_price,#annual_discount_percent').on('input', calculate);

            $('#createBtn').click(function() {
                $('#modalTitle').text('New Subscription Plan');
                $('#packageForm')[0].reset();
                $('#packageId').val('');
                calculate();
                $('#packageModal').removeClass('hidden').addClass('flex');
            });

            $('#modalCancelBtn, #modalCloseBtn').click(function() {
                $('#packageModal').addClass('hidden').removeClass('flex');
            });

            $('#packageForm').submit(function(e) {
                e.preventDefault();
                let id = $('#packageId').val();
                let method = id ? "PUT" : "POST";
                let url = id ? `${apiUrl}/${id}` : apiUrl;

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        package_type: $('#package_type').val(),
                        student_limit: $('#student_limit').val(),
                        teacher_limit: $('#teacher_limit').val(),
                        free_trial_days: $('#free_trial_days').val(),
                        per_student_price: $('#per_student_price').val(),
                        annual_discount_percent: $('#annual_discount_percent').val() || 0,
                        sms_limit: $('#sms_limit').val() || 0
                    },
                    success: function() {
                        showToast("Package saved successfully!");
                        $('#packageModal').addClass('hidden').removeClass('flex');
                        loadPackages();
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message ||
                            "Check your input and try again.", "error");
                    }
                });
            });

            // Global assignment for onclick handlers
            window.editPackage = function(id) {
                $.get(`${apiUrl}/${id}`, function(p) {
                    $('#modalTitle').text('Modify Plan: ' + p.package_type);
                    $('#packageId').val(p.id);
                    $('#package_type').val(p.package_type);
                    $('#student_limit').val(p.student_limit);
                    $('#teacher_limit').val(p.teacher_limit);
                    $('#free_trial_days').val(p.free_trial_days);
                    $('#per_student_price').val(p.per_student_price);
                    $('#annual_discount_percent').val(p.annual_discount_percent);
                    $('#sms_limit').val(p.sms_limit);
                    calculate();
                    $('#packageModal').removeClass('hidden').addClass('flex');
                });
            };

            window.deletePackage = function(id) {
                Swal.fire({
                    title: 'Delete Plan?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-0 shadow-lg border border-gray-100',
                        confirmButton: 'bg-red-600 px-4 py-2 text-xs text-white mx-1',
                        cancelButton: 'bg-gray-200 px-4 py-2 text-xs text-gray-700 mx-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${apiUrl}/${id}`,
                            method: "DELETE",
                            success: function() {
                                showToast("Package removed.", "success");
                                loadPackages();
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.message ||
                                    "Error deleting package", "error");
                            }
                        });
                    }
                });
            };
        });
    </script>

@endsection
