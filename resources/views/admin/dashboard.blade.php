@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-2xl md:text-3xl font-semibold text-gray-900">
        Dashboard
    </h2>
    <p class="text-sm text-gray-500 mt-1">
        System overview and administrative summary.
    </p>
</div>


<!-- ================= Statistics ================= -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

    <!-- Total Schools -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            Total Schools
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $totalSchools ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-school text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            Pending Approvals
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $pendingSchools ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-check-circle text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- SMS Packages -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <p class="text-sm text-gray-500">
            SMS Packages
        </p>
        <div class="flex items-end justify-between mt-4">
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ $smsPackages ?? 0 }}
            </h3>
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-sms text-gray-400 text-sm"></i>
            </div>
        </div>
    </div>

</div>



<!-- ================= Quick Actions ================= -->
<div class="mt-12">

    <h3 class="text-base font-semibold text-gray-800 mb-5">
        Quick Actions
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <a href="{{ url('/admin/approval-schools') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Review Approvals
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <a href="{{ url('/admin/registered-schools') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-school text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Manage Schools
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <a href="{{ url('/admin/create-plan') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-plus-circle text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    Create Plan
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

        <a href="{{ url('/admin/sms-packages') }}"
           class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-sms text-gray-400 text-xs"></i>
                </div>
                <span class="text-sm text-gray-700">
                    SMS Packages
                </span>
            </div>
            <i class="fas fa-arrow-right text-xs text-gray-300"></i>
        </a>

    </div>

</div>

@endsection
