@extends('layouts.admin')

@section('title', 'View Showcase')
@section('page-title', 'View Showcase')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div
            class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    View Showcase
                </h2>

                <p class="text-xs text-gray-500">
                    Review showcase details and page screenshot.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.showcases.edit', $showcase) }}"
                    class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                >
                    <i class="fas fa-pen mr-2"></i>

                    Edit Showcase
                </a>

                <a
                    href="{{ route('admin.showcases.index') }}"
                    class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600"
                >
                    <i class="fas fa-arrow-left mr-2"></i>

                    Back to Showcases
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-sm font-bold text-gray-800">
                        Page Screenshot
                    </h3>
                </div>

                <div class="mt-4">
                    @if ($showcase->image)
                        <div class="overflow-hidden border border-gray-200 bg-gray-50">
                            <img
                                src="{{ asset('storage/' . $showcase->image) }}"
                                alt="{{ $showcase->title_en ?? $showcase->title }}"
                                class="w-full object-cover"
                            >
                        </div>
                    @else
                        <div
                            class="flex min-h-[300px] items-center justify-center border border-dashed border-gray-200 bg-gray-50 text-sm font-medium text-gray-400">
                            No image uploaded
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">
                        Showcase Details
                    </h3>

                    <div class="mt-4 space-y-5 text-sm text-gray-700">
                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Title
                            </p>

                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $showcase->title_en ?? $showcase->title }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Bangla Title
                            </p>

                            <p class="mt-2 font-medium text-gray-900">
                                {{ $showcase->title_bn ?: 'No Bangla title' }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Created At
                            </p>

                            <p class="mt-2 font-medium text-gray-900">
                                {{ $showcase->created_at?->format('j-F-Y h:i A') }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Updated At
                            </p>

                            <p class="mt-2 font-medium text-gray-900">
                                {{ $showcase->updated_at?->format('j-F-Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border border-red-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">
                        Delete Showcase
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-gray-500">
                        Deleting this showcase will remove the record and its uploaded image.
                    </p>

                    <form
                        action="{{ route('admin.showcases.destroy', $showcase) }}"
                        method="POST"
                        class="mt-4"
                        onsubmit="return confirm('Delete this showcase?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center border border-red-100 bg-red-50 px-4 py-3 text-xs font-semibold text-red-700 transition hover:border-red-200 hover:bg-red-100 hover:text-red-800"
                        >
                            <i class="fas fa-trash-can mr-2"></i>

                            Delete Showcase
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
