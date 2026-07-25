@extends('layouts.admin')

@section('title', 'Create Showcase')
@section('page-title', 'Create Showcase')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div
            class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Create Showcase
                </h2>

                <p class="text-xs text-gray-500">
                    Add a new page screenshot to the website showcase section.
                </p>
            </div>

            <a
                href="{{ route('admin.showcases.index') }}"
                class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600"
            >
                <i class="fas fa-arrow-left mr-2"></i>

                Back to Showcases
            </a>
        </div>

        <form
            action="{{ route('admin.showcases.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @php($submitLabel = 'Create Showcase')

            @include('admin.showcase._form')
        </form>
    </div>
@endsection