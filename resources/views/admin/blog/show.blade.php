@extends('layouts.admin')

@section('title', 'View Blog')
@section('page-title', 'View Blog')

@php
    $english = $blog->translations->firstWhere('locale', 'en');
    $bangla = $blog->translations->firstWhere('locale', 'bn');
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">View Blog</h2>
                <p class="text-xs text-gray-500">Review blog content, metadata, publishing state, and translations.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-pen mr-2"></i> Edit Blog
                </a>
                <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Blogs
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="space-y-6">
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">English Content</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $english?->title ?? 'No title' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</p>
                            <div class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $english?->description ?? 'No description' }}</div>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">Bangla Content</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Title</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $bangla?->title ?? 'No title' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Description</p>
                            <div class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $bangla?->description ?? 'No description' }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="border border-gray-100 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-800">English SEO</h3>
                        <div class="mt-4 space-y-4 text-sm text-gray-700">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">SEO Tags</p>
                                <p class="mt-2 whitespace-pre-line">{{ $english?->seo_tags ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Title</p>
                                <p class="mt-2">{{ $english?->meta_title ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Keywords</p>
                                <p class="mt-2 whitespace-pre-line">{{ $english?->meta_keywords ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Description</p>
                                <p class="mt-2 whitespace-pre-line">{{ $english?->meta_description ?? 'Not set' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-100 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-800">Bangla SEO</h3>
                        <div class="mt-4 space-y-4 text-sm text-gray-700">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">SEO Tags</p>
                                <p class="mt-2 whitespace-pre-line">{{ $bangla?->seo_tags ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Title</p>
                                <p class="mt-2">{{ $bangla?->meta_title ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Keywords</p>
                                <p class="mt-2 whitespace-pre-line">{{ $bangla?->meta_keywords ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Meta Description</p>
                                <p class="mt-2 whitespace-pre-line">{{ $bangla?->meta_description ?? 'Not set' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">Publish Details</h3>
                    <div class="mt-4 space-y-4 text-sm text-gray-700">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Slug</p>
                            <p class="mt-2 break-all font-medium text-gray-900">{{ $blog->slug }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</p>
                            <span class="mt-2 inline-flex border px-2.5 py-1 text-xs font-bold {{ $blog->status === 'published' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-amber-100 bg-amber-50 text-amber-700' }}">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Featured</p>
                            <span class="mt-2 inline-flex border px-2.5 py-1 text-xs font-bold {{ $blog->is_featured ? 'border-blue-100 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                {{ $blog->is_featured ? 'Featured' : 'Standard' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Published At</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $blog->published_at?->format('j-F-Y h:i A') ?? 'Not published' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Created At</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $blog->created_at?->format('j-F-Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Updated At</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $blog->updated_at?->format('j-F-Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800">Cover Image</h3>
                    <div class="mt-4">
                        @if ($blog->image)
                            <img
                                src="{{ asset('storage/' . $blog->image) }}"
                                alt="Blog image preview"
                                class="w-full border border-gray-200 object-cover"
                            >
                        @else
                            <div class="flex min-h-[180px] items-center justify-center border border-dashed border-gray-200 bg-gray-50 text-sm font-medium text-gray-400">
                                No image uploaded
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
