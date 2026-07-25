@extends('layouts.school')
@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.finance.donate.partials.header')
            @include('school.finance.donate.partials.table')
        </div>
    </div>
    @include('school.finance.donate.partials.donate-modal')
    @include('school.finance.donate.partials.js.modal-open')
    @include('school.finance.donate.partials.js.error-validation')
    @include('school.finance.donate.partials.js.modal-submit')
@endsection
