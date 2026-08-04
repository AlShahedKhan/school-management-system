@extends('layouts.school')
@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.hrm.payroll.partials.header')
            @include('school.hrm.payroll.partials.table')
        </div>
    </div>
    @include('school.hrm.payroll.partials.payroll-modal')
    @include('school.hrm.payroll.partials.filter')
    @include('school.hrm.payroll.partials.js.modal-open')
    @include('school.hrm.payroll.partials.js.error-validation')
    @include('school.hrm.payroll.partials.js.modal-submit')
@endsection