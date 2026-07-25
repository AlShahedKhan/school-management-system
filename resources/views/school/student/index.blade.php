@extends('layouts.school')

@section('title', 'Student Directory')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.student.partials.header')
            @include('school.student.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @include('school.student.partials.js.student-js')
@endpush
