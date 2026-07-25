@extends('layouts.school')
@section('title', 'Bulk Student Upload')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .form-input-bulk {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            padding: 7px 14px;
            font-size: 13px;
            border-radius: 0;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            display: block;
            color: #1f2937;
            background: #ffffff;
            font-weight: 400;
        }

        .form-input-bulk:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        select.form-input-bulk {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
            padding-right: 40px;
        }

        .btn-compact {
            height: 42px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
        }

        .btn-compact::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-compact:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-compact:hover::before {
            opacity: 1;
        }

        .btn-compact:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-compact.bg-blue-600:hover {
            background: #2563eb;
        }

        .btn-compact.bg-green-600:hover {
            background: #16a34a;
        }

        .btn-compact.border:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
    </style>
    @include('school.partials.student-bulk-upload-modal')
    @include('school.partials.quick-add-modals')
    @include('school.partials.student-bulk-upload-js')
    @include('school.partials.quick-add-js')
@endsection
