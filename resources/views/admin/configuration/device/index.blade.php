@extends('layouts.admin')

@section('title', 'Device Management')

@section('content')

    <div class="device-page">

        {{-- PAGE HEADER --}}
        <div class="page-header">
            <div class="page-title-area">
                <h2>Device Management</h2>
                <p>Manage attendance devices for all registered schools.</p>
            </div>
            <button type="button" class="btn btn-add-device" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                <i class="bi bi-plus-lg"></i>
                Add Device
            </button>
        </div>


        {{-- STATISTICS --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-primary">
                    <i class="bi bi-device-ssd"></i>
                </div>
                <div class="stat-info">
                    {{-- This one was already dynamic --}}
                    <h3>{{ $devices->total() }}</h3>
                    <span>Total Devices</span>
                </div>
            </div>

            {{-- Active Devices Card --}}
            <div class="stat-card">
                <div class="stat-icon stat-success"><i class="bi bi-wifi"></i></div>
                <div class="stat-info">
                    <h3>{{ $activeCount }}</h3>
                    <span>Active Devices</span>
                </div>
            </div>

            {{-- Inactive Devices Card --}}
            <div class="stat-card">
                <div class="stat-icon stat-danger"><i class="bi bi-wifi-off"></i></div>
                <div class="stat-info">
                    <h3>{{ $inactiveCount }}</h3>
                    <span>Inactive Devices</span>
                </div>
            </div>

            {{-- Hold Devices Card --}}
            <div class="stat-card">
                <div class="stat-icon stat-warning"><i class="bi bi-pause-circle"></i></div>
                <div class="stat-info">
                    <h3>{{ $holdCount }}</h3>
                    <span>Hold Devices</span>
                </div>
            </div>
        </div>

        {{-- DEVICE LIST --}}
        <div class="device-card">
            <div class="device-card-header">
                <h5>All Devices</h5>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="deviceSearch" placeholder="Search by device name, school, IP...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table device-table" id="deviceTable">
                    <thead>
                    <tr>
                        <th>Device</th>
                        <th>School</th>
                        <th>IP Address</th>
                        <th>Connection</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="deviceTableBody">
                    @forelse ($devices as $device)
                        <tr id="device-row-{{ $device->id }}">
                            {{-- Device Name & Model --}}
                            <td>
                                <div class="device-name">
                                    <div class="device-avatar">
                                        {{-- Using the communicationIcon accessor from the Device model --}}
                                        <i class="bi {{ $device->communicationIcon }}"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $device->name ?? 'N/A' }}</strong>
                                        <span>{{ $device->model ?? 'Unknown Model' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- School Name --}}
                            <td>{{ $device->school->school_name ?? 'Unassigned' }}</td>

                            {{-- IP Address --}}
                            <td>{{ $device->ip_address }}</td>

                            <td>
                                {{-- Using the connectionStatus accessor from the Device model --}}
                                <span class="status-badge status-{{ $device->connection_status['color'] }}">
                                <span class="status-dot"></span>
                                {{ $device->connection_status['status'] }}
                            </span>
                            </td>

                            {{-- Device Status (Active/Inactive/Hold) --}}
                            <td>
                            <span class="status-badge status-{{ $device->status->value ?? 'inactive' }}">
                                <span class="status-dot"></span>
                                {{ $device->status->label() ?? 'Inactive' }}
                            </span>
                            </td>

                            {{-- Action Buttons --}}
                            <td>
                                <a href="{{ route('admin.configuration.devices.show.page', $device->id) }}" class="action-btn" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="action-btn btn-edit" title="Edit" data-id="{{ $device->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn action-delete btn-delete" title="Delete" data-id="{{ $device->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button class="action-btn btn-instruction" title="Setup Instructions"
                                        data-bs-toggle="modal"
                                        data-bs-target="#instructionModal"
                                        data-device-name="{{ $device->name }}"
                                        data-device-sn="{{ $device->serial_number }}">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-5">
                                <p class="text-muted mb-0">No devices found.</p>
                                <small>Click "Add Device" to get started.</small>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>


            {{-- Pagination Links --}}
            @if ($devices->hasPages())
                <div class="d-flex justify-content-end p-4">
                    {{ $devices->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal partial is included --}}
    @include('admin.configuration.device.partials.create_modal')
    @include('admin.configuration.device.partials.setup_instruction_modal')

    {{-- JavaScript Code --}}

@endsection
@push('scripts') <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deviceModal = new bootstrap.Modal(document.getElementById('addDeviceModal'));
        const deviceForm = document.getElementById('deviceForm');
        const modalTitle = document.querySelector('#addDeviceModal .modal-title-area h5');
        const saveButton = document.querySelector('#deviceForm .btn-save');

        const countrySelect = document.getElementById('country');
        const divisionSelect = document.getElementById('division');
        const districtSelect = document.getElementById('district');
        const upazilaSelect = document.getElementById('upazila');
        const schoolSelect = document.getElementById('school_id');

        const showToast = (message, type = "success") => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({ icon: type, title: message });
        };

        const populateSelect = (select, data, defaultText, isObject = false, selectedValue = null) => {
            select.innerHTML = `<option value="">${defaultText}</option>`;
            if (data && data.length > 0) {
                data.forEach(item => {
                    const value = isObject ? item.id : item;
                    const text = isObject ? item.school_name : item;
                    const option = new Option(text, value);
                    if (selectedValue && value.toString() === selectedValue.toString()) {
                        option.selected = true;
                    }
                    select.add(option);
                });
                select.disabled = false;
            } else {
                select.disabled = true;
            }
        };

        const resetSelects = (...selects) => {
            selects.forEach(select => {
                const defaultText = `Select ${select.id.replace('_', ' ')}`;
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
            });
        };

        const setupCreateModal = () => {
            deviceForm.reset();
            deviceForm.setAttribute('data-mode', 'create');
            deviceForm.removeAttribute('data-id');
            modalTitle.textContent = 'Add New Device';
            saveButton.innerHTML = '<i class="bi bi-check2 me-1"></i>Save Device';

            resetSelects(countrySelect, divisionSelect, districtSelect, upazilaSelect, schoolSelect);
            countrySelect.disabled = false;

            fetch("{{ route('admin.locations.countries') }}")
                .then(res => res.json())
                .then(data => populateSelect(countrySelect, data, 'Select Country'));
        };

        document.querySelector('.btn-add-device').addEventListener('click', setupCreateModal);

        // --- Cascading Dropdowns Logic (for Create mode) ---
        function fetchAndPopulate(url, selectElement, defaultText) {
            fetch(url)
                .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                .then(data => populateSelect(selectElement, data, defaultText))
                .catch(error => {
                    console.error(`Failed to fetch from ${url}:`, error);
                    populateSelect(selectElement, [], defaultText);
                });
        }

        countrySelect.addEventListener('change', function () {
            resetSelects(divisionSelect, districtSelect, upazilaSelect, schoolSelect);
            if (this.value) {
                fetchAndPopulate(`{{ route('admin.locations.divisions') }}?country=${this.value}`, divisionSelect, 'Select Division');
            }
        });

        divisionSelect.addEventListener('change', function () {
            resetSelects(districtSelect, upazilaSelect, schoolSelect);
            if (this.value) {
                fetchAndPopulate(`{{ route('admin.locations.districts') }}?country=${countrySelect.value}&division=${this.value}`, districtSelect, 'Select District');
            }
        });

        districtSelect.addEventListener('change', function () {
            resetSelects(upazilaSelect, schoolSelect);
            if (this.value) {
                fetchAndPopulate(`{{ route('admin.locations.upazilas') }}?country=${countrySelect.value}&division=${divisionSelect.value}&district=${this.value}`, upazilaSelect, 'Select Upazila');
            }
        });

        upazilaSelect.addEventListener('change', function () {
            resetSelects(schoolSelect);
            if (this.value) {
                fetch(`{{ route('admin.locations.schools') }}?country=${countrySelect.value}&division=${divisionSelect.value}&district=${districtSelect.value}&upazila=${this.value}`)
                    .then(res => res.json())
                    .then(data => populateSelect(schoolSelect, data, 'Select School', true));
            }
        });
        // --- End Cascading Dropdowns ---

        // Form Submission (Create and Update) - Unchanged
        deviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const mode = deviceForm.getAttribute('data-mode');
            const id = deviceForm.getAttribute('data-id');
            let url;
            let method = (mode === 'edit') ? 'PUT' : 'POST';

            if (mode === 'edit') {
                let updateUrlTemplate = "{{ route('admin.configuration.devices.update', ['device' => ':id']) }}";
                url = updateUrlTemplate.replace(':id', id);
            } else {
                url = "{{ route('admin.configuration.devices.store') }}";
            }

            const formData = new FormData(deviceForm);
            const data = Object.fromEntries(formData.entries());

            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': method
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json().then(body => ({ status: response.status, body })))
                .then(({ status, body }) => {
                    if (status >= 400) {
                        const errorMessage = body.message || (body.errors ? Object.values(body.errors).flat().join('\n') : 'An unknown error occurred.');
                        showToast(errorMessage, 'error');
                    } else {
                        showToast(body.message, 'success');
                        deviceModal.hide();
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(error => {
                    console.error('Submit Error:', error);
                    showToast('An unexpected network error occurred.', 'error');
                })
                .finally(() => {
                    saveButton.disabled = false;
                    saveButton.innerHTML = (mode === 'edit')
                        ? '<i class="bi bi-check2 me-1"></i>Update Device'
                        : '<i class="bi bi-check2 me-1"></i>Save Device';
                });
        });

        // Edit and Delete button handlers
        document.getElementById('deviceTableBody').addEventListener('click', async function(e) {
            const editBtn = e.target.closest('.btn-edit');
            const deleteBtn = e.target.closest('.btn-delete');

            // Handle Edit - NOW OPTIMIZED
            if (editBtn) {
                const id = editBtn.dataset.id;
                let showUrlTemplate = "{{ route('admin.configuration.devices.show', ['device' => ':id']) }}";
                let fetchUrl = showUrlTemplate.replace(':id', id);

                try {
                    // Single fetch to get all data
                    const response = await fetch(fetchUrl);
                    const { device, dropdowns } = await response.json();

                    deviceForm.reset();
                    deviceForm.setAttribute('data-mode', 'edit');
                    deviceForm.setAttribute('data-id', id);
                    modalTitle.textContent = 'Edit Device';
                    saveButton.innerHTML = '<i class="bi bi-check2 me-1"></i>Update Device';

                    // Populate form with device data
                    for (const key in device) {
                        const field = deviceForm.querySelector(`[name="${key}"]`);
                        if (field) field.value = device[key];
                    }

                    // Populate all dropdowns at once from the single response
                    if (device.school) {
                        const school = device.school;
                        populateSelect(countrySelect, dropdowns.countries, 'Select Country', false, school.country);
                        populateSelect(divisionSelect, dropdowns.divisions, 'Select Division', false, school.division);
                        populateSelect(districtSelect, dropdowns.districts, 'Select District', false, school.district);
                        populateSelect(upazilaSelect, dropdowns.upazilas, 'Select Upazila', false, school.upazila);
                        populateSelect(schoolSelect, dropdowns.schools, 'Select School', true, school.id);
                    }

                    deviceModal.show();
                } catch (error) {
                    console.error("Failed to fetch device for editing:", error);
                    showToast('Could not load device data.', 'error');
                }
            }

            // Handle Delete - Unchanged
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let deleteUrlTemplate = "{{ route('admin.configuration.devices.destroy', ['device' => ':id']) }}";
                        let deleteUrl = deleteUrlTemplate.replace(':id', id);

                        fetch(deleteUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-HTTP-Method-Override': 'DELETE'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.message) {
                                    showToast(data.message, 'success');
                                    document.getElementById(`device-row-${id}`).remove();
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    showToast('Deletion failed.', 'error');
                                }
                            })
                            .catch(err => showToast('Failed to delete device.', 'error'));
                    }
                });
            }
        });

        // Search functionality - Unchanged
        document.getElementById('deviceSearch').addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll('#deviceTableBody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });


        const instructionModal = document.getElementById('instructionModal');
        if (instructionModal) {
            const modalTitle = instructionModal.querySelector('#instructionModalTitle');
            const fullApiUrlEl = instructionModal.querySelector('#fullApiUrl');
            const copyUrlButton = instructionModal.querySelector('#copyUrlButton');

            // Listen for the modal being shown
            instructionModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const deviceName = button.dataset.deviceName;
                const deviceSN = button.dataset.deviceSn;
                const baseUrl = "{{ url('/api/iclock/cdata') }}";
                const fullUrl = `${baseUrl}?SN=${deviceSN}`;

                modalTitle.textContent = `Setup Instructions for "${deviceName}"`;
                fullApiUrlEl.textContent = fullUrl;
            });

            // ==================================================
            // UPDATED: Copy-to-clipboard functionality with fallback
            // ==================================================
            copyUrlButton.addEventListener('click', function() {
                const textToCopy = fullApiUrlEl.textContent;

                // Check if the modern Clipboard API is available (in secure contexts)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        showToast('URL copied to clipboard!', 'success');
                    }).catch(err => {
                        console.error('Modern copy failed: ', err);
                        showToast('Failed to copy URL.', 'error');
                    });
                } else {
                    // Fallback for insecure contexts (like HTTP) or older browsers
                    const textArea = document.createElement("textarea");
                    textArea.value = textToCopy;

                    // Make the textarea invisible and prevent scrolling
                    textArea.style.position = "fixed";
                    textArea.style.top = "-9999px";
                    textArea.style.left = "-9999px";

                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        document.execCommand('copy');
                        showToast('URL copied to clipboard!', 'success');
                    } catch (err) {
                        console.error('Fallback copy failed: ', err);
                        showToast('Failed to copy URL.', 'error');
                    } finally {
                        document.body.removeChild(textArea);
                    }
                }
            });

        }

    });





</script>
@endpush
