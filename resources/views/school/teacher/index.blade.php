@extends('layouts.school')

@section('title', 'Teacher Registration')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
@endpush

@section('content')
    <div class="teacher-registration-page">
        @include('school.teacher.partials.header')
        @include('school.teacher.partials.table')
    </div>

    @include('school.teacher.partials.teacher-modal')
    @include('school.teacher.partials.deactivate-modal')
    @include('school.teacher.partials.filter-modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @include('school.teacher.partials.js.teacher-js')
@endpush
