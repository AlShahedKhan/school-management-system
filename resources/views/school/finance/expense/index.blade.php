@extends('layouts.school')
@section('content')
    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.finance.expense.partials.header')
            @include('school.finance.expense.partials.filter')
            @include('school.finance.expense.partials.table')
        </div>
    </div>
    @include('school.finance.expense.partials.expense-modal')
    @include('school.finance.expense.partials.js.modal-open')
    @include('school.finance.expense.partials.js.error-validation')
    @include('school.finance.expense.partials.js.modal-submit')
    @include('school.finance.expense.partials.js.search')
    @include('school.finance.expense.partials.js.delete')
    @include('school.finance.expense.partials.js.export')
    @include('school.finance.expense.partials.js.filter')
@endsection
