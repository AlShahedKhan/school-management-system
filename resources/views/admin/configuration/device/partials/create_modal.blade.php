{{-- ===================================== --}}
{{-- ADD DEVICE MODAL --}}
{{-- ===================================== --}}
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="deviceForm">
                @csrf
                {{-- MODAL HEADER --}}
                <div class="device-modal-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="modal-title-area">
                            <div class="modal-title-icon"><i class="bi bi-fingerprint"></i></div>
                            <div>
                                <h5>Add New Device</h5>
                                <p>Assign a ZKTeco device to a school.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                {{-- MODAL BODY --}}
                <div class="device-modal-body">
                    {{-- LOCATION --}}
                    <div class="form-section">
                        <div class="section-title"><i class="bi bi-geo-alt"></i><h6>School Location</h6></div>
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Country <span class="required">*</span></label>
                                <select class="form-select" id="country"></select>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Division <span class="required">*</span></label>
                                <select class="form-select" id="division" disabled></select>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">District <span class="required">*</span></label>
                                <select class="form-select" id="district" disabled></select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label class="form-label">Upazila <span class="required">*</span></label>
                                <select class="form-select" id="upazila" disabled></select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label class="form-label">School <span class="required">*</span></label>
                                <select class="form-select" id="school_id" name="school_id" required disabled></select>
                            </div>
                        </div>
                    </div>

                    {{-- DEVICE BASIC INFO --}}
                    <div class="form-section">
                        <div class="section-title"><i class="bi bi-device-ssd"></i><h6>Device Information</h6></div>
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Device Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Example: Main Gate Device" required>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Device Model</label>
                                <input type="text" class="form-control" name="model" placeholder="Example: ZKTeco K40">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number" placeholder="Device serial number">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Device IP <span class="required">*</span></label>
                                <input type="text" class="form-control" name="ip_address" placeholder="192.168.0.150" required>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Port</label>
                                <input type="number" class="form-control" name="port" value="4370" required>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Communication Type</label>
                                <select class="form-select" name="communication_type">
                                    <option value="LAN">LAN</option>
                                    <option value="WiFi">WiFi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- CONNECTION --}}
                    <div class="form-section">
                        <div class="section-title"><i class="bi bi-router"></i><h6>Connection Settings</h6></div>
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Protocol</label>
                                <select class="form-select" name="protocol"><option value="TCP/IP">TCP/IP</option><option value="SDK">SDK</option></select>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Time Zone</label>
                                <select class="form-select" name="time_zone"><option value="Asia/Dhaka">Asia/Dhaka (GMT +6)</option></select>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Sync Interval (Minutes)</label>
                                <input type="number" class="form-control" name="sync_interval" value="5" placeholder="Minutes">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Heartbeat Time (Seconds)</label>
                                <input type="number" class="form-control" name="heartbeat_time" value="60" placeholder="Seconds">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Connection Timeout (Seconds)</label>
                                <input type="number" class="form-control" name="connection_timeout" value="30" placeholder="Seconds">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">Firmware Version</label>
                                <input type="text" class="form-control" name="firmware_version" placeholder="Example: Ver 6.60">
                            </div>
                        </div>
                    </div>

                    {{-- SECURITY --}}
                    <div class="form-section">
                        <div class="section-title"><i class="bi bi-shield-lock"></i><h6>Security & Status</h6></div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">Device Password</label>
                                <input type="password" class="form-control" name="device_password" placeholder="Enter device password">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Device Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="hold">Hold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-save"><i class="bi bi-check2 me-1"></i>Save Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
