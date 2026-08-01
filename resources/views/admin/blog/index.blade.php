@extends('layouts.admin')

@section('title', 'Blogs')
@section('page-title', 'Blogs')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Blogs</h2>
                <p class="text-xs text-gray-500">Manage multilingual blog content, publishing state, featured posts, and SEO metadata.</p>
            </div>
            <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> New Blog
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Blog</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Slug</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Featured</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Published</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($blogs as $blog)
                            @php
                                $english = $blog->translations->firstWhere('locale', 'en');
                                $bangla = $blog->translations->firstWhere('locale', 'bn');
                            @endphp
                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-start gap-4">
                                        <div class="h-16 w-20 shrink-0 overflow-hidden border border-gray-200 bg-gray-50">
                                            @if ($blog->image)
                                                <img src="{{ asset('storage/' . $blog->image) }}" alt="Blog image" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">{{ $english?->title ?? 'Untitled' }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ $bangla?->title ?? 'No Bangla title' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $blog->slug }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex border px-2.5 py-1 text-xs font-bold {{ $blog->status === 'published' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-amber-100 bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex border px-2.5 py-1 text-xs font-bold {{ $blog->is_featured ? 'border-blue-100 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                        {{ $blog->is_featured ? 'Featured' : 'Standard' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $blog->published_at?->format('j-F-Y') ?? 'Not published' }}</p>
                                    @if ($blog->published_at)
                                        <p class="mt-1 text-xs text-gray-400">{{ $blog->published_at->format('h:i A') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('admin.blogs.show', $blog) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center border border-slate-200 bg-slate-50 text-sm text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                                            aria-label="View blog"
                                            title="View blog"
                                        >
                                            <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                        </a>
                                        <a
                                            href="{{ route('admin.blogs.edit', $blog) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center border border-blue-100 bg-blue-50 text-sm text-blue-700 transition hover:border-blue-200 hover:bg-blue-100 hover:text-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                            aria-label="Edit blog"
                                            title="Edit blog"
                                        >
                                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" onsubmit="return confirm('Delete this blog?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center border border-red-100 bg-red-50 text-sm text-red-700 transition hover:border-red-200 hover:bg-red-100 hover:text-red-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                                aria-label="Delete blog"
                                                title="Delete blog"
                                            >
                                                <i class="fas fa-trash-can" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <p class="text-sm font-semibold text-gray-700">No blogs created yet.</p>
                                    <p class="mt-1 text-xs text-gray-400">Create your first blog to start managing public content.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $blogs->links() }}
        </div>
    </div>
@endsection
