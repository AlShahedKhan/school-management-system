@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-2xl md:text-3xl font-semibold text-gray-900">
        Teacher Dashboard
    </h2>
    <p class="text-sm text-gray-500 mt-1">
        Overview of your teaching activities and quick access tools.
    </p>
</div>

<!-- ================= Statistics ================= -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

    <!-- Total Students -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            Total Students
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $students ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-user-graduate text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Assigned Classes -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            Assigned Classes
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $classes ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Total Assignments -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            Total Assignments
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $assignments ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-book text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

</div>

<!-- ================= Quick Actions ================= -->
<div class="mt-12">

    <h3 class="text-base font-semibold text-gray-800 mb-5">
        Quick Access
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Teacher List -->
        <a href="{{ route('teacher.teacher.list') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Teacher List
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <!-- Class Permission -->
        <a href="{{ route('teacher.class.permission') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-key text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Class Permission
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <!-- Assignments -->
        <a href="{{ route('teacher.assignment') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-book text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Assignments
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <!-- Student List -->
        <a href="{{ route('teacher.student.list') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-users text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Student List
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <!-- Class Time -->
        <a href="{{ route('teacher.class.time') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-clock text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Class Time
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

    </div>

</div>

@endsection
