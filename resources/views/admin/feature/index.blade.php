@extends('layouts.admin')

@section('title', 'Features')
@section('page-title', 'Features')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Features</h2>
                <p class="text-xs text-gray-500">Manage public feature cards, ordering, activation state, and bilingual copy.</p>
            </div>
            <a href="{{ route('admin.features.create') }}" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> New Feature
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[920px] w-full border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Feature</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Icon</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Order</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($features as $feature)
                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-900">{{ $feature->title_en }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $feature->title_bn }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-[8px] border border-blue-100 bg-blue-50 text-blue-600">
                                        <div class="h-5 w-5">
                                            @include('partials.public.home-icon', ['icon' => $feature->icon])
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $feature->sort_order }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex border px-2.5 py-1 text-xs font-bold {{ $feature->is_active ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                        {{ $feature->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('admin.features.edit', $feature) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center border border-blue-100 bg-blue-50 text-sm text-blue-700 transition hover:border-blue-200 hover:bg-blue-100 hover:text-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                            aria-label="Edit feature"
                                            title="Edit feature"
                                        >
                                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('admin.features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Delete this feature?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center border border-red-100 bg-red-50 text-sm text-red-700 transition hover:border-red-200 hover:bg-red-100 hover:text-red-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                                aria-label="Delete feature"
                                                title="Delete feature"
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
                                    <p class="text-sm font-semibold text-gray-700">No features created yet.</p>
                                    <p class="mt-1 text-xs text-gray-400">Create your first feature to power the home page and the public features page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $features->links() }}
        </div>
    </div>
@endsection
