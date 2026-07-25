@extends('layouts.school')

@section('title', 'SMS Bundles')
@section('page-title', 'SMS Bundles')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Global Reset */
        .package-card,
        .balance-card,
        .history-card,
        select,
        input,
        .badge,
        .swal2-popup,
        .swal2-input,
        .swal2-select {
            border-radius: 0 !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        .toast-success {
            background: #10b981 !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }

        .toast-error {
            background: #ef4444 !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
        }

        .package-card {
            transition: all .2s ease-in-out;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            max-width: 300px;
            min-height: 230px;
        }

        .package-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Fixed Select Border & Height for Modal */
        .swal2-select {
            border: 1px solid #d1d5db !important;
            box-shadow: none !important;
            height: 38px !important;
            padding: 0 10px !important;
            font-size: 12px !important;
            width: 100% !important;
            margin: 10px auto !important;
        }

        .swal2-popup {
            width: 450px !important;
            padding: 1.5rem !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* Modal Button Adjustments */
        .swal2-actions {
            flex-direction: column !important;
            width: 100%;
            gap: 10px;
        }

        .swal2-confirm,
        .swal2-cancel {
            width: 100% !important;
            margin: 0 !important;
        }

        @media (min-width: 640px) {
            .swal2-actions {
                flex-direction: row !important;
            }

            .swal2-confirm,
            .swal2-cancel {
                width: auto !important;
            }
        }

        @media (max-width: 640px) {
            .package-card {
                max-width: 100%;
                min-height: auto;
            }

            .swal2-popup {
                width: 95% !important;
                padding: 1.25rem !important;
            }

            .table-responsive-stack tr {
                display: flex;
                flex-direction: column;
                border-bottom: 1px solid #f1f5f9;
                padding: 8px 0;
            }

            .table-responsive-stack td {
                display: flex;
                justify-content: space-between;
                padding: 2px 15px !important;
                border: none !important;
            }

            .table-responsive-stack td::before {
                content: attr(data-label);
                font-weight: 700;
                text-transform: uppercase;
                font-size: 9px;
                color: #94a3b8;
            }

            .table-responsive-stack thead {
                display: none;
            }
        }
    </style>

    <div class="p-3 md:p-6 bg-gray-50 min-h-screen">

        <div
            class="bg-white border border-gray-200 p-3 mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- Dynamic Page Title using the current route name / menu name --}}
                <h2 id="pageHeader" class="text-xl font-bold text-gray-800 leading-tight"></h2>

                <div class="flex items-center text-slate-400 text-xs mt-1">
                    <span>School</span>
                    <i class="fas fa-chevron-right mx-1 text-[8px]"></i>
                    <span id="pageTitle" class="font-medium text-slate-500"></span>
                </div>
            </div>

            <div class="balance-card px-3 py-1.5 flex items-center justify-between sm:justify-start gap-3 w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <div class="text-blue-600">
                        <i class="fas fa-comment-dots text-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-[8px] uppercase font-bold text-gray-400 tracking-tighter leading-none">Available
                            Credits</h6>
                        <h2 class="text-lg font-black text-gray-900 leading-none mt-1" id="current-balance">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <div id="package-container" class="flex flex-wrap gap-3 mb-6">
            <div class="w-full text-center py-10">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-blue-600"></div>
            </div>
        </div>

        <div class="history-card bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-3 border-b border-gray-100 bg-white">
                <h5 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Transaction History</h5>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse table-responsive-stack">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-[9px] uppercase text-gray-500 font-bold tracking-widest">Package</th>
                            <th class="px-3 py-2 text-[9px] uppercase text-gray-500 font-bold tracking-widest">Method</th>
                            <th class="px-3 py-2 text-[9px] uppercase text-gray-500 font-bold tracking-widest">Amount</th>
                            <th class="px-3 py-2 text-[9px] uppercase text-gray-500 font-bold tracking-widest">Status</th>
                            <th class="px-3 py-2 text-[9px] uppercase text-gray-500 font-bold tracking-widest">Activation
                                Date</th>
                            <th class="px-3 py-2 text-right text-[9px] uppercase text-gray-500 font-bold tracking-widest">
                                Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody id="history-container" class="text-xs">
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-400 italic">Fetching...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            fetchPackages();
            fetchBalance();
            fetchHistory();

            function showToast(msg, type = "success") {
                Toastify({
                    text: msg,
                    duration: 2500,
                    gravity: "top",
                    position: "right",
                    className: type === "success" ? "toast-success" : "toast-error",
                }).showToast();
            }

            function fetchPackages() {
                const container = document.getElementById('package-container');
                fetch('/api/sms-packages', {
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('token'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(response => {
                        const packages = response.data || [];
                        if (packages.length === 0) {
                            container.innerHTML =
                                '<div class="w-full text-center py-10 text-gray-400 uppercase text-[9px] tracking-widest">No active bundles.</div>';
                            return;
                        }
                        container.innerHTML = packages.map(pkg => `
                        <div class="package-card p-4 flex flex-col justify-between flex-1 min-w-[260px]">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-black text-gray-900 text-sm uppercase tracking-tight">${pkg.name}</h4>
                                    <i class="fas fa-bolt text-blue-100 text-lg"></i>
                                </div>
                                <div class="mb-4">
                                    <span class="text-xl font-black text-gray-900 tracking-tighter">৳${pkg.sale_price}</span>
                                    <p class="text-[9px] text-blue-600 font-bold uppercase mt-0.5">${pkg.sms_quantity} SMS • ${pkg.validity_days} Days</p>
                                </div>
                            </div>
                            <button onclick="openPaymentModal(${pkg.id}, '${pkg.name}', ${pkg.sale_price})" 
                                class="w-full flex items-center justify-center border-2 border-gray-900 text-gray-900 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-[9px] font-black uppercase tracking-widest transition-all active:scale-95 h-[32px] p-0">
                                Request Activation
                            </button>
                        </div>
                    `).join('');
                    });
            }

            window.openPaymentModal = function(id, name, price) {
                Swal.fire({
                    title: '<span class="text-[14px] font-black uppercase tracking-tight">Payment Method</span>',
                    html: `<p class="text-[10px] text-gray-500 uppercase font-bold mb-2">Package: ${name} | Total: ৳${price}</p>`,
                    input: 'select',
                    inputOptions: {
                        'Cash': 'Cash',
                        'Bank': 'Bank'
                    },
                    inputPlaceholder: 'Select Method',
                    showCancelButton: true,
                    confirmButtonText: 'SUBMIT REQUEST',
                    cancelButtonText: 'CANCEL',
                    customClass: {
                        confirmButton: 'border-2 border-blue-600 bg-blue-600 text-white hover:bg-blue-700 px-6 py-2 text-[10px] font-black uppercase tracking-widest transition-all',
                        cancelButton: 'border-2 border-gray-200 text-gray-500 hover:bg-gray-100 px-6 py-2 text-[10px] font-black uppercase tracking-widest transition-all',
                        input: 'swal2-select',
                        actions: 'swal2-actions'
                    },
                    buttonsStyling: false,
                    preConfirm: (value) => {
                        if (value === 'Bank') {
                            Swal.showValidationMessage(
                                'Bank gateway unavailable. Please select Cash.');
                            return false;
                        }
                        if (!value) {
                            Swal.showValidationMessage('Please select a method');
                            return false;
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value === 'Cash') {
                        submitRequest(id, result.value);
                    }
                });
            }

            function submitRequest(id, method) {
                fetch('/api/purchase-sms', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + localStorage.getItem('token'),
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            sms_package_id: id,
                            payment_method: method
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast("Activation Request Sent");
                            fetchHistory();
                            fetchBalance();
                        } else {
                            showToast(data.message || "Request Error", "error");
                        }
                    });
            }

            function fetchHistory() {
                fetch('/api/sms-purchase-history', {
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then(res => res.json())
                    .then(response => {
                        const container = document.getElementById('history-container');
                        const data = response.data || [];
                        if (data.length === 0) {
                            container.innerHTML =
                                '<tr><td colspan="6" class="text-center py-8 text-gray-400 uppercase text-[9px] font-bold">No history</td></tr>';
                            return;
                        }
                        container.innerHTML = data.map(item => {
                            let sStyle = item.status === 'approved' ?
                                'text-green-600 border-green-200 bg-green-50' :
                                (item.status === 'rejected' ? 'text-red-600 border-red-200 bg-red-50' :
                                    'text-yellow-600 border-yellow-200 bg-yellow-50');

                            const activationDate = item.purchase_date ? new Date(item.purchase_date)
                                .toLocaleDateString('en-GB') : '--';
                            const expiryDate = item.expiry_date ? new Date(item.expiry_date)
                                .toLocaleDateString('en-GB') : 'N/A';

                            return `
                        <tr class="border-b border-gray-50">
                            <td class="px-3 py-2" data-label="Package">
                                <div class="font-bold text-gray-900 uppercase text-[10px] tracking-tight">${item.package?.name || 'Bundle'}</div>
                                <div class="text-[7px] text-gray-400 font-mono uppercase">${item.trx_id}</div>
                            </td>
                            <td class="px-3 py-2" data-label="Method"><span class="text-[8px] font-black uppercase text-gray-500 bg-gray-100 px-1 py-0.5">${item.payment_method}</span></td>
                            <td class="px-3 py-2 font-black text-gray-900" data-label="Amount">৳${item.sale_price}</td>
                            <td class="px-3 py-2" data-label="Status">
                                <span class="text-[8px] font-black uppercase border px-1.5 py-0.5 ${sStyle}">${item.status}</span>
                            </td>
                            <td class="px-3 py-2 text-[9px] text-gray-500 font-bold" data-label="Activation Date">${activationDate}</td>
                            <td class="px-3 py-2 text-right text-[9px] text-gray-900 font-black" data-label="Expiry Date">${expiryDate}</td>
                        </tr>`;
                        }).join('');
                    });
            }

            function fetchBalance() {
                // Pointing to the real school master balance
                fetch('/api/sms-balance', {
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Ensure the ID matches your HTML h2
                            $('#current-balance').text(data.balance.toLocaleString());
                        }
                    });
            }
        });
    </script>

@endsection
