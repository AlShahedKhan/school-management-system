@extends('layouts.school')
@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.hrm.employee.partials.header')
            @include('school.hrm.employee.partials.table')
        </div>
    </div>
    @include('school.hrm.employee.partials.employee-modal')
    @include('school.hrm.employee.partials.js.modal-open')
    @include('school.hrm.employee.partials.js.error-validation')
    @include('school.hrm.employee.partials.js.modal-submit')
@endsection