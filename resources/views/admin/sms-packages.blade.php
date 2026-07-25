@extends('layouts.admin')

@section('title', 'SMS Package Management')
@section('page-title', 'SMS Package Management')

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
                <h2 class="text-xl font-bold text-gray-800">SMS Packages</h2>
                <p class="text-xs text-gray-500">Manage SMS bundles for schools</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa fa-search text-xs"></i>
                    </span>
                    <input type="text" id="search"
                        class="pl-9 pr-3 py-2 border border-gray-200 text-sm w-full focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Search SMS packages...">
                </div>

                <select id="filterDate"
                    class="border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Dates</option>
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                </select>

                <button id="createBtn"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-medium transition-all">
                    <i class="fa fa-plus-circle"></i> Create SMS Package
                </button>

            </div>
        </div>

        <div id="packageContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 custom-scrollbar"></div>

    </div>

    <div id="smsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white shadow-2xl w-full max-w-lg p-6 relative">

            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold" id="modalTitle">SMS Package Details</h3>
                <button id="modalCloseBtn" class="h-8 w-8 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="smsForm">
                <input type="hidden" id="smsPackageId">

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1">Package Name</label>
                        <input type="text" id="name" class="w-full border border-gray-300 px-2 py-1 text-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">SMS Quantity</label>
                        <input type="number" id="sms_quantity" class="w-full border border-gray-300 px-2 py-1 text-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Validity (Days)</label>
                        <input type="number" id="validity_days" class="w-full border border-gray-300 px-2 py-1 text-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Purchase Price</label>
                        <input type="number" step="0.01" id="purchase_price"
                            class="w-full border border-gray-300 px-2 py-1 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Sale Price</label>
                        <input type="number" step="0.01" id="sale_price"
                            class="w-full border border-gray-300 px-2 py-1 text-sm" required>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 gap-3 bg-blue-50 p-3 border border-blue-100">
                        <div>
                            <label class="text-[10px] font-bold text-blue-600 uppercase">Profit Per Package</label>
                            <input type="text" id="profit_preview"
                                class="w-full bg-transparent font-bold text-green-600 text-sm outline-none" readonly>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" id="modalCancelBtn"
                        class="px-4 py-2 bg-gray-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold">Save
                        Package</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // CSRF Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const apiUrl = "/api/sms-packages";

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

            function calculateProfit() {
                let purchase = parseFloat($('#purchase_price').val()) || 0;
                let sale = parseFloat($('#sale_price').val()) || 0;
                $('#profit_preview').val('৳' + (sale - purchase).toFixed(2));
            }

            window.loadPackages = function() {
                $.get(apiUrl, {
                    search: $('#search').val(),
                    date_filter: $('#filterDate').val()
                }, function(res) {
                    let packages = res.data || res;
                    let html = '';
                    if (!packages.length) {
                        html =
                            '<div class="col-span-full text-center text-gray-400 italic py-6">No SMS packages found...</div>';
                    } else {
                        packages.forEach(p => {
                            html += `
                    <div class="package-card flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">${p.name}</h3>
                            <p class="text-xs text-gray-500 mt-1">${p.sms_quantity} SMS Bundles</p>
                            <p class="text-sm mt-1"><span class="font-semibold">Validity:</span> ${p.validity_days} Days</p>
                            <p class="text-sm mt-1"><span class="font-semibold">Buy Price:</span> ৳${p.purchase_price}</p>
                            <p class="text-sm mt-1"><span class="font-semibold">Sale Price:</span> ৳${p.sale_price}</p>
                            <p class="text-sm font-bold text-green-600 mt-1">Profit: ৳${p.profit_per_package || (p.sale_price - p.purchase_price).toFixed(2)}</p>
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

            // Initial Load & Listeners
            loadPackages();
            $('#search, #filterDate').on('keyup change', loadPackages);
            $('#purchase_price, #sale_price').on('input', calculateProfit);

            $('#createBtn').click(function() {
                $('#modalTitle').text('New SMS Package');
                $('#smsForm')[0].reset();
                $('#smsPackageId').val('');
                $('#profit_preview').val('৳0.00');
                $('#smsModal').removeClass('hidden').addClass('flex');
            });

            $('#modalCancelBtn, #modalCloseBtn').click(function() {
                $('#smsModal').addClass('hidden').removeClass('flex');
            });

            $('#smsForm').submit(function(e) {
                e.preventDefault();
                let id = $('#smsPackageId').val();
                let method = id ? "PUT" : "POST";
                let url = id ? `${apiUrl}/${id}` : apiUrl;

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        name: $('#name').val(),
                        sms_quantity: $('#sms_quantity').val(),
                        validity_days: $('#validity_days').val(),
                        purchase_price: $('#purchase_price').val(),
                        sale_price: $('#sale_price').val(),
                    },
                    success: function() {
                        showToast("SMS Package saved successfully!");
                        $('#smsModal').addClass('hidden').removeClass('flex');
                        loadPackages();
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || "Error saving package.",
                        "error");
                    }
                });
            });

            window.editPackage = function(id) {
                $.get(`${apiUrl}/${id}`, function(p) {
                    $('#modalTitle').text('Modify SMS Package');
                    $('#smsPackageId').val(p.id);
                    $('#name').val(p.name);
                    $('#sms_quantity').val(p.sms_quantity);
                    $('#validity_days').val(p.validity_days);
                    $('#purchase_price').val(p.purchase_price);
                    $('#sale_price').val(p.sale_price);
                    calculateProfit();
                    $('#smsModal').removeClass('hidden').addClass('flex');
                });
            };

            window.deletePackage = function(id) {
                Swal.fire({
                    title: 'Delete SMS Package?',
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
                                showToast("SMS Package removed.", "success");
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
