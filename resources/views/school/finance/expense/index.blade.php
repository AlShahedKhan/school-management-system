@extends('layouts.school')

@section('title', 'Expense Management')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
@endpush

@section('content')
    <div class="expense-management-page">
        @include('school.finance.expense.partials.header')
        @include('school.finance.expense.partials.table')
    </div>

    @include('school.finance.expense.partials.expense-modal')
    @include('school.finance.expense.partials.filter')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @include('school.finance.expense.partials.js.expense-js')
@endpush
