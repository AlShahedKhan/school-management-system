@extends('layouts.admin')

@section('title', 'Teacher Configuration')

@section('page-title', 'Teacher Configuration')

@section('content')



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