@extends('layouts.admin')

@section('title', 'Device Management')

@section('content')

<style>
    :root{
        --primary:#6366f1;
        --primary-dark:#4f46e5;
        --primary-soft:#eef2ff;
        --success:#16a34a;
        --success-soft:#dcfce7;
        --danger:#dc2626;
        --danger-soft:#fee2e2;
        --warning:#d97706;
        --warning-soft:#fef3c7;
        --dark:#111827;
        --text:#374151;
        --muted:#6b7280;
        --border:#e5e7eb;
        --bg:#f8fafc;
    }

    body{
        background:#f8fafc;
    }

    .device-page{
        padding: 8px;
    }

    .page-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        margin-bottom:24px;
    }

    .page-title-area h2{
        margin:0;
        color:var(--dark);
        font-size:26px;
        font-weight:700;
    }

    .page-title-area p{
        margin:6px 0 0;
        color:var(--muted);
        font-size:14px;
    }

    .btn-add-device{
        border:0;
        border-radius:12px;
        background:var(--primary);
        color:#fff;
        padding:11px 18px;
        font-size:14px;
        font-weight:600;
        display:flex;
        align-items:center;
        gap:8px;
        box-shadow:0 8px 20px rgba(99,102,241,.18);
        transition:.2s;
    }

    .btn-add-device:hover{
        background:var(--primary-dark);
        color:#fff;
        transform:translateY(-1px);
    }

    /* Statistics */

    .stats-grid{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:18px;
        margin-bottom:22px;
    }

    .stat-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        padding:20px;
        display:flex;
        align-items:center;
        gap:15px;
        box-shadow:0 5px 20px rgba(15,23,42,.03);
    }

    .stat-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:20px;
    }

    .stat-primary{
        background:var(--primary-soft);
        color:var(--primary);
    }

    .stat-success{
        background:var(--success-soft);
        color:var(--success);
    }

    .stat-danger{
        background:var(--danger-soft);
        color:var(--danger);
    }

    .stat-warning{
        background:var(--warning-soft);
        color:var(--warning);
    }

    .stat-info h3{
        margin:0;
        font-size:22px;
        font-weight:700;
        color:var(--dark);
    }

    .stat-info span{
        color:var(--muted);
        font-size:13px;
    }

    /* Main Card */

    .device-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:18px;
        box-shadow:0 6px 25px rgba(15,23,42,.04);
        overflow:hidden;
    }

    .device-card-header{
        padding:20px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        border-bottom:1px solid var(--border);
    }

    .device-card-header h5{
        margin:0;
        color:var(--dark);
        font-weight:700;
    }

    .search-box{
        width:280px;
        position:relative;
    }

    .search-box i{
        position:absolute;
        top:50%;
        left:14px;
        transform:translateY(-50%);
        color:#9ca3af;
    }

    .search-box input{
        width:100%;
        border:1px solid var(--border);
        border-radius:10px;
        padding:10px 14px 10px 38px;
        outline:none;
        font-size:13px;
    }

    .search-box input:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(99,102,241,.1);
    }

    .device-table{
        margin:0;
    }

    .device-table thead th{
        padding:14px 16px;
        border-bottom:1px solid var(--border);
        background:#f9fafb;
        color:#6b7280;
        font-size:12px;
        font-weight:700;
        text-transform:uppercase;
        white-space:nowrap;
    }

    .device-table tbody td{
        padding:16px;
        vertical-align:middle;
        border-color:#f1f5f9;
        font-size:13px;
        color:var(--text);
    }

    .device-name{
        display:flex;
        align-items:center;
        gap:12px;
    }

    .device-avatar{
        width:42px;
        height:42px;
        border-radius:12px;
        background:var(--primary-soft);
        color:var(--primary);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:18px;
    }

    .device-name strong{
        display:block;
        color:var(--dark);
        font-size:14px;
    }

    .device-name span{
        font-size:12px;
        color:var(--muted);
    }

    .status-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 10px;
        border-radius:100px;
        font-size:12px;
        font-weight:600;
    }

    .status-active{
        background:var(--success-soft);
        color:var(--success);
    }

    .status-inactive{
        background:var(--danger-soft);
        color:var(--danger);
    }

    .status-hold{
        background:var(--warning-soft);
        color:var(--warning);
    }

    .status-dot{
        width:7px;
        height:7px;
        border-radius:50%;
        background:currentColor;
    }

    .action-btn{
        width:34px;
        height:34px;
        border:1px solid var(--border);
        border-radius:9px;
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color:#64748b;
        margin-right:4px;
        transition:.2s;
    }

    .action-btn:hover{
        color:var(--primary);
        border-color:#c7d2fe;
        background:var(--primary-soft);
    }

    .action-delete:hover{
        color:var(--danger);
        background:var(--danger-soft);
        border-color:#fecaca;
    }

    /* Modal */

    .modal-content{
        border:none;
        border-radius:20px;
        overflow:hidden;
        box-shadow:0 30px 80px rgba(15,23,42,.18);
    }

    .device-modal-header{
        padding:22px 25px;
        border-bottom:1px solid var(--border);
        background:#fff;
    }

    .modal-title-area{
        display:flex;
        align-items:center;
        gap:14px;
    }

    .modal-title-icon{
        width:44px;
        height:44px;
        border-radius:12px;
        background:var(--primary-soft);
        color:var(--primary);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:19px;
    }

    .modal-title-area h5{
        margin:0;
        color:var(--dark);
        font-weight:700;
    }

    .modal-title-area p{
        margin:3px 0 0;
        font-size:12px;
        color:var(--muted);
    }

    .device-modal-body{
        padding:25px;
        background:#fbfdff;
        max-height:70vh;
        overflow-y:auto;
    }

    .form-section{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        padding:20px;
        margin-bottom:20px;
    }

    .form-section:last-child{
        margin-bottom:0;
    }

    .section-title{
        display:flex;
        align-items:center;
        gap:9px;
        margin-bottom:18px;
        padding-bottom:12px;
        border-bottom:1px solid #f1f5f9;
    }

    .section-title i{
        color:var(--primary);
    }

    .section-title h6{
        margin:0;
        font-size:14px;
        font-weight:700;
        color:var(--dark);
    }

    .form-label{
        font-size:12px;
        font-weight:600;
        color:#374151;
        margin-bottom:7px;
    }

    .form-label .required{
        color:#ef4444;
    }

    .form-control,
    .form-select{
        min-height:43px;
        border-radius:10px;
        border:1px solid #dfe3ea;
        font-size:13px;
        color:var(--text);
    }

    .form-control:focus,
    .form-select:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(99,102,241,.1);
    }

    .modal-footer{
        padding:18px 25px;
        border-top:1px solid var(--border);
        background:#fff;
    }

    .btn-cancel{
        border:1px solid var(--border);
        background:#fff;
        color:#475569;
        border-radius:10px;
        padding:10px 18px;
        font-size:13px;
        font-weight:600;
    }

    .btn-save{
        border:0;
        background:var(--primary);
        color:#fff;
        border-radius:10px;
        padding:10px 20px;
        font-size:13px;
        font-weight:600;
    }

    .btn-save:hover{
        background:var(--primary-dark);
        color:#fff;
    }

    @media(max-width:991px){
        .stats-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media(max-width:767px){

        .page-header{
            flex-direction:column;
            align-items:flex-start;
        }

        .stats-grid{
            grid-template-columns:1fr;
        }

        .device-card-header{
            flex-direction:column;
            align-items:flex-start;
        }

        .search-box{
            width:100%;
        }
    }
</style>


<div class="device-page">

    {{-- PAGE HEADER --}}
    <div class="page-header">

        <div class="page-title-area">
            <h2>Device Management</h2>
            <p>Manage  devices for all schools.</p>
        </div>

        <button
            type="button"
            class="btn btn-add-device"
            data-bs-toggle="modal"
            data-bs-target="#addDeviceModal"
        >
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
                <h3>0</h3>
                <span>Total Devices</span>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-icon stat-success">
                <i class="bi bi-wifi"></i>
            </div>

            <div class="stat-info">
                <h3>9</h3>
                <span>Active Devices</span>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-icon stat-danger">
                <i class="bi bi-wifi-off"></i>
            </div>

            <div class="stat-info">
                <h3>2</h3>
                <span>Inactive Devices</span>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-icon stat-warning">
                <i class="bi bi-pause-circle"></i>
            </div>

            <div class="stat-info">
                <h3>1</h3>
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

                <input
                    type="text"
                    id="deviceSearch"
                    placeholder="Search device..."
                >
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
                        <th>Last Sync</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>


                <tbody>

                    <tr>

                        <td>

                            <div class="device-name">

                                <div class="device-avatar">
                                    <i class="bi bi-fingerprint"></i>
                                </div>

                                <div>
                                    <strong>Main Gate Device</strong>
                                    <span>ZKTeco K40</span>
                                </div>

                            </div>

                        </td>


                        <td>
                            Astha Model School
                        </td>


                        <td>
                            192.168.0.150
                        </td>


                        <td>
                            <i class="bi bi-router me-1"></i>
                            LAN
                        </td>


                        <td>
                            Today, 10:35 PM
                        </td>


                        <td>

                            <span class="status-badge status-active">
                                <span class="status-dot"></span>
                                Active
                            </span>

                        </td>


                        <td>

                            <button class="action-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="action-btn" title="View">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button class="action-btn action-delete" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>

                    </tr>



                    <tr>

                        <td>

                            <div class="device-name">

                                <div class="device-avatar">
                                    <i class="bi bi-fingerprint"></i>
                                </div>

                                <div>
                                    <strong>Teacher Room Device</strong>
                                    <span>ZKTeco F18</span>
                                </div>

                            </div>

                        </td>


                        <td>
                            Sunrise School
                        </td>


                        <td>
                            192.168.0.151
                        </td>


                        <td>
                            <i class="bi bi-wifi me-1"></i>
                            WiFi
                        </td>


                        <td>
                            Today, 09:42 PM
                        </td>


                        <td>

                            <span class="status-badge status-inactive">
                                <span class="status-dot"></span>
                                Inactive
                            </span>

                        </td>


                        <td>

                            <button class="action-btn">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="action-btn">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button class="action-btn action-delete">
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>

                    </tr>



                    <tr>

                        <td>

                            <div class="device-name">

                                <div class="device-avatar">
                                    <i class="bi bi-fingerprint"></i>
                                </div>

                                <div>
                                    <strong>Student Gate Device</strong>
                                    <span>ZKTeco K50</span>
                                </div>

                            </div>

                        </td>


                        <td>
                            Greenfield Academy
                        </td>


                        <td>
                            192.168.1.100
                        </td>


                        <td>
                            <i class="bi bi-router me-1"></i>
                            LAN
                        </td>


                        <td>
                            Yesterday, 05:20 PM
                        </td>


                        <td>

                            <span class="status-badge status-hold">
                                <span class="status-dot"></span>
                                Hold
                            </span>

                        </td>


                        <td>

                            <button class="action-btn">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="action-btn">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button class="action-btn action-delete">
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- ===================================== --}}
{{-- ADD DEVICE MODAL --}}
{{-- ===================================== --}}

<div
    class="modal fade"
    id="addDeviceModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <form id="deviceForm">


                {{-- MODAL HEADER --}}
                <div class="device-modal-header">

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="modal-title-area">

                            <div class="modal-title-icon">
                                <i class="bi bi-fingerprint"></i>
                            </div>

                            <div>
                                <h5>Add New Device</h5>
                                <p>
                                    Assign a ZKTeco device to a school.
                                </p>
                            </div>

                        </div>


                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>

                </div>



                {{-- MODAL BODY --}}
                <div class="device-modal-body">


                    {{-- LOCATION --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="bi bi-geo-alt"></i>
                            <h6>School Location</h6>
                        </div>


                        <div class="row g-3">


                            {{-- COUNTRY --}}
                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Country
                                    <span class="required">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="country"
                                >

                                    <option value="">
                                        Select Country
                                    </option>

                                    <option value="bangladesh">
                                        Bangladesh
                                    </option>

                                </select>

                            </div>



                            {{-- DIVISION --}}
                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Division
                                    <span class="required">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="division"
                                    disabled
                                >

                                    <option value="">
                                        Select Division
                                    </option>

                                </select>

                            </div>



                            {{-- DISTRICT --}}
                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    District
                                    <span class="required">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="district"
                                    disabled
                                >

                                    <option>
                                        Select District
                                    </option>

                                </select>

                            </div>



                            {{-- UPAZILA --}}
                            <div class="col-lg-6 col-md-6">

                                <label class="form-label">
                                    Upazila
                                    <span class="required">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="upazila"
                                    disabled
                                >

                                    <option>
                                        Select Upazila
                                    </option>

                                </select>

                            </div>



                            {{-- SCHOOL --}}
                            <div class="col-lg-6 col-md-6">

                                <label class="form-label">
                                    School
                                    <span class="required">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="school"
                                    disabled
                                >

                                    <option>
                                        Select School
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>



                    {{-- DEVICE BASIC INFO --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="bi bi-device-ssd"></i>
                            <h6>Device Information</h6>
                        </div>


                        <div class="row g-3">


                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Device Name
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Example: Main Gate Device"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Device Model
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Example: ZKTeco K40"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Serial Number
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Device serial number"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Device IP
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="192.168.0.150"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Port
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    value="4370"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Communication Type
                                </label>

                                <select class="form-select">

                                    <option value="lan">
                                        LAN
                                    </option>

                                    <option value="wifi">
                                        WiFi
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>



                    {{-- CONNECTION --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="bi bi-router"></i>
                            <h6>Connection Settings</h6>
                        </div>


                        <div class="row g-3">


                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Protocol
                                </label>

                                <select class="form-select">

                                    <option>TCP/IP</option>

                                    <option>SDK</option>

                                    <option>Protocol</option>

                                </select>

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Time Zone
                                </label>

                                <select class="form-select">

                                    <option>
                                        Asia/Dhaka (GMT +6)
                                    </option>

                                </select>

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Sync Interval
                                </label>

                                <select class="form-select">

                                    <option>5 Minutes</option>
                                    <option>10 Minutes</option>
                                    <option>15 Minutes</option>
                                    <option>30 Minutes</option>

                                </select>

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Heartbeat Time
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    value="60"
                                    placeholder="Seconds"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Connection Timeout
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    value="30"
                                    placeholder="Seconds"
                                >

                            </div>



                            <div class="col-lg-4 col-md-6">

                                <label class="form-label">
                                    Firmware Version
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Example: Ver 6.60"
                                >

                            </div>

                        </div>

                    </div>



                    {{-- SECURITY --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="bi bi-shield-lock"></i>
                            <h6>Security & Status</h6>
                        </div>


                        <div class="row g-3">


                            <div class="col-lg-6">

                                <label class="form-label">
                                    Device Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    placeholder="Enter device password"
                                >

                            </div>



                            <div class="col-lg-6">

                                <label class="form-label">
                                    Device Status
                                </label>

                                <select class="form-select">

                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>

                                    <option value="hold">
                                        Hold
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="btn btn-save"
                    >
                        <i class="bi bi-check2 me-1"></i>
                        Save Device
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Demo Location Data
    |--------------------------------------------------------------------------
    | Frontend UI only
    */

    const locationData = {

        dhaka: {

            districts: {

                dhaka: {
                    upazilas: [
                        'Dhamrai',
                        'Dohar',
                        'Keraniganj',
                        'Nawabganj',
                        'Savar'
                    ]
                },

                gazipur: {
                    upazilas: [
                        'Gazipur Sadar',
                        'Kaliakair',
                        'Kaliganj',
                        'Kapasia',
                        'Sreepur'
                    ]
                },

                narayanganj: {
                    upazilas: [
                        'Araihazar',
                        'Bandar',
                        'Narayanganj Sadar',
                        'Rupganj',
                        'Sonargaon'
                    ]
                }

            }

        },


        chattogram: {

            districts: {

                chattogram: {
                    upazilas: [
                        'Anwara',
                        'Boalkhali',
                        'Hathazari',
                        'Patiya',
                        'Rangunia'
                    ]
                },

                coxsbazar: {
                    upazilas: [
                        'Chakaria',
                        'Coxs Bazar Sadar',
                        'Ramu',
                        'Teknaf',
                        'Ukhiya'
                    ]
                }

            }

        },


        rajshahi: {

            districts: {

                rajshahi: {
                    upazilas: [
                        'Bagha',
                        'Bagmara',
                        'Charghat',
                        'Godagari',
                        'Paba'
                    ]
                },

                natore: {
                    upazilas: [
                        'Bagatipara',
                        'Baraigram',
                        'Gurudaspur',
                        'Lalpur',
                        'Natore Sadar'
                    ]
                }

            }

        }

    };


    const country = document.getElementById('country');
    const division = document.getElementById('division');
    const district = document.getElementById('district');
    const upazila = document.getElementById('upazila');
    const school = document.getElementById('school');


    /*
    |--------------------------------------------------------------------------
    | Country Change
    |--------------------------------------------------------------------------
    */

    country.addEventListener('change', function () {

        division.innerHTML =
            '<option value="">Select Division</option>';

        district.innerHTML =
            '<option value="">Select District</option>';

        upazila.innerHTML =
            '<option value="">Select Upazila</option>';

        school.innerHTML =
            '<option value="">Select School</option>';


        if(this.value === 'bangladesh'){

            const divisions = {
                dhaka: 'Dhaka',
                chattogram: 'Chattogram',
                rajshahi: 'Rajshahi',
                khulna: 'Khulna',
                barishal: 'Barishal',
                sylhet: 'Sylhet',
                rangpur: 'Rangpur',
                mymensingh: 'Mymensingh'
            };


            Object.entries(divisions).forEach(([value, name]) => {

                division.innerHTML +=
                    `<option value="${value}">${name}</option>`;

            });


            division.disabled = false;

        }

    });



    /*
    |--------------------------------------------------------------------------
    | Division Change
    |--------------------------------------------------------------------------
    */

    division.addEventListener('change', function () {

        district.innerHTML =
            '<option value="">Select District</option>';

        upazila.innerHTML =
            '<option value="">Select Upazila</option>';

        school.innerHTML =
            '<option value="">Select School</option>';


        upazila.disabled = true;
        school.disabled = true;


        const selectedDivision = locationData[this.value];


        if(!selectedDivision){

            district.disabled = true;
            return;

        }


        Object.keys(selectedDivision.districts).forEach(function(key){

            let districtName = key
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, str => str.toUpperCase());


            if(key === 'coxsbazar'){
                districtName = "Cox's Bazar";
            }


            district.innerHTML +=
                `<option value="${key}">
                    ${districtName}
                </option>`;

        });


        district.disabled = false;

    });



    /*
    |--------------------------------------------------------------------------
    | District Change
    |--------------------------------------------------------------------------
    */

    district.addEventListener('change', function () {

        upazila.innerHTML =
            '<option value="">Select Upazila</option>';

        school.innerHTML =
            '<option value="">Select School</option>';

        school.disabled = true;


        const divisionValue = division.value;
        const districtValue = this.value;


        const districtData =
            locationData[divisionValue]?.districts[districtValue];


        if(!districtData){

            upazila.disabled = true;
            return;

        }


        districtData.upazilas.forEach(function(item){

            upazila.innerHTML +=
                `<option value="${item}">
                    ${item}
                </option>`;

        });


        upazila.disabled = false;

    });



    /*
    |--------------------------------------------------------------------------
    | Upazila Change
    |--------------------------------------------------------------------------
    */

    upazila.addEventListener('change', function () {

        school.innerHTML =
            '<option value="">Select School</option>';


        if(this.value){

            school.innerHTML += `
                <option value="1">
                    Astha Model School
                </option>

                <option value="2">
                    Sunrise School & College
                </option>

                <option value="3">
                    Greenfield Academy
                </option>
            `;


            school.disabled = false;

        }

    });



    /*
    |--------------------------------------------------------------------------
    | Demo Form Submit
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('deviceForm')
        .addEventListener('submit', function(e){

            e.preventDefault();

            alert(
                'UI Demo Only - Device information is not saved yet.'
            );

        });



    /*
    |--------------------------------------------------------------------------
    | Device Search
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('deviceSearch')
        .addEventListener('keyup', function(){

            let value = this.value.toLowerCase();

            let rows =
                document.querySelectorAll('#deviceTable tbody tr');


            rows.forEach(function(row){

                row.style.display =
                    row.innerText
                        .toLowerCase()
                        .includes(value)
                    ? ''
                    : 'none';

            });

        });

</script>

@endsection