@extends('layouts.school')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- CENTER WRAPPER -->
    <div class="min-h-[80vh] flex items-center justify-center px-3">

        <div class="w-full max-w-3xl">

            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 text-center sm:text-left">
                    Current Subscription Plan
                </h2>

                <!-- Subscription Cards -->
                <div id="subscriptionCard" class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div class="bg-green-50 p-3 rounded-lg shadow-sm flex flex-col">
                        <h3 class="text-xs font-semibold text-green-700 mb-1">Package</h3>
                        <p id="packageName" class="text-sm truncate">Loading...</p>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg shadow-sm flex flex-col">
                        <h3 class="text-xs font-semibold text-blue-700 mb-1">Start Date</h3>
                        <p id="startDate" class="text-sm">Loading...</p>
                    </div>

                    <div class="bg-yellow-50 p-3 rounded-lg shadow-sm flex flex-col">
                        <h3 class="text-xs font-semibold text-yellow-700 mb-1">Expiry Date</h3>
                        <p id="expiryDate" class="text-sm">Loading...</p>
                    </div>

                    <div class="bg-red-50 p-3 rounded-lg shadow-sm flex flex-col">
                        <h3 class="text-xs font-semibold text-red-700 mb-1">Days Remaining</h3>
                        <p id="daysRemaining" class="text-sm">Loading...</p>
                    </div>

                    <div
                        class="bg-gray-50 p-3 rounded-lg shadow-sm col-span-full flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <h3 class="text-xs font-semibold mb-1 sm:mb-0">Status</h3>
                        <p id="status" class="text-sm">Loading...</p>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-center">
                    <button id="renewBtn"
                        class="flex items-center justify-center gap-2 text-green-600 border border-green-600 px-3 py-1.5 text-sm rounded hover:bg-green-50 transition w-full sm:w-auto">
                        <i class="mdi mdi-refresh text-sm"></i> Renew
                    </button>

                    <button id="changeBtn"
                        class="flex items-center justify-center gap-2 text-blue-600 border border-blue-600 px-3 py-1.5 text-sm rounded hover:bg-blue-50 transition w-full sm:w-auto">
                        <i class="mdi mdi-swap-horizontal text-sm"></i> Change Plan
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= RENEW MODAL ================= -->
    <div id="renewModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50 p-3">
        <div class="bg-white rounded-lg p-4 max-w-md w-full shadow-lg">
            <h3 class="text-base font-semibold mb-3">Renew Subscription</h3>

            <label class="block text-xs mb-1">Start Date</label>
            <input type="date" id="renewStartDate" class="w-full border rounded p-1.5 text-sm mb-2">

            <label class="block text-xs mb-1">Duration</label>
            <select id="renewDuration" class="w-full border rounded p-1.5 text-sm mb-2">
                <option value="2">2 Months</option>
                <option value="4">4 Months</option>
                <option value="6">6 Months</option>
            </select>

            <label class="block text-xs mb-1">Expiry Date</label>
            <input type="text" id="renewExpiryDate" class="w-full border rounded p-1.5 text-sm mb-3 bg-gray-100"
                readonly>

            <div class="flex flex-col sm:flex-row justify-end gap-2">
                <button id="renewCancel" class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300 w-full sm:w-auto">
                    Cancel
                </button>
                <button id="renewConfirm"
                    class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center gap-1 w-full sm:w-auto">
                    <i class="mdi mdi-check-circle text-sm"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- ================= CHANGE PLAN MODAL ================= -->
    <div id="changeModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50 p-3">
        <div class="bg-white rounded-lg p-4 max-w-md w-full shadow-lg">
            <h3 class="text-base font-semibold mb-3">Change Plan</h3>

            <label class="block text-xs mb-1">Package</label>
            <select id="newPackage" class="w-full border rounded p-1.5 text-sm mb-2"></select>

            <label class="block text-xs mb-1">Start Date</label>
            <input type="date" id="changeStartDate" class="w-full border rounded p-1.5 text-sm mb-2">

            <label class="block text-xs mb-1">Duration</label>
            <select id="changeDuration" class="w-full border rounded p-1.5 text-sm mb-2">
                <option value="2">2 Months</option>
                <option value="4">4 Months</option>
                <option value="6">6 Months</option>
            </select>

            <label class="block text-xs mb-1">Expiry Date</label>
            <input type="text" id="changeExpiryDate" class="w-full border rounded p-1.5 text-sm mb-3 bg-gray-100"
                readonly>

            <div class="flex flex-col sm:flex-row justify-end gap-2">
                <button id="changeCancel" class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300 w-full sm:w-auto">
                    Cancel
                </button>
                <button id="changeConfirm"
                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center gap-1 w-full sm:w-auto">
                    <i class="mdi mdi-check-circle text-sm"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- JS (UNCHANGED — EXACT SAME LOGIC) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            axios.defaults.headers.common['X-CSRF-TOKEN'] =
                document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let currentSubscriptionId = null;
            let currentPackageId = null;

            const renewModal = document.getElementById('renewModal');
            const changeModal = document.getElementById('changeModal');

            const renewStart = document.getElementById('renewStartDate');
            const renewDuration = document.getElementById('renewDuration');
            const renewExpiry = document.getElementById('renewExpiryDate');

            const changeStart = document.getElementById('changeStartDate');
            const changeDuration = document.getElementById('changeDuration');
            const changeExpiry = document.getElementById('changeExpiryDate');
            const newPackage = document.getElementById('newPackage');

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function calculateExpiry(start, months, field) {
                if (!start) return;
                let date = new Date(start);
                date.setMonth(date.getMonth() + parseInt(months));
                field.value = date.toISOString().split('T')[0];
            }

            function fetchSubscription() {
                axios.get('/api/current-subscription').then(res => {
                    const s = res.data.subscription;
                    if (!s) return;

                    currentSubscriptionId = s.id;
                    currentPackageId = s.package_id || null;

                    document.getElementById('packageName').textContent = s.package_name;
                    document.getElementById('startDate').textContent = s.start_date;
                    document.getElementById('expiryDate').textContent = s.expires_at;
                    document.getElementById('daysRemaining').textContent = s.days_remaining + " Days";
                    document.getElementById('status').textContent = s.status;

                    renewStart.value = s.start_date_raw;
                    changeStart.value = s.start_date_raw;
                });
            }

            fetchSubscription();

            document.getElementById('renewBtn').onclick = () => openModal(renewModal);
            document.getElementById('renewCancel').onclick = () => closeModal(renewModal);

            document.getElementById('changeBtn').onclick = () => {
                openModal(changeModal);
                axios.get('/api/school/packages-fetch').then(res => {
                    newPackage.innerHTML = '';
                    res.data.packages.forEach(p => {
                        const selected = p.id == currentPackageId ? 'selected' : '';
                        newPackage.innerHTML +=
                            `<option value="${p.id}" ${selected}>${p.package_type} - ${p.student_limit} Students</option>`;
                    });
                });
            };

            document.getElementById('changeCancel').onclick = () => closeModal(changeModal);

            renewStart.onchange = renewDuration.onchange =
                () => calculateExpiry(renewStart.value, renewDuration.value, renewExpiry);

            changeStart.onchange = changeDuration.onchange =
                () => calculateExpiry(changeStart.value, changeDuration.value, changeExpiry);

            document.getElementById('renewConfirm').onclick = () => {
                if (!currentSubscriptionId)
                    return Swal.fire('Error', 'No active subscription', 'error');

                axios.post(`/api/subscription/renew/${currentSubscriptionId}`, {
                    duration: renewDuration.value,
                    start_date: renewStart.value
                }).then(res => {
                    Swal.fire('Success', res.data.message, 'success');
                    closeModal(renewModal);
                    fetchSubscription();
                }).catch(err =>
                    Swal.fire('Error', err.response?.data?.message || 'Failed', 'error')
                );
            };

            document.getElementById('changeConfirm').onclick = () => {
                if (!currentSubscriptionId)
                    return Swal.fire('Error', 'No active subscription', 'error');

                axios.post(`/api/school/subscription/change/${currentSubscriptionId}`, {
                    package_id: newPackage.value,
                    duration: changeDuration.value,
                    start_date: changeStart.value
                }).then(res => {
                    Swal.fire('Success', res.data.message, 'success');
                    closeModal(changeModal);
                    fetchSubscription();
                }).catch(err =>
                    Swal.fire('Error', err.response?.data?.message || 'Failed', 'error')
                );
            };

        });
    </script>
@endsection
