@extends('layouts.school')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.hrm.employee.partials.header')
            @include('school.hrm.employee.partials.table')
        </div>
    </div>
    @include('school.hrm.employee.partials.employee-modal')
    @include('school.hrm.employee.partials.filter')
    @include('school.hrm.employee.partials.js.employee-js')
@endsection