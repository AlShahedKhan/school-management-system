@extends('layouts.admin')

@section('title', 'Teacher Configuration')

@section('page-title', 'Teacher Configuration')

@section('content')

<style>

    .teacher-config-page {
        padding: 8px 0 30px;
    }

    .teacher-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .teacher-page-header h2 {
        font-size: 25px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .teacher-page-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .btn-add-teacher {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 11px 17px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-add-teacher:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
    }


    /* Stats */

    .teacher-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .teacher-stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 15px rgba(15,23,42,.03);
    }

    .teacher-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .teacher-stat-icon.blue {
        color: #2563eb;
        background: #eff6ff;
    }

    .teacher-stat-icon.green {
        color: #16a34a;
        background: #f0fdf4;
    }

    .teacher-stat-icon.orange {
        color: #ea580c;
        background: #fff7ed;
    }

    .teacher-stat-icon.purple {
        color: #7c3aed;
        background: #f5f3ff;
    }

    .teacher-stat-content h3 {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: #0f172a;
    }

    .teacher-stat-content p {
        margin: 3px 0 0;
        font-size: 12px;
        color: #64748b;
    }


    /* Table card */

    .teacher-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(15,23,42,.035);
    }

    .teacher-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .teacher-card-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .teacher-search {
        position: relative;
        width: 270px;
    }

    .teacher-search i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .teacher-search input {
        width: 100%;
        height: 39px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 0 13px 0 37px;
        font-size: 12px;
        outline: none;
    }

    .teacher-search input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    }


    /* table */

    .teacher-table {
        margin: 0;
    }

    .teacher-table thead th {
        background: #f8fafc;
        padding: 13px 16px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        white-space: nowrap;
        border-color: #e2e8f0;
    }

    .teacher-table tbody td {
        padding: 15px 16px;
        font-size: 12px;
        vertical-align: middle;
        color: #334155;
        border-color: #f1f5f9;
    }

    .teacher-info {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .teacher-avatar {
        width: 40px;
        height: 40px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
    }

    .teacher-info strong {
        display: block;
        color: #0f172a;
        font-size: 13px;
    }

    .teacher-info small {
        color: #94a3b8;
    }

    .teacher-status {
        padding: 5px 9px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .teacher-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .teacher-status.inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    .teacher-status span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .teacher-action {
        width: 32px;
        height: 32px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        border-radius: 8px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 3px;
        transition: .2s;
    }

    .teacher-action:hover {
        color: #2563eb;
        background: #eff6ff;
        border-color: #bfdbfe;
    }


    /* Modal */

    #addTeacherModal .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(15,23,42,.2);
    }

    .teacher-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .teacher-modal-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .teacher-modal-title-icon {
        width: 43px;
        height: 43px;
        border-radius: 11px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px;
    }

    .teacher-modal-title h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .teacher-modal-title p {
        font-size: 11px;
        color: #64748b;
        margin: 3px 0 0;
    }

    .teacher-modal-body {
        padding: 24px;
        max-height: 70vh;
        overflow-y: auto;
        background: #f8fafc;
    }

    .teacher-form-section {
        padding: 19px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 13px;
        margin-bottom: 18px;
    }

    .teacher-form-section:last-child {
        margin-bottom: 0;
    }

    .teacher-form-title {
        display: flex;
        gap: 8px;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 11px;
        margin-bottom: 16px;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .teacher-form-title i {
        color: #2563eb;
    }

    .teacher-form-section .form-label {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }

    .teacher-form-section .form-select,
    .teacher-form-section .form-control {
        min-height: 42px;
        border-radius: 9px;
        border: 1px solid #dbe2ea;
        font-size: 12px;
    }

    .teacher-form-section .form-select:focus,
    .teacher-form-section .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    }

    .required {
        color: #ef4444;
    }

    .teacher-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .teacher-btn-cancel {
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        border-radius: 9px;
        padding: 9px 17px;
        font-size: 12px;
        font-weight: 600;
    }

    .teacher-btn-save {
        border: none;
        background: #2563eb;
        color: white;
        border-radius: 9px;
        padding: 9px 18px;
        font-size: 12px;
        font-weight: 600;
    }


    @media(max-width: 991px) {

        .teacher-stats {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media(max-width: 767px) {

        .teacher-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .teacher-stats {
            grid-template-columns: 1fr;
        }

        .teacher-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .teacher-search {
            width: 100%;
        }

    }

</style>


<div class="teacher-config-page">

    {{-- Header --}}
    <div class="teacher-page-header">

        <div>
            <h2>Teacher Configuration</h2>

            <p>
                Configure teachers based on their school location.
            </p>
        </div>


        <button
            type="button"
            class="btn btn-add-teacher"
            data-bs-toggle="modal"
            data-bs-target="#addTeacherModal"
        >
            <i class="fas fa-plus"></i>

            Add Teacher
        </button>

    </div>


    {{-- Statistics --}}
    <div class="teacher-stats">

        <div class="teacher-stat-card">

            <div class="teacher-stat-icon blue">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>

            <div class="teacher-stat-content">
                <h3>120</h3>
                <p>Total Teachers</p>
            </div>

        </div>


        <div class="teacher-stat-card">

            <div class="teacher-stat-icon green">
                <i class="fas fa-user-check"></i>
            </div>

            <div class="teacher-stat-content">
                <h3>108</h3>
                <p>Active Teachers</p>
            </div>

        </div>


        <div class="teacher-stat-card">

            <div class="teacher-stat-icon orange">
                <i class="fas fa-school"></i>
            </div>

            <div class="teacher-stat-content">
                <h3>15</h3>
                <p>Schools</p>
            </div>

        </div>


        <div class="teacher-stat-card">

            <div class="teacher-stat-icon purple">
                <i class="fas fa-location-dot"></i>
            </div>

            <div class="teacher-stat-content">
                <h3>8</h3>
                <p>Divisions</p>
            </div>

        </div>

    </div>



    {{-- Teacher List --}}
    <div class="teacher-card">

        <div class="teacher-card-header">

            <h5>
                <i class="fas fa-list me-2 text-primary"></i>
                Teacher List
            </h5>


            <div class="teacher-search">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    id="teacherSearch"
                    placeholder="Search teacher..."
                >

            </div>

        </div>


        <div class="table-responsive">

            <table class="table teacher-table" id="teacherTable">

                <thead>

                    <tr>
                        <th>Teacher</th>
                        <th>School</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Upazila</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    <tr>

                        <td>

                            <div class="teacher-info">

                                <div class="teacher-avatar">
                                    AH
                                </div>

                                <div>
                                    <strong>Abdul Hasan</strong>
                                    <small>ID: T-1001</small>
                                </div>

                            </div>

                        </td>


                        <td>
                            Astha Model School
                        </td>

                        <td>
                            Dhaka
                        </td>

                        <td>
                            Gazipur
                        </td>

                        <td>
                            Sreepur
                        </td>

                        <td>

                            <span class="teacher-status active">
                                <span></span>
                                Active
                            </span>

                        </td>

                        <td>

                            <button class="teacher-action">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button class="teacher-action">
                                <i class="fas fa-pen"></i>
                            </button>

                        </td>

                    </tr>



                    <tr>

                        <td>

                            <div class="teacher-info">

                                <div class="teacher-avatar">
                                    SR
                                </div>

                                <div>
                                    <strong>Sumaiya Rahman</strong>
                                    <small>ID: T-1002</small>
                                </div>

                            </div>

                        </td>


                        <td>
                            Sunrise School
                        </td>

                        <td>
                            Dhaka
                        </td>

                        <td>
                            Dhaka
                        </td>

                        <td>
                            Savar
                        </td>

                        <td>

                            <span class="teacher-status active">
                                <span></span>
                                Active
                            </span>

                        </td>

                        <td>

                            <button class="teacher-action">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button class="teacher-action">
                                <i class="fas fa-pen"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- Add Teacher Modal --}}

<div
    class="modal fade"
    id="addTeacherModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form id="teacherForm">

                {{-- Header --}}
                <div class="teacher-modal-header">

                    <div class="teacher-modal-title">

                        <div class="teacher-modal-title-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>

                        <div>
                            <h5>Add Teacher Configuration</h5>

                            <p>
                                Select location and school for teacher.
                            </p>
                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>



                {{-- Body --}}
                <div class="teacher-modal-body">

                    <div class="teacher-form-section">

                        <div class="teacher-form-title">
                            <i class="fas fa-location-dot"></i>
                            Location Information
                        </div>


                        <div class="row g-3">


                            {{-- Country --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Country
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="teacherCountry"
                                    class="form-select"
                                    required
                                >
                                    <option value="">
                                        Select Country
                                    </option>

                                    <option value="bangladesh">
                                        Bangladesh
                                    </option>
                                </select>

                            </div>



                            {{-- Division --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Division
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="teacherDivision"
                                    class="form-select"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Select Division
                                    </option>
                                </select>

                            </div>



                            {{-- District --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    District
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="teacherDistrict"
                                    class="form-select"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Select District
                                    </option>
                                </select>

                            </div>



                            {{-- Upazila --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Upazila
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="teacherUpazila"
                                    class="form-select"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Select Upazila
                                    </option>
                                </select>

                            </div>



                            {{-- School --}}
                            <div class="col-12">

                                <label class="form-label">
                                    School
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="teacherSchool"
                                    class="form-select"
                                    disabled
                                    required
                                >
                                    <option value="">
                                        Select School
                                    </option>
                                </select>

                            </div>

                        </div>

                    </div>



                    {{-- Teacher info --}}
                    <div class="teacher-form-section">

                        <div class="teacher-form-title">
                            <i class="fas fa-user"></i>
                            Teacher Information
                        </div>


                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Teacher Name
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter teacher name"
                                    required
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Teacher ID
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Example: T-1001"
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Designation
                                </label>

                                <select class="form-select">

                                    <option value="">
                                        Select Designation
                                    </option>

                                    <option>
                                        Assistant Teacher
                                    </option>

                                    <option>
                                        Senior Teacher
                                    </option>

                                    <option>
                                        Head Teacher
                                    </option>

                                    <option>
                                        Principal
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Status
                                </label>

                                <select class="form-select">

                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- Footer --}}
                <div class="teacher-modal-footer">

                    <button
                        type="button"
                        class="teacher-btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="teacher-btn-save"
                    >
                        <i class="fas fa-check me-1"></i>
                        Save Teacher
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<script>

    /*
    |--------------------------------------------------------------------------
    | Frontend Demo Location Data
    |--------------------------------------------------------------------------
    */

    const teacherLocationData = {

        dhaka: {

            districts: {

                dhaka: [
                    'Savar',
                    'Dhamrai',
                    'Dohar',
                    'Keraniganj',
                    'Nawabganj'
                ],

                gazipur: [
                    'Gazipur Sadar',
                    'Sreepur',
                    'Kaliakair',
                    'Kapasia',
                    'Kaliganj'
                ],

                narayanganj: [
                    'Narayanganj Sadar',
                    'Rupganj',
                    'Sonargaon',
                    'Bandar',
                    'Araihazar'
                ]

            }

        },


        chattogram: {

            districts: {

                chattogram: [
                    'Patiya',
                    'Hathazari',
                    'Anwara',
                    'Rangunia',
                    'Boalkhali'
                ],

                coxsbazar: [
                    'Coxs Bazar Sadar',
                    'Ramu',
                    'Ukhiya',
                    'Teknaf',
                    'Chakaria'
                ]

            }

        },


        rajshahi: {

            districts: {

                rajshahi: [
                    'Paba',
                    'Bagha',
                    'Bagmara',
                    'Charghat',
                    'Godagari'
                ],

                natore: [
                    'Natore Sadar',
                    'Lalpur',
                    'Baraigram',
                    'Bagatipara',
                    'Gurudaspur'
                ]

            }

        }

    };


    const teacherCountry =
        document.getElementById('teacherCountry');

    const teacherDivision =
        document.getElementById('teacherDivision');

    const teacherDistrict =
        document.getElementById('teacherDistrict');

    const teacherUpazila =
        document.getElementById('teacherUpazila');

    const teacherSchool =
        document.getElementById('teacherSchool');


    /*
    |--------------------------------------------------------------------------
    | Country
    |--------------------------------------------------------------------------
    */

    teacherCountry.addEventListener('change', function() {

        teacherDivision.innerHTML =
            '<option value="">Select Division</option>';

        teacherDistrict.innerHTML =
            '<option value="">Select District</option>';

        teacherUpazila.innerHTML =
            '<option value="">Select Upazila</option>';

        teacherSchool.innerHTML =
            '<option value="">Select School</option>';


        teacherDistrict.disabled = true;
        teacherUpazila.disabled = true;
        teacherSchool.disabled = true;


        if(this.value === 'bangladesh') {

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


            Object.entries(divisions).forEach(
                ([value, label]) => {

                    teacherDivision.innerHTML +=
                        `<option value="${value}">
                            ${label}
                        </option>`;

                }
            );


            teacherDivision.disabled = false;

        }

    });



    /*
    |--------------------------------------------------------------------------
    | Division
    |--------------------------------------------------------------------------
    */

    teacherDivision.addEventListener('change', function() {

        teacherDistrict.innerHTML =
            '<option value="">Select District</option>';

        teacherUpazila.innerHTML =
            '<option value="">Select Upazila</option>';

        teacherSchool.innerHTML =
            '<option value="">Select School</option>';


        teacherUpazila.disabled = true;
        teacherSchool.disabled = true;


        const data =
            teacherLocationData[this.value];


        if(!data) {

            teacherDistrict.disabled = true;

            return;

        }


        Object.keys(data.districts).forEach(function(key) {

            let label = key.charAt(0).toUpperCase()
                + key.slice(1);


            if(key === 'coxsbazar') {

                label = "Cox's Bazar";

            }


            teacherDistrict.innerHTML +=
                `<option value="${key}">
                    ${label}
                </option>`;

        });


        teacherDistrict.disabled = false;

    });



    /*
    |--------------------------------------------------------------------------
    | District
    |--------------------------------------------------------------------------
    */

    teacherDistrict.addEventListener('change', function() {

        teacherUpazila.innerHTML =
            '<option value="">Select Upazila</option>';

        teacherSchool.innerHTML =
            '<option value="">Select School</option>';


        teacherSchool.disabled = true;


        const upazilas =
            teacherLocationData[
                teacherDivision.value
            ]?.districts[this.value];


        if(!upazilas) {

            teacherUpazila.disabled = true;

            return;

        }


        upazilas.forEach(function(name) {

            teacherUpazila.innerHTML +=
                `<option value="${name}">
                    ${name}
                </option>`;

        });


        teacherUpazila.disabled = false;

    });



    /*
    |--------------------------------------------------------------------------
    | Upazila
    |--------------------------------------------------------------------------
    */

    teacherUpazila.addEventListener('change', function() {

        teacherSchool.innerHTML =
            '<option value="">Select School</option>';


        if(this.value) {

            teacherSchool.innerHTML += `

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


            teacherSchool.disabled = false;

        }

    });



    /*
    |--------------------------------------------------------------------------
    | Frontend Demo Submit
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('teacherForm')
        .addEventListener('submit', function(e) {

            e.preventDefault();


            Swal.fire({

                icon: 'success',

                title: 'UI Demo',

                text: 'Teacher configuration frontend is working properly.',

                confirmButtonText: 'OK'

            });

        });



    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('teacherSearch')
        .addEventListener('keyup', function() {

            const value =
                this.value.toLowerCase();


            const rows =
                document.querySelectorAll(
                    '#teacherTable tbody tr'
                );


            rows.forEach(function(row) {

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