@extends('layouts.school')
@section('title', 'Student Admission')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
.form-input {
width: 100%;
border: 1.5px solid #e5e7eb;
padding: 7px 14px;
font-size: 14px;
border-radius: 0;
margin-bottom: 12px;
transition: all 0.3s ease;
display: block;
color: #1f2937;
background: #ffffff;
font-weight: 400;
letter-spacing: 0.01em;
}
.form-input:focus {
outline: none;
border-color: #3b82f6;
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
transform: translateY(-1px);
}
.form-input:hover {
border-color: #cbd5e1;
}
.form-input[readonly] {
background: #f9fafb;
color: #6b7280;
cursor: not-allowed;
}
.form-input::placeholder {
color: #9ca3af;
font-weight: 400;
}
input[type="date"]::-webkit-calendar-picker-indicator {
opacity: 0.4;
}
.error-text {
font-size: 11px;
color: #dc2626;
margin-bottom: 5px;
display: none;
}
.custom-select-wrapper {
position: relative;
width: 100%;
margin-bottom: 12px;
}
.custom-select-display {
background: #fff;
border: 1.5px solid #e5e7eb;
height: 36px;
padding: 0 14px;
display: flex;
align-items: center;
justify-content: space-between;
cursor: pointer;
font-size: 14px;
color: #1f2937;
border-radius: 0;
transition: all 0.3s ease;
font-weight: 400;
letter-spacing: 0.01em;
}
.custom-select-display:hover {
border-color: #cbd5e1;
}
.custom-select-wrapper.active .custom-select-display {
border-color: #3b82f6;
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.custom-select-options {
position: absolute;
top: 44px;
left: 0;
width: 100%;
background: #ffffff;
border: 1.5px solid #e5e7eb;
border-radius: 0;
box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
z-index: 999;
display: none;
max-height: 200px;
overflow-y: auto;
}
.custom-select-options::-webkit-scrollbar {
width: 6px;
}
.custom-select-options::-webkit-scrollbar-track {
background: #f9fafb;
border-radius: 3px;
}
.custom-select-options::-webkit-scrollbar-thumb {
background: #d1d5db;
border-radius: 3px;
}
.custom-select-options::-webkit-scrollbar-thumb:hover {
background: #9ca3af;
}
.custom-option {
padding: 10px 14px;
font-size: 14px;
cursor: pointer;
color: #1f2937;
transition: all 0.2s ease;
font-weight: 400;
}
.custom-option:hover {
background: #eff6ff;
color: #2563eb;
}
.hidden-select {
position: absolute;
opacity: 0;
pointer-events: none;
height: 0;
width: 0;
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
.btn-group-right {
display: flex;
justify-content: space-between;
gap: 12px;
margin-top: 1.5rem;
}
.capitalize-text {
text-transform: capitalize;
}
@media (max-width: 640px) {
.btn-group-right {
justify-content: space-between;
gap: 8px;
}
.btn-group-right .btn-compact {
flex: 1;
justify-content: center;
}
.form-input {
font-size: 16px;
padding: 7px 12px;
}
.custom-select-display {
font-size: 16px;
height: 36px;
}
}
</style>
@include('school.partials.student-admission-modal')
@include('school.partials.quick-add-modals')
@include('school.partials.student-admission-js')
@include('school.partials.quick-add-js')
@endsection
