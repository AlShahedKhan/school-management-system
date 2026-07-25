@extends('layouts.school')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        #teacherTableBody .teacher-row:hover > .teacher-cell {
            background-color: #fed7aa !important;
        }
    </style>
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.partials.teacher-table-header')
            @include('school.partials.filter-modal')
            @include('school.partials.teacher-table')
        </div>
    </div>
    @include('school.partials.teacher-register-modal')
    @include('school.partials.teacher-deactivate-modal')
    @include('school.partials.teacher-register-js')
@endsection
