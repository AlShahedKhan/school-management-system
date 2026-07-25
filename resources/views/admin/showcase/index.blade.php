@extends('layouts.admin')

@section('title', 'Page Showcases')
@section('page-title', 'Page Showcases')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div
            class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Page Showcases</h2>

                <p class="text-xs text-gray-500">
                    Manage page screenshots displayed in the website showcase section.
                </p>
            </div>

            <a
                href="{{ route('admin.showcases.create') }}"
                class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
            >
                <i class="fas fa-plus mr-2"></i>

                New Showcase
            </a>
        </div>

        @if (session('success'))
            <div
                class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Showcase
                            </th>

                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Image
                            </th>

                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($showcases as $showcase)
                            <tr class="align-middle transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-900">
                                        {{ $showcase->title_en ?? $showcase->title }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $showcase->title_bn ?: 'No Bangla title' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <div
                                        class="h-20 w-32 overflow-hidden border border-gray-200 bg-gray-50">
                                        @if ($showcase->image)
                                            <img
                                                src="{{ asset('storage/' . $showcase->image) }}"
                                                alt="{{ $showcase->title_en ?? $showcase->title }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <div
                                                class="flex h-full w-full items-center justify-center text-xs font-medium text-gray-400">
                                                No image
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="admin-action-group">
                                        <a
                                            href="{{ route('admin.showcases.show', $showcase) }}"
                                            class="admin-action-icon admin-action-icon--view"
                                            aria-label="View showcase"
                                            title="View showcase"
                                        >
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>

                                        <a
                                            href="{{ route('admin.showcases.edit', $showcase) }}"
                                            class="admin-action-icon admin-action-icon--edit"
                                            aria-label="Edit showcase"
                                            title="Edit showcase"
                                        >
                                            <i class="fas fa-pen" aria-hidden="true"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.showcases.destroy', $showcase) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this showcase?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="admin-action-icon admin-action-icon--delete"
                                                aria-label="Delete showcase"
                                                title="Delete showcase"
                                            >
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-12 text-center">
                                    <p class="text-sm font-semibold text-gray-700">
                                        No showcases created yet.
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Create your first showcase to display page previews.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
