@extends('layouts.school')
@section('title', 'Student Promotion')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .form-input-promote {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            padding: 7px 14px;
            font-size: 13px;
            border-radius: 0;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            display: block;
            color: #1f2937;
            background: #ffffff;
            font-weight: 400;
        }

        .form-input-promote:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        select.form-input-promote {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
            padding-right: 40px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }
    </style>
    @include('school.partials.student-promote-modal')
    @include('school.partials.quick-add-modals')
    @include('school.partials.student-promote-js')
    @include('school.partials.quick-add-js')
@endsection
