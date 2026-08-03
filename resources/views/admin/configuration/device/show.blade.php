@extends('layouts.admin')

@section('title', 'Device Details: ' . $device->name)
@section('page-title', 'Device Details')

@section('content')
    <div class="device-page mb-4">

        {{-- PAGE HEADER --}}
        <div class="page-header d-flex justify-content-between mb-4">
            <div class="page-title-area">
                <h2 class="mb-0">Device: {{ $device->name }}</h2>
                <p class="text-muted">Detailed information for the selected device.</p>
            </div>
            <a href="{{ route('admin.configuration.devices.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Device List
            </a>
        </div>

        <div class="row g-4">
            {{-- Left Column: Device Info & Connection --}}
            <div class="col-lg-7">
                <div class="details-card">
                    <div class="details-header">
                        <h5><i class="bi bi-fingerprint me-2"></i>Device Information</h5>
                    </div>
                    <div class="details-body">
                        <div class="detail-item">
                            <span class="detail-label">Device Name</span>
                            <span class="detail-value">{{ $device->name ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Model</span>
                            <span class="detail-value">{{ $device->model ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Serial Number</span>
                            <span class="detail-value">{{ $device->serial_number ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Firmware Version</span>
                            <span class="detail-value">{{ $device->firmware_version ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="details-card mt-4">
                    <div class="details-header">
                        <h5><i class="bi bi-router me-2"></i>Connection Details</h5>
                    </div>
                    <div class="details-body">
                        {{-- NEW: Live Connection Status --}}
                        <div class="detail-item">
                            <span class="detail-label">Connection Status</span>
                            <span class="detail-value">
                                <span class="status-badge status-{{ $device->connection_status['color'] }}">
                                    <span class="status-dot"></span>
                                    {{ $device->connection_status['status'] }}
                                </span>
                            </span>
                        </div>
                        {{-- NEW: Last Heartbeat Time --}}
                        <div class="detail-item">
                            <span class="detail-label">Last Heartbeat</span>
                            <span class="detail-value">
                                @if($device->last_heartbeat_at)
                                    {{ $device->last_heartbeat_at->format('d M Y, h:i A') }}
                                    <small class="d-block text-muted">({{ $device->last_heartbeat_at->diffForHumans() }})</small>
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">IP Address</span>
                            <span class="detail-value">{{ $device->ip_address }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Port</span>
                            <span class="detail-value">{{ $device->port }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Communication Type</span>
                            <span class="detail-value">{{ $device->communication_type ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Protocol</span>
                            <span class="detail-value">{{ $device->protocol ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Sync Interval</span>
                            <span class="detail-value">{{ $device->sync_interval ? $device->sync_interval . ' minutes' : 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Heartbeat Time</span>
                            <span class="detail-value">{{ $device->heartbeat_time ? $device->heartbeat_time . ' seconds' : 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Connection Timeout</span>
                            <span class="detail-value">{{ $device->connection_timeout ? $device->connection_timeout . ' seconds' : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: School & Status --}}
            <div class="col-lg-5">
                <div class="details-card">
                    <div class="details-header">
                        <h5><i class="bi bi-building me-2"></i>Assigned School</h5>
                    </div>
                    <div class="details-body">
                        @if($device->school)
                            <div class="detail-item">
                                <span class="detail-label">School Name</span>
                                <span class="detail-value">{{ $device->school->school_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">{{ $device->school->upazila }}, {{ $device->school->district }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Contact</span>
                                <span class="detail-value">{{ $device->school->mobile }}</span>
                            </div>
                        @else
                            <p class="text-muted text-center">This device is not assigned to any school.</p>
                        @endif
                    </div>
                </div>

                <div class="details-card mt-4">
                    <div class="details-header">
                        <h5><i class="bi bi-shield-check me-2"></i>Status & Settings</h5>
                    </div>
                    <div class="details-body">
                        <div class="detail-item">
                            <span class="detail-label">Device Status</span>
                            <span class="detail-value">
                                <span class="status-badge status-{{ $device->status->value ?? 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $device->status->label() ?? 'Inactive' }}
                                </span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Time Zone</span>
                            <span class="detail-value">{{ $device->time_zone ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
