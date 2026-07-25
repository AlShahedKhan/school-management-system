@extends('layouts.admin')

@section('title', 'About Settings')
@section('page-title', 'About Settings')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">About Settings</h2>
                <p class="text-xs text-gray-500">Manage the public About page intro, mission, and vision in English and Bangla.</p>
            </div>
            <a href="{{ route('admin.about.people.index') }}" class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                <i class="fas fa-users mr-2"></i> Manage People
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.about.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">About Page Intro</h3>
                <div class="mt-5 grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">English</h4>
                        <div>
                            <label for="page_eyebrow_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Eyebrow</label>
                            <input id="page_eyebrow_en" name="page_eyebrow_en" type="text" value="{{ old('page_eyebrow_en', $payload['page_eyebrow_en']) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('page_eyebrow_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="page_title_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                            <input id="page_title_en" name="page_title_en" type="text" value="{{ old('page_title_en', $payload['page_title_en']) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('page_title_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="page_intro_en" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Intro</label>
                            <textarea id="page_intro_en" name="page_intro_en" rows="5" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('page_intro_en', $payload['page_intro_en']) }}</textarea>
                            @error('page_intro_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">Bangla</h4>
                        <div>
                            <label for="page_eyebrow_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Eyebrow</label>
                            <input id="page_eyebrow_bn" name="page_eyebrow_bn" type="text" value="{{ old('page_eyebrow_bn', $payload['page_eyebrow_bn']) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('page_eyebrow_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="page_title_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                            <input id="page_title_bn" name="page_title_bn" type="text" value="{{ old('page_title_bn', $payload['page_title_bn']) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                            @error('page_title_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="page_intro_bn" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Intro</label>
                            <textarea id="page_intro_bn" name="page_intro_bn" rows="5" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old('page_intro_bn', $payload['page_intro_bn']) }}</textarea>
                            @error('page_intro_bn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            @foreach (['mission' => 'Mission', 'vision' => 'Vision'] as $prefix => $label)
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">{{ $label }}</h3>
                    <div class="mt-5 grid gap-6 lg:grid-cols-2">
                        @foreach (['en' => 'English', 'bn' => 'Bangla'] as $locale => $localeLabel)
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">{{ $localeLabel }}</h4>
                                <div>
                                    <label for="{{ $prefix }}_title_{{ $locale }}" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</label>
                                    <input id="{{ $prefix }}_title_{{ $locale }}" name="{{ $prefix }}_title_{{ $locale }}" type="text" value="{{ old($prefix . '_title_' . $locale, $payload[$prefix . '_title_' . $locale]) }}" class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                                    @error($prefix . '_title_' . $locale) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="{{ $prefix }}_summary_{{ $locale }}" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Summary</label>
                                    <textarea id="{{ $prefix }}_summary_{{ $locale }}" name="{{ $prefix }}_summary_{{ $locale }}" rows="4" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old($prefix . '_summary_' . $locale, $payload[$prefix . '_summary_' . $locale]) }}</textarea>
                                    @error($prefix . '_summary_' . $locale) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="{{ $prefix }}_details_{{ $locale }}" class="mb-1 block text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Details</label>
                                    <textarea id="{{ $prefix }}_details_{{ $locale }}" name="{{ $prefix }}_details_{{ $locale }}" rows="8" class="w-full border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-blue-500">{{ old($prefix . '_details_' . $locale, $payload[$prefix . '_details_' . $locale]) }}</textarea>
                                    @error($prefix . '_details_' . $locale) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <button type="submit" class="inline-flex h-11 items-center justify-center bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Save About Settings
                </button>
            </div>
        </form>
    </div>
@endsection
