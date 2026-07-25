@extends('layouts.school')
@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.finance.collection.partials.header')
            @include('school.finance.collection.partials.table')
        </div>
    </div>
    @include('school.finance.collection.partials.donate-modal')
    @include('school.finance.collection.partials.js.modal-open')
    @include('school.finance.collection.partials.js.error-validation')
    @include('school.finance.collection.partials.js.modal-submit')
@endsection
