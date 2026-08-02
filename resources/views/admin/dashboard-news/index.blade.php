@extends('layouts.admin')

@section('title', 'Dashboard News')
@section('page-title', 'Dashboard News')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Dashboard News</h2>
                <p class="text-xs text-gray-500">Manage the news message shown on every school dashboard.</p>
            </div>
            <a href="{{ route('admin.dashboard-news.create') }}" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> New News
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">News</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Publish Window</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Order</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($newsItems as $news)
                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-blue-600">{{ $news->label }}</p>
                                    <p class="mt-1 max-w-xl text-sm font-semibold text-gray-900">{{ $news->message }}</p>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-500">
                                    <p>Start: {{ $news->starts_at?->format('j-F-Y, h:i A') ?? 'Any time' }}</p>
                                    <p class="mt-1">End: {{ $news->ends_at?->format('j-F-Y, h:i A') ?? 'No end date' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $news->sort_order }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex border px-2.5 py-1 text-xs font-bold {{ $news->is_active ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                        {{ $news->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('admin.dashboard-news.edit', $news) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center border border-blue-100 bg-blue-50 text-sm text-blue-700 transition hover:border-blue-200 hover:bg-blue-100 hover:text-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                            aria-label="Edit dashboard news"
                                            title="Edit dashboard news"
                                        >
                                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('admin.dashboard-news.destroy', $news) }}" method="POST" onsubmit="return confirm('Delete this dashboard news?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center border border-red-100 bg-red-50 text-sm text-red-700 transition hover:border-red-200 hover:bg-red-100 hover:text-red-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                                aria-label="Delete dashboard news"
                                                title="Delete dashboard news"
                                            >
                                                <i class="fas fa-trash-can" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <p class="text-sm font-semibold text-gray-700">No dashboard news created yet.</p>
                                    <p class="mt-1 text-xs text-gray-400">Create one to show a message on school dashboards.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $newsItems->links() }}
        </div>
    </div>
@endsection
